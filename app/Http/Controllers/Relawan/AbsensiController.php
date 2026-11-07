<?php

namespace App\Http\Controllers\Relawan;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Services\FaceRecognitionService;
use App\Services\GeofenceService;
use App\Services\WatermarkService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AbsensiController extends Controller
{
    protected FaceRecognitionService $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->faceService = $faceService;
    }

    public function index()
    {
        $user         = Auth::user();
        $absenHariIni = $user->todayAttendance();
        $schedule     = $user->division ? $user->division->todaySchedule() : null;

        return view('relawan.absensi.index', compact('user', 'absenHariIni', 'schedule'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type'             => 'required|in:in,out',
            'photo'            => 'required|string', // base64
            'lat'              => 'required|numeric|between:-90,90',
            'lng'              => 'required|numeric|between:-180,180',
            'client_timestamp' => 'nullable|string',
        ]);

        $user      = Auth::user();
        $type      = $request->type;
        $serverNow = now();
        $lat       = (float) $request->lat;
        $lng       = (float) $request->lng;

        // 1. Validasi Wajah Terdaftar (Enrollment)
        if (!$user->face_enrolled) {
            return $this->logAndFail($user, $type, $request, 'Wajah belum terdaftar. Silakan lakukan pendaftaran wajah terlebih dahulu.');
        }

        // 2. Validasi Geofencing (Haversine Formula) - F-09 PRD 2.0
        $geoCheck = GeofenceService::checkWithinRadius($lat, $lng);
        $distance = $geoCheck['distance_m'];

        if (!$geoCheck['is_within']) {
            $allowed = $geoCheck['allowed_radius_m'];
            $msg = "Anda berada di luar radius absensi SPPG! Jarak Anda: {$distance}m (Maksimal: {$allowed}m).";
            return $this->logAndFail($user, $type, $request, $msg, 'out_of_radius');
        }

        // 3. Validasi Jadwal Divisi - F-11 PRD 2.0
        $division = $user->division;
        $schedule = $division ? $division->todaySchedule() : null;
        $jamSekarang = $serverNow->format('H:i:s');

        if (!$division) {
            return $this->logAndFail($user, $type, $request,
                'Anda belum ditempatkan ke divisi apapun. Hubungi Admin.',
                'no_division'
            );
        }

        // Cek validasi jadwal (bisa dimatikan via SCHEDULE_CHECK=false di .env untuk development/demo)
        $scheduleCheckEnabled = env('SCHEDULE_CHECK', true);

        if ($scheduleCheckEnabled && $schedule) {
            if ($type === 'in') {
                if ($jamSekarang < $schedule->check_in_start || $jamSekarang > $schedule->check_in_end) {
                    $start = substr($schedule->check_in_start, 0, 5);
                    $end   = substr($schedule->check_in_end, 0, 5);
                    return $this->logAndFail($user, $type, $request,
                        "Absen masuk divisi {$division->name} hanya dibuka pukul {$start} - {$end} WIB.",
                        'outside_schedule'
                    );
                }
            } else {
                if ($jamSekarang < $schedule->check_out_start) {
                    $start = substr($schedule->check_out_start, 0, 5);
                    return $this->logAndFail($user, $type, $request,
                        "Absen pulang divisi {$division->name} baru dapat dilakukan setelah pukul {$start} WIB.",
                        'outside_schedule'
                    );
                }
            }
        }

        // 4. Cek Duplikasi Absensi
        $attendance = Attendance::firstOrNew(['user_id' => $user->id, 'date' => $serverNow->toDateString()]);

        if ($type === 'in' && $attendance->check_in_at) {
            return $this->logAndFail($user, $type, $request, 'Anda sudah melakukan absen masuk hari ini.');
        }
        if ($type === 'out' && $attendance->check_out_at) {
            return $this->logAndFail($user, $type, $request, 'Anda sudah melakukan absen pulang hari ini.');
        }
        if ($type === 'out' && !$attendance->check_in_at) {
            return $this->logAndFail($user, $type, $request, 'Anda belum melakukan absen masuk hari ini.');
        }

        // 5. Verifikasi Wajah & Liveness (ARSA API / Dummy) - F-10 PRD 2.0
        $faceCheck = $this->faceService->verifyFace($user, $request->photo);

        if (!$faceCheck['success']) {
            $reason = !$faceCheck['liveness'] ? 'liveness_failed' : 'face_mismatch';
            $msg    = !$faceCheck['liveness']
                ? 'Verifikasi keaktifan wajah (liveness) gagal. Pastikan bukan foto/layar HP!'
                : 'Wajah tidak cocok dengan data terdaftar (Skor: ' . round($faceCheck['match_score'] * 100, 1) . '%).';

            return $this->logAndFail($user, $type, $request, $msg, $reason, $faceCheck['match_score'], $faceCheck['liveness']);
        }

        // 6. Watermark Server-Side (Intervention Image / GD) - F-12, F-13 PRD 2.0
        try {
            $watermarkedPath = WatermarkService::processAndSave(
                $request->photo,
                $user,
                $type,
                $lat,
                $lng,
                $distance
            );
        } catch (\Throwable $e) {
            return $this->logAndFail($user, $type, $request, 'Gagal memproses watermark foto: ' . $e->getMessage());
        }

        // 7. Simpan Absensi ke Database - F-14 PRD 2.0
        if ($type === 'in') {
            $attendance->check_in_at         = $serverNow;
            $attendance->check_in_score      = $faceCheck['match_score'];
            $attendance->check_in_liveness   = $faceCheck['liveness'];
            $attendance->check_in_lat        = $lat;
            $attendance->check_in_lng        = $lng;
            $attendance->distance_m          = $distance;
            $attendance->photo_check_in_path = $watermarkedPath;
        } else {
            $attendance->check_out_at         = $serverNow;
            $attendance->check_out_score      = $faceCheck['match_score'];
            $attendance->check_out_liveness   = $faceCheck['liveness'];
            $attendance->check_out_lat        = $lat;
            $attendance->check_out_lng        = $lng;
            $attendance->distance_out_m       = $distance;
            $attendance->photo_check_out_path = $watermarkedPath;
        }
        $attendance->save();

        if ($type === 'out') {
            $attendance->calculateTotalHours();
        }

        // 8. Catat Audit Log Sukses
        AttendanceLog::create([
            'user_id'          => $user->id,
            'type'             => $type,
            'status'           => 'success',
            'match_score'      => $faceCheck['match_score'],
            'liveness_passed'  => $faceCheck['liveness'],
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'client_timestamp' => $request->client_timestamp,
        ]);

        $label = $type === 'in' ? 'Masuk' : 'Pulang';
        return response()->json([
            'success'   => true,
            'message'   => "Absen {$label} berhasil dicatat pukul " . $serverNow->format('H:i:s') . " WIB (Jarak: {$distance}m).",
            'photo_url' => asset('storage/' . $watermarkedPath),
            'time'      => $serverNow->toDateTimeString(),
        ]);
    }

    private function logAndFail(
        $user, string $type, Request $request,
        string $message, string $reasonCode = 'validation_failed',
        ?float $score = null, ?bool $liveness = null
    ) {
        AttendanceLog::create([
            'user_id'          => $user->id,
            'type'             => $type,
            'status'           => 'failed',
            'reason'           => $reasonCode . ': ' . $message,
            'match_score'      => $score,
            'liveness_passed'  => $liveness,
            'ip_address'       => $request->ip(),
            'user_agent'       => $request->userAgent(),
            'client_timestamp' => $request->client_timestamp ?? null,
        ]);

        // Cek apakah relawan ini gagal 3x berturut-turut hari ini (F-25 PRD 2.0 Alert Admin)
        $recentFails = AttendanceLog::where('user_id', $user->id)
            ->where('status', 'failed')
            ->whereDate('created_at', today())
            ->count();

        if ($recentFails === 3) {
            WhatsAppService::alertAdminAnomaly(
                "Relawan *{$user->name}* ({$user->email}) mengalami kegagalan absensi {$recentFails}x hari ini.\nAlasan Terakhir: {$message}"
            );
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], 422);
        }
        return redirect()->back()->with('error', $message);
    }
}
