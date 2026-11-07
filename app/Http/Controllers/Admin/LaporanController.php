<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Ambil data laporan berdasarkan bulan yang dipilih.
     */
    private function getLaporanData(string $bulan): array
    {
        [$tahun, $bln] = explode('-', $bulan);

        $data = User::where('role', 'relawan')
            ->where('is_active', true)
            ->with(['attendances' => function ($q) use ($tahun, $bln) {
                $q->whereYear('date', $tahun)->whereMonth('date', $bln);
            }, 'division'])
            ->orderBy('name')
            ->get();

        return [$data, $tahun, $bln];
    }

    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        [$data] = $this->getLaporanData($bulan);

        return view('admin.laporan.index', compact('data', 'bulan'));
    }

    /**
     * Export laporan kehadiran ke PDF (F-30 PRD 2.0)
     */
    public function exportPdf(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        [$data, $tahun, $bln] = $this->getLaporanData($bulan);

        $bulanLabel = Carbon::createFromDate($tahun, $bln, 1)->translatedFormat('F Y');

        $pdf = Pdf::loadView('admin.laporan.export_pdf', compact('data', 'bulanLabel'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('Laporan_Kehadiran_' . $tahun . '_' . str_pad($bln, 2, '0', STR_PAD_LEFT) . '.pdf');
    }

    /**
     * Export laporan kehadiran ke Excel/CSV (F-30 PRD 2.0)
     */
    public function exportExcel(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        [$data, $tahun, $bln] = $this->getLaporanData($bulan);

        $bulanLabel = Carbon::createFromDate($tahun, $bln, 1)->translatedFormat('F Y');
        $filename   = 'Laporan_Kehadiran_' . $tahun . '_' . str_pad($bln, 2, '0', STR_PAD_LEFT) . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($data, $bulanLabel) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 agar Excel bisa baca karakter Indonesia
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, ['LAPORAN REKAP KEHADIRAN RELAWAN SPPG CURUG CUKANGGALIH 02']);
            fputcsv($handle, ['Periode', $bulanLabel]);
            fputcsv($handle, ['Dicetak', now()->format('d/m/Y H:i')]);
            fputcsv($handle, []);

            fputcsv($handle, ['No', 'Nama Relawan', 'NIK', 'Divisi', 'Hari Hadir', 'Total Jam Kerja', 'Rata-rata Jam/Hari']);

            $no = 1;
            foreach ($data as $relawan) {
                $totalHari = $relawan->attendances->whereNotNull('check_in_at')->count();
                $totalJam  = (float) $relawan->attendances->sum('total_hours');
                $rataRata  = $totalHari > 0 ? round($totalJam / $totalHari, 2) : 0;

                fputcsv($handle, [
                    $no++,
                    $relawan->name,
                    $relawan->nik ?? '-',
                    $relawan->division?->name ?? 'Umum',
                    $totalHari,
                    number_format($totalJam, 2),
                    number_format($rataRata, 2),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
