<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $bulan = $request->get('bulan', now()->format('Y-m'));
        [$tahun, $bln] = explode('-', $bulan);

        $data = User::where('role', 'relawan')
            ->where('is_active', true)
            ->with(['attendances' => function ($q) use ($tahun, $bln) {
                $q->whereYear('date', $tahun)->whereMonth('date', $bln);
            }])
            ->orderBy('name')
            ->get();

        return view('admin.laporan.index', compact('data', 'bulan'));
    }
}
