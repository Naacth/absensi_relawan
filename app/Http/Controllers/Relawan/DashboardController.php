<?php

namespace App\Http\Controllers\Relawan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load('division');

        $absenHariIni = $user->todayAttendance();
        $schedule = $user->division ? $user->division->todaySchedule() : null;

        $riwayat = $user->attendances()
            ->orderByDesc('date')
            ->limit(15)
            ->get();

        $pengajuanIzinTerbaru = $user->leaveRequests()
            ->orderByDesc('created_at')
            ->limit(3)
            ->get();

        return view('relawan.dashboard', compact('user', 'absenHariIni', 'schedule', 'riwayat', 'pengajuanIzinTerbaru'));
    }
}
