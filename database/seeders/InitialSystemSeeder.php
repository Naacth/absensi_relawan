<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\SalaryScheme;
use App\Models\Setting;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InitialSystemSeeder extends Seeder
{
    public function run(): void
    {
        // ─── 1. Settings Sistem ───────────────────────────────────────────────
        $settings = [
            ['key' => 'sppg_name',        'value' => 'SPPG Curug Cukanggalih 2',  'label' => 'Nama Instansi/SPPG'],
            ['key' => 'sppg_latitude',    'value' => '-6.2577369',                 'label' => 'Latitude Lokasi SPPG'],
            ['key' => 'sppg_longitude',   'value' => '106.5500299',                'label' => 'Longitude Lokasi SPPG'],
            ['key' => 'geofence_radius',  'value' => '100',                        'label' => 'Radius Geofencing (meter)'],
            ['key' => 'arsa_api_key',     'value' => '',                           'label' => 'ARSA Face API Key'],
            ['key' => 'fonnte_api_token', 'value' => '',                           'label' => 'Fonnte WA API Token'],
        ];
        foreach ($settings as $s) {
            Setting::updateOrCreate(['key' => $s['key']], $s);
        }

        // ─── 2. Divisi ────────────────────────────────────────────────────────
        // Pemetaan jabatan dari data relawan.md:
        //   Pengolahan      : Head Chef, Asistant Chef, Cooking
        //   Produksi        : Pemorsian, Persiapan
        //   Distribusi      : Driver, Cuci Ompreng, Cleaning Service, ASLAP
        //   Administrasi    : Admin, KaSPPG
        //   Keamanan        : Security

        $divPengolahan = Division::updateOrCreate(
            ['name' => 'Divisi Pengolahan'],
            [
                'description'          => 'Head Chef, Asistant Chef, dan Cooking',
                'daily_rate'           => 75000,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
            ]
        );

        $divProduksi = Division::updateOrCreate(
            ['name' => 'Divisi Produksi'],
            [
                'description'          => 'Pemorsian dan Persiapan bahan pangan',
                'daily_rate'           => 70000,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
            ]
        );

        $divDistribusi = Division::updateOrCreate(
            ['name' => 'Divisi Distribusi & Layanan'],
            [
                'description'          => 'Driver, Cuci Ompreng, Cleaning Service, ASLAP',
                'daily_rate'           => 65000,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
            ]
        );

        $divAdmin = Division::updateOrCreate(
            ['name' => 'Divisi Administrasi'],
            [
                'description'          => 'Admin dan KaSPPG',
                'daily_rate'           => 80000,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
            ]
        );

        $divKeamanan = Division::updateOrCreate(
            ['name' => 'Divisi Keamanan'],
            [
                'description'          => 'Security SPPG',
                'daily_rate'           => 65000,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
            ]
        );

        // ─── 3. Jadwal Kerja (1 jadwal tunggal per divisi, berlaku semua hari) ─
        $allDivisions = [$divPengolahan, $divProduksi, $divDistribusi, $divAdmin, $divKeamanan];
        foreach ($allDivisions as $div) {
            WorkSchedule::updateOrCreate(
                ['division_id' => $div->id, 'day_of_week' => null],
                [
                    'check_in_start'  => '06:00:00',
                    'check_in_end'    => '08:30:00',
                    'check_out_start' => '14:00:00',
                    'check_out_end'   => '18:00:00',
                    'late_tolerance'  => 15,
                ]
            );
        }

        // ─── 4. Skema Gaji Global (Fallback) ─────────────────────────────────
        SalaryScheme::updateOrCreate(
            ['name' => 'Skema Standar Relawan SPPG'],
            [
                'hourly_rate'          => 0,
                'daily_transport'      => 20000,
                'meal_allowance'       => 15000,
                'overtime_hourly_rate' => 20000,
                'late_deduction'       => 5000,
                'is_active'            => true,
            ]
        );

        // ─── 5. User: Super Admin ─────────────────────────────────────────────
        User::updateOrCreate(
            ['email' => 'superadmin@sppg.local'],
            [
                'name'          => 'Super Admin SPPG',
                'nik'           => '3201019999990001',
                'phone'         => '081234567890',
                'address'       => 'Curug Cukanggalih, Kab. Tangerang',
                'password'      => Hash::make('superadmin123'),
                'role'          => 'super_admin',
                'is_active'     => true,
                'face_enrolled' => false,
            ]
        );

        // ─── 6. User: Admin (dari data relawan — Jabatan: Admin) ──────────────
        User::updateOrCreate(
            ['email' => 'tiarakartika0002@gmail.com'],
            [
                'name'          => 'TIARA KARTIKA NURZAHRA',
                'nik'           => '3603176104020005',
                'phone'         => '083897446008',
                'address'       => 'Kp. Cisereh RT001/007, Kadu Jaya, Curug, Kab. Tangerang',
                'password'      => Hash::make('admin123'),
                'role'          => 'admin',
                'is_active'     => true,
                'face_enrolled' => false,
            ]
        );

        // ─── 7. Relawan (data lengkap dari data relawan.md) ───────────────────
        $relawanData = [
            // ── Divisi Pengolahan ──────────────────────────────────────────────
            [
                'name'    => 'NASRUL PUNGGUH MAULANA',
                'nik'     => '3201100503990003',
                'email'   => 'nasrulpm11@gmail.com',
                'phone'   => '089691838570',
                'address' => 'Kp. Ciseeng RT004/001, Bojongsempu, Parung, Kab. Bogor',
                'div'     => $divPengolahan->id,
            ],
            [
                'name'    => 'JUNED',
                'nik'     => '3603280410890006',
                'email'   => 'junetjun89@gmail.com',
                'phone'   => '082165102267',
                'address' => 'Cluster Udayana Blok UG No.3, Perum Bumi Indah RT005/020, Sukamantri, Pasar Kemis, Kab. Tangerang',
                'div'     => $divPengolahan->id,
            ],
            [
                'name'    => 'AHMAD ASIDUL ALAM',
                'nik'     => '3603051310060006',
                'email'   => 'ahmadasidul58@gmail.com',
                'phone'   => '081296916109',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divPengolahan->id,
            ],
            [
                'name'    => 'FEBRI ARIYANTO',
                'nik'     => '3603171602990002',
                'email'   => 'febriariyanto.12296@gmail.com',
                'phone'   => '085692126558',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divPengolahan->id,
            ],
            [
                'name'    => 'FEBRIAN',
                'nik'     => '3603170902050015',
                'email'   => 'n.molxinous@gmail.com',
                'phone'   => '085871417837',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divPengolahan->id,
            ],
            [
                'name'    => 'JUNIAR ADITIYA',
                'nik'     => '3603171006060003',
                'email'   => 'juniaraditiya4@gmail.com',
                'phone'   => '085773415341',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divPengolahan->id,
            ],
            [
                'name'    => 'MUHAMAD GALIH FIRMANSYAH',
                'nik'     => '3603172003070007',
                'email'   => 'firmansyahmuhamad364@gmail.com',
                'phone'   => '085711559624',
                'address' => 'Kp. Cukanggalih RT004/008, Cukanggalih, Curug, Kab. Tangerang',
                'div'     => $divPengolahan->id,
            ],

            // ── Divisi Produksi ────────────────────────────────────────────────
            [
                'name'    => 'AHMAD YASIN NASUTION',
                'nik'     => '1271012506040002',
                'email'   => 'ahmadyasinnst@gmail.com',
                'phone'   => '085839167963',
                'address' => 'Dusun 3 RT007/003, Purworejo, Kota Gajah, Kab. Lampung Tengah',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'ARA SRIAGUSTIN',
                'nik'     => '3603315209030007',
                'email'   => 'arasrgstn@gmail.com',
                'phone'   => '081413253203',
                'address' => 'Kp. Ranca Gede RT002/006, Munjul, Solear, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'DAVA WAHYU FEDIANSYAH',
                'nik'     => '3603171602070001',
                'email'   => 'davafediansyah16@gmail.com',
                'phone'   => '082111232123',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'DESI AMELIA',
                'nik'     => '3603175805010005',
                'email'   => 'ameldesyamelia@gmail.com',
                'phone'   => '085692665457',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'DZAKI MUFADHOL',
                'nik'     => '1802163101060002',
                'email'   => 'zakifadhol2@gmail.com',
                'phone'   => '0895405709839',
                'address' => 'Harapan Rejo RT009/003, Harapan Rejo, Seputih Agung, Kab. Lampung Tengah',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'MUHAMAD DAFFA',
                'nik'     => '3603170303060003',
                'email'   => 'muhamaddaffa416@gmail.com',
                'phone'   => '083811156329',
                'address' => 'Kp. Pabuaran RT001/005, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'REYSHA MEYRONICA',
                'nik'     => '3603176405060011',
                'email'   => 'resnica78@gmail.com',
                'phone'   => '083159601072',
                'address' => 'Kp. Sukabakti RT002/014, Sukabakti, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'RIYAN INDRI',
                'nik'     => '3603176105940005',
                'email'   => 'riyanindri21@gmail.com',
                'phone'   => '085694534294',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'YUNI SELISTIAWANTI',
                'nik'     => '3671025906950006',
                'email'   => 'yuniselistiawanti0@gmail.com',
                'phone'   => '085718545552',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'ARDAN ABI MANYU',
                'nik'     => '3603192309080002',
                'email'   => 'ardanabimanyu5@gmail.com',
                'phone'   => '083875060409',
                'address' => 'Kp. Panyembir RT003/001, Serdang Kulon, Panongan, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'ASEP SOPIAN',
                'nik'     => '3603191405920001',
                'email'   => 'mnawawi001111@gmail.com',
                'phone'   => '089670274458',
                'address' => 'Kp. Cipari RT003/002, Ciakar, Panongan, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'DIAN RANA',
                'nik'     => '3603226207900002',
                'email'   => 'diyansukses87@gmail.com',
                'phone'   => '083874667664',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'FEBRIANTO SETIAWAN',
                'nik'     => '3603192202000006',
                'email'   => 'gobang1478@gmail.com',
                'phone'   => '0895325184176',
                'address' => 'Kp. Cipari RT002/002, Ciakar, Panongan, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'IKA SANTI WIDIARTI',
                'nik'     => '3603174405870003',
                'email'   => 'zainmuhamad@gmail.com',
                'phone'   => '081314402031',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'PUTRI NATHASA JULIYANTI',
                'nik'     => '3274057007050002',
                'email'   => 'sasyans32@gmail.com',
                'phone'   => '0895806924848',
                'address' => 'Jl. Dr. Cipto Mk Gg. Ariakiban No.164 RT001/008, Pekiringan, Kesambi, Kota Cirebon',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'REHAN SETIAWAN',
                'nik'     => '1801070210040004',
                'email'   => 'reyhansetiawan181@gmail.com',
                'phone'   => '08987578078',
                'address' => 'Kp. Pabuaran No.166 RT004/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'SITI HUMU HANI',
                'nik'     => '3603196503890001',
                'email'   => 'shellanurcahyanti144@gmail.com',
                'phone'   => '085695770569',
                'address' => 'Kp. Cipari RT003/002, Ciakar, Panongan, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'SYAROVA PERMATA SARI',
                'nik'     => '3603175911050003',
                'email'   => 'revapermatasari60@gmail.com',
                'phone'   => '085718227639',
                'address' => 'Kp. Pabuaran RT001/005, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],
            [
                'name'    => 'YUNI WIJI ASIH',
                'nik'     => '3603177005070001',
                'email'   => 'yuniwijiasih1@gmail.com',
                'phone'   => '088905925613',
                'address' => 'Kp. Nagrog RT001/007, Curug Wetan, Curug, Kab. Tangerang',
                'div'     => $divProduksi->id,
            ],

            // ── Divisi Distribusi & Layanan ────────────────────────────────────
            [
                'name'    => 'DAFA DANUARI FADILLAH',
                'nik'     => '3603172002030003',
                'email'   => 'dafadanuarifadillah3@gmail.com',
                'phone'   => '081311889377',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'YOGA ANDIKA SUHENDAR',
                'nik'     => '3603172708050003',
                'email'   => 'dikad4787@gmail.com',
                'phone'   => '085775149215',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'AHMAD FITRAH RAMADHANI FAUZI',
                'nik'     => '3603171902960004',
                'email'   => 'fitrahramadhaan02@gmail.com',
                'phone'   => '085780651141',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'ARIS AMANTO',
                'nik'     => '1808032405930008',
                'email'   => 'arisamanto230597@gmail.com',
                'phone'   => '081386195676',
                'address' => 'Perum Cluster Teratai Blok A2, Ds. Talaga, Kec. Cikupa, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'JESIKA ISTIQOMAH',
                'nik'     => '3603174101050009',
                'email'   => 'jesjesjesika6@gmail.com',
                'phone'   => '081400808780',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'MUHAMMAD CHARLES SURYO PANUNTUN',
                'nik'     => '3671015101030001',
                'email'   => 'romensaromen91@gmail.com',
                'phone'   => '088219957335',
                'address' => 'Perum Citra Pasundan Blok E.2 No.10 RT003/014, Curug Wetan, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'SEFTIAZI DWI PUTRA',
                'nik'     => '3603170109060005',
                'email'   => 'septiazidwiputra28@gmail.com',
                'phone'   => '0895320561673',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'SELVI LESTARI',
                'nik'     => '3603205312900002',
                'email'   => 'bangbang6271@gmail.com',
                'phone'   => '08815907755',
                'address' => 'Kp. Cipari RT004/002, Ciakar, Panongan, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'SHANTY SUSANTY',
                'nik'     => '3603174101820017',
                'email'   => 'shantysusantyyy@gmail.com',
                'phone'   => '085810624290',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'SITI LATIPAH',
                'nik'     => '3603194607830004',
                'email'   => 'warsowetan@gmail.com',
                'phone'   => '081398594675',
                'address' => 'Kp. Cipari RT003/002, Ciakar, Panongan, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'SULFIANDARI',
                'nik'     => '3209156709900003',
                'email'   => 'silviawulandari61@gmail.com',
                'phone'   => '081322130073',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'TIKA KARTIKA',
                'nik'     => '3603195104040005',
                'email'   => 'tika21511@gmail.com',
                'phone'   => '0895374773494',
                'address' => 'Kp. Ranca Kalapa RT003/001, Ranca Kalapa, Panongan, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'YUYUN MAULANA',
                'nik'     => '3603171106980010',
                'email'   => 'kamsanikamsani17@gmail.com',
                'phone'   => '083160274708',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'DODI FIRMANSYAH',
                'nik'     => '3603172004010009',
                'email'   => 'fdoni5718@gmail.com',
                'phone'   => '0895352100264',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'ENDANG PRIYATNA',
                'nik'     => '3603181408960005',
                'email'   => 'enddank.ali@gmail.com',
                'phone'   => '085715103020',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'IRFAN MIFTAHULHADI',
                'nik'     => '3603172203960006',
                'email'   => 'irfanmiftahulhadi1@gmail.com',
                'phone'   => '0881011265149',
                'address' => 'Kp. Pabuaran RT004/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],
            [
                'name'    => 'MUHAMMAD ARIANSYAH',
                'nik'     => '3603172411060004',
                'email'   => 'aiari7748@gmail.com',
                'phone'   => '085770501426',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divDistribusi->id,
            ],

            // ── Divisi Administrasi ────────────────────────────────────────────
            [
                'name'    => 'APRIL SUSANTO WARUWU',
                'nik'     => '1204050804000001',
                'email'   => 'aprilsusanto.kasppg@gmail.com',
                'phone'   => '085261318012',
                'address' => 'Ononamolo I Bot RT/RW 000/000, Kec. Hiliduho, Kab. Nias, Sumatera Utara',
                'div'     => $divAdmin->id,
            ],

            // ── Divisi Keamanan ────────────────────────────────────────────────
            [
                'name'    => 'HENDRA WIJAYA',
                'nik'     => '3603170602730008',
                'email'   => 'hendrawijaya.security@gmail.com',
                'phone'   => '085711940484',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divKeamanan->id,
            ],
            [
                'name'    => 'SARJO SUSANTO',
                'nik'     => '3603170401660005',
                'email'   => 'srjsusanto@gmail.com',
                'phone'   => '085692126533',
                'address' => 'Kp. Pabuaran RT002/006, Curug Kulon, Curug, Kab. Tangerang',
                'div'     => $divKeamanan->id,
            ],
        ];

        foreach ($relawanData as $r) {
            User::updateOrCreate(
                ['email' => strtolower($r['email'])],
                [
                    'name'          => $r['name'],
                    'nik'           => $r['nik'],
                    'phone'         => $r['phone'],
                    'division_id'   => $r['div'],
                    'address'       => $r['address'],
                    'password'      => Hash::make('relawan123'),
                    'role'          => 'relawan',
                    'is_active'     => true,
                    'face_enrolled' => false,
                ]
            );
        }
    }
}
