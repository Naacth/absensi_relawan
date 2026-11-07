<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class DivisionController extends Controller
{
    public function index()
    {
        $divisions = Division::withCount('users')->with('schedules')->get();
        return view('admin.divisions.index', compact('divisions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:divisions,name',
            'description' => 'nullable|string|max:255',
            'daily_rate'  => 'nullable|numeric|min:0',
        ]);

        $division = Division::create([
            'name'        => $request->name,
            'description' => $request->description,
            'daily_rate'  => $request->daily_rate ?? 0,
        ]);

        // Buat 1 jadwal tunggal (berlaku semua hari, day_of_week = NULL)
        WorkSchedule::create([
            'division_id'     => $division->id,
            'day_of_week'     => null,
            'check_in_start'  => '06:00:00',
            'check_in_end'    => '08:30:00',
            'check_out_start' => '14:00:00',
            'check_out_end'   => '18:00:00',
            'late_tolerance'  => 15,
        ]);

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Divisi dan jadwal kerja berhasil dibuat.');
    }

    public function edit(Division $division)
    {
        $division->load('schedules');
        // Ambil jadwal tunggal, buat baru jika belum ada (migrasi dari data lama)
        $schedule = $division->schedules()->whereNull('day_of_week')->first();
        if (!$schedule) {
            // Ambil salah satu jadwal lama jika ada, sebagai nilai default
            $old = $division->schedules()->first();
            $schedule = WorkSchedule::create([
                'division_id'     => $division->id,
                'day_of_week'     => null,
                'check_in_start'  => $old?->check_in_start  ?? '06:00:00',
                'check_in_end'    => $old?->check_in_end    ?? '08:30:00',
                'check_out_start' => $old?->check_out_start ?? '14:00:00',
                'check_out_end'   => $old?->check_out_end   ?? '18:00:00',
                'late_tolerance'  => $old?->late_tolerance  ?? 15,
            ]);
        }

        return view('admin.divisions.edit', compact('division', 'schedule'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name'                 => 'required|string|max:100|unique:divisions,name,' . $division->id,
            'description'          => 'nullable|string|max:255',
            'daily_rate'           => 'nullable|numeric|min:0',
            'daily_transport'      => 'nullable|numeric|min:0',
            'meal_allowance'       => 'nullable|numeric|min:0',
            'overtime_hourly_rate' => 'nullable|numeric|min:0',
            'late_deduction'       => 'nullable|numeric|min:0',
            'check_in_start'       => 'required|date_format:H:i',
            'check_in_end'         => 'required|date_format:H:i|after:check_in_start',
            'check_out_start'      => 'required|date_format:H:i',
            'check_out_end'        => 'required|date_format:H:i|after:check_out_start',
            'late_tolerance'       => 'required|integer|min:0|max:120',
        ]);

        $division->update([
            'name'                 => $request->name,
            'description'          => $request->description,
            'daily_rate'           => $request->daily_rate ?? 0,
            'daily_transport'      => $request->daily_transport ?? 0,
            'meal_allowance'       => $request->meal_allowance ?? 0,
            'overtime_hourly_rate' => $request->overtime_hourly_rate ?? 0,
            'late_deduction'       => $request->late_deduction ?? 0,
        ]);

        // Update atau buat jadwal tunggal
        WorkSchedule::updateOrCreate(
            [
                'division_id' => $division->id,
                'day_of_week' => null,
            ],
            [
                'check_in_start'  => $request->check_in_start . ':00',
                'check_in_end'    => $request->check_in_end . ':00',
                'check_out_start' => $request->check_out_start . ':00',
                'check_out_end'   => $request->check_out_end . ':00',
                'late_tolerance'  => (int) $request->late_tolerance,
            ]
        );

        return redirect()->route('admin.divisions.index')
            ->with('success', 'Divisi dan jadwal kerja berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        if ($division->users()->count() > 0) {
            return redirect()->back()->with('error', 'Divisi tidak dapat dihapus karena masih memiliki relawan.');
        }

        $division->delete();
        return redirect()->route('admin.divisions.index')
            ->with('success', 'Divisi berhasil dihapus.');
    }
}
