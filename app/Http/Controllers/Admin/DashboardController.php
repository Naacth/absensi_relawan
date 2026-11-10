<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\AttendanceLog;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalRelawan   = User::where('role', 'relawan')->where('is_active', true)->count();
        $hadirHariIni   = Attendance::whereDate('date', today())->whereNotNull('check_in_at')->count();
        $izinHariIni    = LeaveRequest::where('status', 'approved')
            ->whereDate('start_date', '<=', today())
            ->whereDate('end_date', '>=', today())
            ->count();

        $belumAbsen     = max(0, $totalRelawan - $hadirHariIni - $izinHariIni);
        $persentaseHadir = $totalRelawan > 0 ? round(($hadirHariIni / $totalRelawan) * 100, 1) : 0;

        // Relawan yang sudah absen masuk hari ini beserta skor & koordinat
        $absenHariIni = Attendance::with(['user.division'])
            ->whereDate('date', today())
            ->whereNotNull('check_in_at')
            ->orderByDesc('check_in_at')
            ->get();

        // Tren 7 hari terakhir
        $tren = collect(range(6, 0))->map(function ($daysAgo) {
            $tanggal = now()->subDays($daysAgo)->toDateString();
            return [
                'tanggal' => $tanggal,
                'hadir'   => Attendance::whereDate('date', $tanggal)->whereNotNull('check_in_at')->count(),
            ];
        });

        // Log absensi anomali / gagal terbaru (Fraud & Security Log)
        $logGagal = AttendanceLog::with('user')
            ->where('status', 'failed')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalRelawan', 'hadirHariIni', 'izinHariIni', 'belumAbsen', 'persentaseHadir',
            'absenHariIni', 'tren', 'logGagal'
        ));
    }
}
