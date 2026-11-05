<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalaryScheme;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $salaryScheme = SalaryScheme::first() ?? new SalaryScheme();

        return view('admin.settings.index', compact('settings', 'salaryScheme'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'sppg_name'            => 'required|string|max:150',
            'sppg_latitude'        => 'required|numeric|between:-90,90',
            'sppg_longitude'       => 'required|numeric|between:-180,180',
            'geofence_radius'      => 'required|numeric|min:10|max:5000',
            'hourly_rate'          => 'required|numeric|min:0',
            'daily_transport'      => 'required|numeric|min:0',
            'meal_allowance'       => 'required|numeric|min:0',
            'overtime_hourly_rate' => 'required|numeric|min:0',
            'late_deduction'       => 'required|numeric|min:0',
            'arsa_api_key'         => 'nullable|string',
            'fonnte_api_token'     => 'nullable|string',
        ]);

        // Simpan setting umum
        Setting::set('sppg_name', $request->sppg_name, 'Nama Instansi/SPPG');
        Setting::set('sppg_latitude', $request->sppg_latitude, 'Latitude Lokasi SPPG');
        Setting::set('sppg_longitude', $request->sppg_longitude, 'Longitude Lokasi SPPG');
        Setting::set('geofence_radius', $request->geofence_radius, 'Radius Geofencing (meter)');
        Setting::set('arsa_api_key', $request->arsa_api_key, 'ARSA Face API Key');
        Setting::set('fonnte_api_token', $request->fonnte_api_token, 'Fonnte WA API Token');

        // Simpan skema gaji
        $scheme = SalaryScheme::first() ?? new SalaryScheme();
        $scheme->fill([
            'name'                 => 'Skema Standar Relawan SPPG',
            'hourly_rate'          => $request->hourly_rate,
            'daily_transport'      => $request->daily_transport,
            'meal_allowance'       => $request->meal_allowance,
            'overtime_hourly_rate' => $request->overtime_hourly_rate,
            'late_deduction'       => $request->late_deduction,
            'is_active'            => true,
        ])->save();

        return redirect()->back()->with('success', 'Konfigurasi sistem & skema gaji berhasil diperbarui.');
    }
}

