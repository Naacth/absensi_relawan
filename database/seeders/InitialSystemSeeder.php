<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\SalaryScheme;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class InitialSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings Awal
        $settings = [
            ['key' => 'sppg_name',          'value' => 'SPPG CURUG (Drijaya Wahana Perkasa)', 'label' => 'Nama Instansi/SPPG'],
            ['key' => 'sppg_latitude',      'value' => '-6.2577369',                          'label' => 'Latitude Lokasi SPPG'],
            ['key' => 'sppg_longitude',     'value' => '106.5500299',                         'label' => 'Longitude Lokasi SPPG'],
            ['key' => 'geofence_radius',    'value' => '100',                      'label' => 'Radius Geofencing (meter)'],
            ['key' => 'arsa_api_key',       'value' => '',                         'label' => 'ARSA Face API Key'],
            ['key' => 'fonnte_api_token',   'value' => '',                         'label' => 'Fonnte WA API Token'],
        ];

        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        // 2. Divisi
        $divPengolahan = Division::firstOrCreate(['name' => 'Divisi Pengolahan'], ['description' => 'Operasional produksi dan pengolahan pangan']);
        $divDistribusi = Division::firstOrCreate(['name' => 'Divisi Distribusi'], ['description' => 'Penyaluran dan logistik pangan']);
        $divAdmin      = Division::firstOrCreate(['name' => 'Divisi Administrasi'], ['description' => 'Pendataan, inventaris, dan administrasi']);

        // 3. Jadwal Kerja untuk Tiap Divisi (Senin - Sabtu: 1 s/d 6)
        $divisions = [$divPengolahan, $divDistribusi, $divAdmin];
        foreach ($divisions as $div) {
            for ($day = 1; $day <= 6; $day++) {
                WorkSchedule::updateOrCreate(
                    [
                        'division_id' => $div->id,
                        'day_of_week' => $day,
                    ],
                    [
                        'check_in_start'  => '06:00:00',
                        'check_in_end'    => '08:30:00',
                        'check_out_start' => '14:00:00',
                        'check_out_end'   => '18:00:00',
                        'late_tolerance'  => 15,
                    ]
                );
            }
        }

        // 4. Skema Gaji Master
        SalaryScheme::firstOrCreate(
            ['name' => 'Skema Standar Relawan SPPG'],
            [
                'hourly_rate'          => 15000,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
                'is_active'            => true,
            ]
        );

        // 5. User: Super Admin
        User::updateOrCreate(
            ['email' => 'superadmin@sppg.local'],
            [
                'name'         => 'Super Admin SPPG',
                'nik'          => '3201019999990001',
                'phone'        => '081234567890',
                'address'      => 'Curug Cukanggalih, Kab. Tangerang',
                'password'     => Hash::make('superadmin123'),
                'role'         => 'super_admin',
                'is_active'    => true,
                'face_enrolled'=> false,
            ]
        );

        // 6. User: Admin
        User::updateOrCreate(
            ['email' => 'admin@sppg.local'],
            [
                'name'         => 'Admin Operasional',
                'nik'          => '3201019999990002',
                'phone'        => '081234567891',
                'address'      => 'Curug Cukanggalih, Kab. Tangerang',
                'password'     => Hash::make('admin123'),
                'role'         => 'admin',
                'is_active'    => true,
                'face_enrolled'=> false,
            ]
        );

        // 7. Relawan dengan Divisi
        $relawanList = [
            ['name' => 'Siti Rahayu',    'email' => 'siti@sppg.local',  'nik' => '3201010101010001', 'div' => $divPengolahan->id, 'phone' => '081234567801'],
            ['name' => 'Budi Santoso',   'email' => 'budi@sppg.local',  'nik' => '3201010101010002', 'div' => $divDistribusi->id, 'phone' => '081234567802'],
            ['name' => 'Rina Wulandari', 'email' => 'rina@sppg.local',  'nik' => '3201010101010003', 'div' => $divAdmin->id,      'phone' => '081234567803'],
        ];

        foreach ($relawanList as $r) {
            User::updateOrCreate(
                ['email' => $r['email']],
                [
                    'name'         => $r['name'],
                    'nik'          => $r['nik'],
                    'phone'        => $r['phone'],
                    'division_id'  => $r['div'],
                    'address'      => 'Curug Cukanggalih, Kab. Tangerang',
                    'password'     => Hash::make('relawan123'),
                    'role'         => 'relawan',
                    'is_active'    => true,
                    'face_enrolled'=> false,
                ]
            );
        }
    }
}

