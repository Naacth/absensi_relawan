<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Division;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiManualController extends Controller
{
    /**
     * Halaman absensi manual — tampilkan semua relawan untuk tanggal tertentu
     */
    public function index(Request $request)
    {
        $tanggal   = $request->get('tanggal', today()->toDateString());
        $divisiId  = $request->get('divisi_id');

        $query = User::where('role', 'relawan')
            ->where('is_active', true)
            ->with([
                'division',
                'attendances' => fn($q) => $q->whereDate('date', $tanggal),
            ])
            ->orderBy('name');

        if ($divisiId) {
            $query->where('division_id', $divisiId);
        }

        $relawanList = $query->get()->map(function ($r) use ($tanggal) {
            $r->absensi_hari_ini = $r->attendances->first();
            return $r;
        });

        $divisions = Division::orderBy('name')->get();

        return view('admin.absensi_manual.index', compact('relawanList', 'tanggal', 'divisiId', 'divisions'));
    }

    /**
     * Simpan absensi manual — bisa hadir atau hapus kehadiran (toggle)
     * Menerima array user_id yang dicentang = hadir
     */
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'      => 'required|date',
            'manual_note'  => 'nullable|string|max:255',
            'hadir'        => 'nullable|array',
            'hadir.*'      => 'exists:users,id',
            'jam_masuk'    => 'nullable|date_format:H:i',
            'jam_pulang'   => 'nullable|date_format:H:i',
        ]);

        $tanggal    = Carbon::parse($request->tanggal)->toDateString();
        $hadirIds   = $request->input('hadir', []);
        $jamMasuk   = $request->jam_masuk   ? Carbon::parse($tanggal . ' ' . $request->jam_masuk)   : Carbon::parse($tanggal . ' 07:00:00');
        $jamPulang  = $request->jam_pulang  ? Carbon::parse($tanggal . ' ' . $request->jam_pulang)  : Carbon::parse($tanggal . ' 15:00:00');
        $note       = $request->manual_note ?: 'Input manual oleh admin';
        $adminId    = Auth::id();

        // Hitung total jam dari jam masuk & pulang
        $totalHours = round($jamMasuk->diffInMinutes($jamPulang) / 60, 2);

        // Ambil semua relawan aktif yang ditampilkan
        $divisiId = $request->input('divisi_id');
        $query    = User::where('role', 'relawan')->where('is_active', true);
        if ($divisiId) $query->where('division_id', $divisiId);
        $semuaRelawan = $query->pluck('id');

        $updated = 0;
        $removed = 0;

        foreach ($semuaRelawan as $userId) {
            if (in_array($userId, $hadirIds)) {
                // ── HADIR: buat atau update record absensi ──
                Attendance::updateOrCreate(
                    ['user_id' => $userId, 'date' => $tanggal],
                    [
                        'check_in_at'   => $jamMasuk,
                        'check_out_at'  => $jamPulang,
                        'total_hours'   => $totalHours,
                        'is_manual'     => true,
                        'manual_by'     => $adminId,
                        'manual_note'   => $note,
                        // Kosongkan field GPS/wajah untuk input manual
                        'check_in_score'    => null,
                        'check_in_liveness' => null,
                        'check_in_lat'      => null,
                        'check_in_lng'      => null,
                        'distance_m'        => null,
                    ]
                );
                $updated++;
            } else {
                // ── TIDAK HADIR: hapus record manual jika ada (jangan hapus yang asli dari relawan) ──
                $existing = Attendance::where('user_id', $userId)
                    ->whereDate('date', $tanggal)
                    ->first();

                if ($existing && $existing->is_manual) {
                    $existing->delete();
                    $removed++;
                }
                // Jika absensi asli (bukan manual), biarkan — tidak dihapus
            }
        }

        return redirect()
            ->route('admin.absensi-manual.index', ['tanggal' => $tanggal, 'divisi_id' => $divisiId])
            ->with('success', "Absensi manual berhasil disimpan. {$updated} relawan hadir, {$removed} dihapus.");
    }

    /**
     * Hapus 1 record absensi manual tertentu
     */
    public function destroy(Attendance $attendance)
    {
        if (!$attendance->is_manual) {
            return redirect()->back()->with('error', 'Hanya absensi manual yang dapat dihapus lewat fitur ini.');
        }

        $tanggal = $attendance->date->toDateString();
        $attendance->delete();

        return redirect()->back()->with('success', 'Record absensi manual berhasil dihapus.');
    }
}
