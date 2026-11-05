<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin utama
        User::updateOrCreate(
            ['email' => 'admin@sppg.local'],
            [
                'name'         => 'Admin SPPG',
                'nik'          => null,
                'phone'        => null,
                'address'      => null,
                'password'     => Hash::make('admin123'),
                'role'         => 'admin',
                'is_active'    => true,
                'face_enrolled'=> false,
            ]
        );

        // Relawan contoh
        $relawan = [
            ['name' => 'Siti Rahayu',   'email' => 'siti@sppg.local',   'nik' => '3201010101010001'],
            ['name' => 'Budi Santoso',  'email' => 'budi@sppg.local',   'nik' => '3201010101010002'],
            ['name' => 'Rina Wulandari','email' => 'rina@sppg.local',   'nik' => '3201010101010003'],
        ];

        foreach ($relawan as $r) {
            User::updateOrCreate(
                ['email' => $r['email']],
                [
                    'name'         => $r['name'],
                    'nik'          => $r['nik'],
                    'phone'        => '08' . rand(100000000, 999999999),
                    'address'      => 'Curug Cukanggalih, Tangerang',
                    'password'     => Hash::make('relawan123'),
                    'role'         => 'relawan',
                    'is_active'    => true,
                    'face_enrolled'=> false,
                ]
            );
        }

        $this->command->info('✅ User seeder selesai: 1 admin + 3 relawan');
    }
}
