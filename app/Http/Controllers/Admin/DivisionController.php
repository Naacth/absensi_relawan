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
        ]);

        $division = Division::create($request->only('name', 'description'));

        // Buat jadwal kerja default Senin - Sabtu (1 s/d 6)
        for ($day = 1; $day <= 6; $day++) {
            WorkSchedule::create([
                'division_id'     => $division->id,
                'day_of_week'     => $day,
                'check_in_start'  => '06:00:00',
                'check_in_end'    => '08:30:00',
                'check_out_start' => '14:00:00',
                'check_out_end'   => '18:00:00',
                'late_tolerance'  => 15,
            ]);
        }

        return redirect()->route('admin.divisions.index')->with('success', 'Divisi dan jadwal kerja default berhasil dibuat.');
    }

    public function edit(Division $division)
    {
        $division->load('schedules');
        return view('admin.divisions.edit', compact('division'));
    }

    public function update(Request $request, Division $division)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:divisions,name,' . $division->id,
            'description' => 'nullable|string|max:255',
            'schedules'   => 'nullable|array',
        ]);

        $division->update($request->only('name', 'description'));

        // Update jadwal jika ada input
        if ($request->has('schedules')) {
            foreach ($request->schedules as $schedId => $times) {
                WorkSchedule::where('id', $schedId)
                    ->where('division_id', $division->id)
                    ->update([
                        'check_in_start'  => $times['check_in_start'],
                        'check_in_end'    => $times['check_in_end'],
                        'check_out_start' => $times['check_out_start'],
                        'check_out_end'   => $times['check_out_end'],
                        'late_tolerance'  => (int) ($times['late_tolerance'] ?? 15),
                    ]);
            }
        }

        return redirect()->route('admin.divisions.index')->with('success', 'Data divisi dan jadwal kerja berhasil diperbarui.');
    }

    public function destroy(Division $division)
    {
        if ($division->users()->count() > 0) {
            return redirect()->back()->with('error', 'Divisi tidak dapat dihapus karena masih memiliki relawan.');
        }

        $division->delete();
        return redirect()->route('admin.divisions.index')->with('success', 'Divisi berhasil dihapus.');
    }
}

