<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\LeaveRequest;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\SalaryScheme;
use App\Models\User;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with(['creator', 'approver'])
            ->withCount('details')
            ->orderByDesc('period_start')
            ->paginate(10);

        return view('admin.payroll.index', compact('payrolls'));
    }

    public function create()
    {
        $divisions = Division::where('daily_rate', '>', 0)->orderBy('name')->get();
        return view('admin.payroll.create', compact('divisions'));
    }

    /**
     * Halaman pengaturan skema honor (transport, makan, lembur) — dapat diakses Admin
     */
    public function skemaIndex()
    {
        $scheme    = SalaryScheme::where('is_active', true)->first() ?? new SalaryScheme();
        $divisions = Division::orderBy('name')->get();
        return view('admin.payroll.skema', compact('scheme', 'divisions'));
    }

    public function skemaUpdate(Request $request)
    {
        $request->validate([
            'daily_transport'      => 'required|numeric|min:0',
            'meal_allowance'       => 'required|numeric|min:0',
            'overtime_hourly_rate' => 'required|numeric|min:0',
            'late_deduction'       => 'required|numeric|min:0',
            // Daily rate per divisi
            'division_rates'       => 'nullable|array',
            'division_rates.*'     => 'nullable|numeric|min:0',
        ]);

        // Update skema tunjangan global (sebagai fallback divisi yang belum diatur)
        $scheme = SalaryScheme::where('is_active', true)->first() ?? new SalaryScheme(['name' => 'Skema Standar Relawan SPPG', 'is_active' => true, 'hourly_rate' => 0]);
        $scheme->fill([
            'daily_transport'      => $request->daily_transport,
            'meal_allowance'       => $request->meal_allowance,
            'overtime_hourly_rate' => $request->overtime_hourly_rate,
            'late_deduction'       => $request->late_deduction,
        ])->save();

        // Update daily_rate + allowances per divisi
        if ($request->has('division_rates')) {
            foreach ($request->division_rates as $divId => $rate) {
                Division::where('id', $divId)->update(['daily_rate' => (float)($rate ?? 0)]);
            }
        }

        return redirect()->back()->with('success', 'Skema honor & tarif divisi berhasil diperbarui.');
    }
    public function generate(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'title'        => 'nullable|string|max:150',
            'notes'        => 'nullable|string',
        ]);

        $periodStart = Carbon::parse($request->period_start)->startOfDay();
        $periodEnd   = Carbon::parse($request->period_end)->endOfDay();
        $title       = $request->title ?: 'Payroll Periode ' . $periodStart->translatedFormat('d F Y') . ' s/d ' . $periodEnd->translatedFormat('d F Y');

        // Skema global sebagai fallback jika divisi belum punya daily_rate
        $scheme = SalaryScheme::where('is_active', true)->first() ?? new SalaryScheme([
            'hourly_rate'          => 0,
            'daily_transport'      => 0,
            'meal_allowance'       => 0,
            'overtime_hourly_rate' => 0,
            'late_deduction'       => 0,
        ]);

        $relawanList = User::where('role', 'relawan')
            ->where('is_active', true)
            ->with('division')
            ->get();

        $payroll = Payroll::create([
            'title'           => $title,
            'period_start'    => $periodStart->toDateString(),
            'period_end'      => $periodEnd->toDateString(),
            'status'          => 'draft',
            'total_disbursed' => 0,
            'created_by'      => Auth::id(),
            'notes'           => $request->notes,
        ]);

        $totalAllDisbursed = 0;

        foreach ($relawanList as $relawan) {
            // Ambil absensi sah (ada check_in dan check_out) dalam rentang periode
            $attendances = Attendance::where('user_id', $relawan->id)
                ->whereBetween('date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                ->whereNotNull('check_in_at')
                ->whereNotNull('check_out_at')
                ->get();

            $totalDaysAttended = $attendances->count();
            $totalHours        = (float) $attendances->sum('total_hours');

            // ── Honor Pokok: pakai daily_rate divisi, fallback ke hourly_rate global ──
            $divisionDailyRate = (float) ($relawan->division?->daily_rate ?? 0);
            if ($divisionDailyRate > 0) {
                // Honor = hari hadir × tarif harian divisi
                $baseSalary = $totalDaysAttended * $divisionDailyRate;
            } else {
                // Fallback: jam kerja × tarif per jam (skema global)
                $baseSalary = $totalHours * (float) $scheme->hourly_rate;
            }

            // ── Uang Transport (dari divisi, fallback skema global) ──
            $transportAllowance = $totalDaysAttended * (float)($relawan->division?->daily_transport ?? $scheme->daily_transport);

            // ── Uang Makan: hari dengan jam kerja ≥ 6 jam ──
            $mealDays      = $attendances->filter(fn($a) => (float)$a->total_hours >= 6)->count();
            $mealAllowance = $mealDays * (float)($relawan->division?->meal_allowance ?? $scheme->meal_allowance);

            // ── Lembur: jam > 8 per hari ──
            $overtimeHours = 0;
            foreach ($attendances as $a) {
                if ((float)$a->total_hours > 8) {
                    $overtimeHours += ((float)$a->total_hours - 8);
                }
            }
            $overtimePay = $overtimeHours * (float)($relawan->division?->overtime_hourly_rate ?? $scheme->overtime_hourly_rate);

            // ── Hari Izin Resmi ──
            $totalDaysLeave = LeaveRequest::where('user_id', $relawan->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($periodStart, $periodEnd) {
                    $q->whereBetween('start_date', [$periodStart->toDateString(), $periodEnd->toDateString()])
                      ->orWhereBetween('end_date', [$periodStart->toDateString(), $periodEnd->toDateString()]);
                })
                ->count();

            $deductions = 0;
            $netSalary  = $baseSalary + $transportAllowance + $mealAllowance + $overtimePay - $deductions;

            $totalAllDisbursed += $netSalary;

            PayrollDetail::create([
                'payroll_id'          => $payroll->id,
                'user_id'             => $relawan->id,
                'total_days_attended' => $totalDaysAttended,
                'total_days_leave'    => $totalDaysLeave,
                'total_hours'         => $totalHours,
                'base_salary'         => $baseSalary,
                'transport_allowance' => $transportAllowance,
                'meal_allowance'      => $mealAllowance,
                'overtime_pay'        => $overtimePay,
                'deductions'          => $deductions,
                'net_salary'          => $netSalary,
                'payment_status'      => 'unpaid',
            ]);
        }

        $payroll->update(['total_disbursed' => $totalAllDisbursed]);

        return redirect()->route('admin.payroll.show', $payroll)
            ->with('success', 'Payroll berhasil dikalkulasi otomatis dalam status Draft.');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['details.user.division', 'creator', 'approver']);
        return view('admin.payroll.show', compact('payroll'));
    }

    public function approve(Payroll $payroll)
    {
        $payroll->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        // Kirim Notifikasi WA ke masing-masing relawan (F-24 PRD 2.0)
        // Eager load user agar tidak N+1 query
        $payroll->load('details.user');
        foreach ($payroll->details as $detail) {
            $user = $detail->user;
            $periodStr = Carbon::parse($payroll->period_start)->format('d/m/Y') . ' - ' . Carbon::parse($payroll->period_end)->format('d/m/Y');
            $slipUrl = route('relawan.payroll.slip', $detail->id);

            WhatsAppService::notifyPayrollApproved($user, (float)$detail->net_salary, $periodStr, $slipUrl);
        }

        return redirect()->back()->with('success', 'Payroll telah disetujui dan notifikasi WhatsApp slip honor telah dikirimkan ke relawan.');
    }

    public function markAsPaid(Payroll $payroll)
    {
        $payroll->update(['status' => 'paid']);
        $payroll->details()->update([
            'payment_status' => 'paid',
            'paid_at'        => now(),
        ]);

        return redirect()->back()->with('success', 'Status seluruh pembayaran payroll telah ditandai LUNAS (PAID).');
    }

    public function exportCsv(Payroll $payroll)
    {
        $payroll->load(['details.user.division']);

        $filename = 'rekap_payroll_' . Str::slug($payroll->title) . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($payroll) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 untuk Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['REKAPITULASI HONORARIUM RELAWAN SPPG CURUG CUKANGGALIH 02']);
            fputcsv($handle, ['Judul', $payroll->title]);
            fputcsv($handle, ['Periode', $payroll->period_start . ' s/d ' . $payroll->period_end]);
            fputcsv($handle, ['Total Pencairan', 'Rp ' . number_format($payroll->total_disbursed, 0, ',', '.')]);
            fputcsv($handle, []);

            fputcsv($handle, [
                'No', 'Nama Relawan', 'NIK', 'Divisi', 'No HP',
                'Hari Hadir', 'Hari Izin', 'Total Jam',
                'Honor Jam (Rp)', 'Transport (Rp)', 'Uang Makan (Rp)', 'Lembur (Rp)',
                'Potongan (Rp)', 'Total Bersih (Rp)', 'Status Bayar'
            ]);

            $no = 1;
            foreach ($payroll->details as $d) {
                fputcsv($handle, [
                    $no++,
                    $d->user->name,
                    $d->user->nik ?? '-',
                    $d->user->division?->name ?? 'Umum',
                    $d->user->phone ?? '-',
                    $d->total_days_attended,
                    $d->total_days_leave,
                    $d->total_hours,
                    $d->base_salary,
                    $d->transport_allowance,
                    $d->meal_allowance,
                    $d->overtime_pay,
                    $d->deductions,
                    $d->net_salary,
                    $d->payment_status === 'paid' ? 'LUNAS' : 'PENDING',
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function slipPdf(PayrollDetail $detail)
    {
        $detail->load(['payroll', 'user.division']);

        $pdf = Pdf::loadView('admin.payroll.slip_pdf', compact('detail'))
            ->setPaper('a5', 'landscape');

        $filename = 'Slip_Honor_' . Str::slug($detail->user->name) . '_' . $detail->payroll->period_start . '.pdf';
        return $pdf->download($filename);
    }
}

