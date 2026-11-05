<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="280" alt="Laravel Logo">

# Sistem Absensi & Payroll Relawan SPPG Curug Cukanggalih 02

**Aplikasi Web Absensi Multi-Lapis (Verifikasi Wajah, Geofencing Haversine, Timestamp Server) & Penggajian Otomatis**

[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP 8.2+](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL 9.x](https://img.shields.io/badge/MySQL-9.x-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![Bootstrap 5.3](https://img.shields.io/badge/Bootstrap-5.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com)
[![ARSA Face API](https://img.shields.io/badge/ARSA%20Face%20API-Ready-00C49F?style=for-the-badge)](https://arsa.ai)
[![Fonnte WA API](https://img.shields.io/badge/Fonnte%20WA%20API-Ready-25D366?style=for-the-badge&logo=whatsapp&logoColor=white)](https://fonnte.com)
[![Hostinger](https://img.shields.io/badge/Shared%20Hosting-Hostinger%20Ready-673AB7?style=for-the-badge)](https://hostinger.co.id)

</div>

---

## 📋 Daftar Isi
1. [Tentang Sistem](#-tentang-sistem)
2. [Latar Belakang & Masalah](#-latar-belakang--masalah)
3. [Fitur Unggulan](#-fitur-unggulan)
4. [Arsitektur Keamanan Multi-Lapis](#-arsitektur-keamanan-multi-lapis)
5. [Rumus Geofencing Haversine](#-rumus-geofencing-haversine)
6. [Hak Akses Pengguna (RBAC)](#-hak-akses-pengguna-rbac)
7. [Akun Bawaan (Default Credentials)](#-akun-bawaan-default-credentials)
8. [Panduan Instalasi & Menjalankan](#-panduan-instalasi--menjalankan)
9. [Struktur Direktori Utama](#-struktur-direktori-utama)
10. [Nilai Akademis (Sempro & Jurnal)](#-nilai-akademis-sempro--jurnal)

---

## 🌟 Tentang Sistem

**Sistem Absensi & Payroll Relawan SPPG Curug Cukanggalih 02** adalah platform berbasis web modern yang dirancang untuk mengelola kehadiran dan kompensasi relawan secara objektif, transparan, dan bebas manipulasi.

Sistem ini didesain khusus agar dapat dijalankan dengan efisien di lingkungan **Shared Hosting (Hostinger 3GB RAM)** tanpa memerlukan server mahal (VPS/GPU), dengan memanfaatkan arsitektur *API-as-a-Service* (ARSA Face API untuk biometrik dan Fonnte API untuk gateway WhatsApp).

---

## ⚠️ Latar Belakang & Masalah

| Masalah Riil di Lapangan | Solusi pada Sistem Ini |
|---|---|
| **Titip Absen**: Rekan kerja mengabsenkan relawan yang tidak hadir. | **Verifikasi Wajah Biometrik & Liveness Check** memastikan hanya relawan bersangkutan yang dapat absen. |
| **Absen dari Rumah**: Relawan mengaku sudah di lokasi kerja. | **Geofencing GPS Spasial (Haversine Formula)** dengan radius ketat 100 meter dari titik SPPG. |
| **Manipulasi Jam**: Relawan mengubah jam di perangkat klien. | **Server Timestamp (`now()`)** acuan tunggal yang tidak terpengaruh jam HP/laptop. |
| **Ketiadaan Bukti Visual**: Tidak ada dokumentasi fisik kehadiran. | **Server-Side Watermark** permanen memuat Nama, NIK, Waktu, Koordinat, dan Jarak. |
| **Rekap Honor Manual**: Menghitung jam kerja & uang makan memakan waktu berhari-hari. | **One-Click Payroll Generator & Slip Gaji PDF** otomatis selesai dalam 1 detik. |

---

## ✨ Fitur Unggulan

### 1. 🛡️ Absensi 4 Lapis Keamanan
* **WebRTC Camera API**: Pengambilan foto langsung dari kamera browser tanpa perantara upload file statis.
* **Toggle Flip / Mirror**: Opsi tampilan kamera normal (tidak mirror) atau mode cermin dengan memori preferensi `localStorage`.
* **HTML5 Geolocation**: Mengunci koordinat latitude dan longitude relawan secara real-time.
* **Geofencing Haversine**: Mengukur jarak eksak (meter) ke pusat SPPG Curug Cukanggalih 02 (`-6.262500, 106.564700`).
* **Liveness & Face Verification**: Menguji keaslian wajah dan kecocokan biometrik terhadap wajah terdaftar.
* **Watermark Otomatis Server-Side**: Menggabungkan data identitas, waktu server, dan koordinat ke foto bukti absensi secara permanen.

### 2. 💰 Penggajian & Payroll Otomatis (DSS)
* **Kalkulasi Jam Kerja Eksak**: Total jam kerja dihitung desimal presisi dari selisih `check_in_at` dan `check_out_at`.
* **Master Skema Honor Dinamis**:
  * Honor jam kerja (`total_hours × tarif_per_jam`)
  * Uang transport kehadiran sah per hari
  * Uang makan harian (untuk kehadiran $\ge$ 6 jam)
  * Insentif lembur (kelebihan jam kerja > 8 jam/hari)
  * Potongan keterlambatan
* **Persetujuan Berjenjang (Draft &rarr; Approved &rarr; Paid)**.
* **Cetak Slip Gaji PDF Resmi**: Dibuat via `DomPDF` siap cetak/unduh.
* **Ekspor Excel / CSV**: Format kompatibel untuk pelaporan kas daerah atau transfer bank kolektif.

### 3. 🏢 Manajemen Divisi & Jadwal Fleksibel
* Relawan terorganisir per divisi: **Pengolahan**, **Distribusi**, dan **Administrasi**.
* Jam operasional masuk, batas akhir masuk, dan batas jam pulang dapat diatur berbeda untuk tiap divisi per hari (Senin–Sabtu).
* Pengaturan toleransi keterlambatan dalam menit.

### 4. 📝 Pengajuan Izin / Sakit / Cuti Online
* Relawan mengunggah surat dokter / bukti pendukung langsung dari dashboard.
* Pengurus dapat menyetujui (*Approve*) atau menolak (*Reject*) dengan catatan.
* Izin sah tidak dihitung *alpha* (mangkir) pada laporan rekapitulasi penggajian.

### 5. 📲 WhatsApp Gateway Terintegrasi (Fonnte API)
* Notifikasi otomatis penerbitan slip honorarium saat payroll disetujui.
* Pengingat absensi H-15 menit sebelum batas waktu masuk berakhir.
* Peringatan instan (*Fraud Alert*) ke WhatsApp Admin jika terdeteksi 3x kegagalan berturut-turut.

### 6. 🔍 Deteksi Anomali & Kecurangan (Fraud Detection)
* Memantau penggunaan IP Address ganda untuk relawan berbeda dalam rentang waktu singkat.
* Mencatat seluruh log percobaan gagal lengkap dengan alasan (*out_of_radius*, *face_mismatch*, *outside_schedule*, dll).

---

## 📐 Arsitektur Keamanan Multi-Lapis

```text
[ Browser Relawan ]
   │
   ├── 1. Akses WebRTC Kamera (Snapshot Wajah)
   ├── 2. HTML5 Geolocation (Lat & Lng Relawan)
   │
   ▼ (Kirim Data via HTTPS POST)
[ Laravel 12 Backend - Shared Hosting ]
   │
   ├── 3. Validasi Geofencing Spasial (Haversine Formula)
   │      Jarak <= 100 Meter? ──(Tidak)──> Tolak (out_of_radius) & Simpan Log
   │      │ (Ya)
   ├── 4. Validasi Jadwal Divisi
   │      Sesuai Jam Operasional? ──(Tidak)──> Tolak (outside_schedule) & Simpan Log
   │      │ (Ya)
   ├── 5. ARSA Face API (atau Mode Dummy)
   │      Skor >= 0.85 & Liveness True? ──(Tidak)──> Tolak (face_mismatch) & Alert WA
   │      │ (Ya)
   ├── 6. PHP GD Watermark Processor
   │      Bakar teks: Nama, NIK, Divisi, Server Time, GPS Lat/Lng & Jarak ke foto
   │
   ▼
[ Database MySQL ]
   ├── Simpan record attendances (check_in_at, distance_m, photo_path)
   └── Audit trail attendance_logs (Status: Success)
   │
   ▼
[ Akhir Bulan: Modul Payroll Otomatis ]
   └── Hitung Total Jam, Transport, Uang Makan ──> Terbitkan Slip PDF & Notif WA
```

---

## 🧮 Rumus Geofencing Haversine

Jarak lengkung bumi dihitung langsung di backend menggunakan formula:

```math
d = 2r \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta\lambda}{2}\right)}\right)
```

> **Keterangan:**
> - $r = 6.371.000\text{ meter}$ (jari-jari bumi)
> - $\phi_1, \phi_2$ = Latitude titik awal dan titik tujuan (dalam radian)
> - $\Delta\phi$ = Selisih latitude $(\phi_2 - \phi_1)$
> - $\Delta\lambda$ = Selisih longitude $(\lambda_2 - \lambda_1)$

---

## 👥 Hak Akses Pengguna (RBAC)

| Menu / Fitur | Super Admin | Admin | Relawan |
|---|:---:|:---:|:---:|
| **Dashboard Statistik & Log Fraud** | ✅ | ✅ | ❌ |
| **Pengaturan Geofencing & API Key** | ✅ | ❌ | ❌ |
| **Pengaturan Master Skema Gaji** | ✅ | ❌ | ❌ |
| **Manajemen Divisi & Jam Kerja** | ✅ | ✅ | ❌ |
| **Manajemen Relawan & Reset Wajah** | ✅ | ✅ | ❌ |
| **Verifikasi Permohonan Izin / Sakit** | ✅ | ✅ | ❌ |
| **Generate & Draft Payroll** | ✅ | ✅ | ❌ |
| **Approve & Finalisasi Payroll** | ✅ | ✅ | ❌ |
| **Ekspor CSV / Excel Keuangan** | ✅ | ✅ | ❌ |
| **Absensi Wajah + GPS Geofencing** | ❌ | ❌ | ✅ |
| **Pendaftaran Wajah (Enrollment)** | ❌ | ❌ | ✅ |
| **Mengajukan Izin / Sakit Online** | ❌ | ❌ | ✅ |
| **Unduh Slip Honorarium PDF Pribadi** | ❌ | ❌ | ✅ |

---

## 🔑 Akun Bawaan (Default Credentials)

Semua akun berikut sudah di-seed otomatis ke dalam database:

| Email Akun | Password | Role | Penugasan / Divisi |
|---|---|---|---|
| **`superadmin@sppg.local`** | `superadmin123` | `super_admin` | Akses Penuh & Pengaturan Sistem |
| **`admin@sppg.local`** | `admin123` | `admin` | Admin Operasional & Payroll |
| **`siti@sppg.local`** | `relawan123` | `relawan` | Divisi Pengolahan |
| **`budi@sppg.local`** | `relawan123` | `relawan` | Divisi Distribusi |
| **`rina@sppg.local`** | `relawan123` | `relawan` | Divisi Administrasi |

---

## 🛠️ Panduan Instalasi & Menjalankan

### Prasyarat Sistem
* PHP $\ge$ 8.2 (dengan ekstensi `pdo_mysql`, `gd`, `fileinfo`, `curl`, `mbstring`)
* Composer $\ge$ 2.x
* MySQL Server $\ge$ 8.0 atau 9.x
* Browser modern (Chrome, Edge, Firefox) dengan izin Kamera dan Lokasi

### Langkah Setup

1. **Clone atau Buka Direktori Proyek**:
   ```bash
   git clone https://github.com/Naacth/absensi_relawan.git
   cd absensi_relawan
   ```

2. **Pasang Dependensi PHP**:
   ```bash
   composer install
   ```

3. **Konfigurasi Environment (`.env`)**:
   Salin file `.env.example` ke `.env`, lalu atur database Anda:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=absensisppg
   DB_USERNAME=root
   DB_PASSWORD=your_password

   # Konfigurasi Geofencing & API
   SPPG_NAME="SPPG Curug Cukanggalih 02"
   SPPG_LATITUDE=-6.262500
   SPPG_LONGITUDE=106.564700
   GEOFENCE_RADIUS_METERS=100

   # Mode Dummy (Aman untuk Demo/Sidang tanpa internet/kuota API)
   FACE_API_MODE=dummy
   FONNTE_API_MODE=dummy
   ```

4. **Jalankan Migrasi & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Hubungkan Storage Symlink**:
   ```bash
   php artisan storage:link
   ```

6. **Jalankan Server Lokal**:
   ```bash
   php artisan serve
   ```
   Buka peramban di `http://127.0.0.1:8000`.

---

## 📂 Struktur Direktori Utama

```text
Absensi_vermuk/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php               # Login & Logout Sanctum
│   │   │   ├── Admin/
│   │   │   │   ├── DashboardController.php      # Monitoring & Fraud Alert
│   │   │   │   ├── DivisionController.php       # Manajemen Divisi & Jadwal
│   │   │   │   ├── LeaveRequestController.php   # Persetujuan Izin/Sakit
│   │   │   │   ├── PayrollController.php        # Generate Gaji, Slip PDF, CSV
│   │   │   │   ├── RelawanController.php        # CRUD Relawan & Reset Wajah
│   │   │   │   └── SettingController.php        # Konfigurasi Geofencing & Gaji
│   │   │   └── Relawan/
│   │   │       ├── AbsensiController.php        # Validasi Wajah, GPS & Watermark
│   │   │       ├── DashboardController.php      # Status Kehadiran Relawan
│   │   │       ├── EnrollmentController.php     # Pendaftaran Wajah Pertama Kali
│   │   │       ├── LeaveRequestController.php   # Pengajuan Izin Online
│   │   │       └── PayrollController.php        # Riwayat & Unduh Slip PDF
│   │   └── Middleware/
│   │       └── RoleMiddleware.php               # Proteksi Role Tiga Tingkat
│   ├── Models/
│   │   ├── Attendance.php                       # Model Kehadiran & Jam Kerja
│   │   ├── AttendanceLog.php                    # Audit Trail Percobaan Absen
│   │   ├── Division.php                         # Master Divisi
│   │   ├── FaceEmbedding.php                    # Data Biometrik Terenkripsi
│   │   ├── LeaveRequest.php                     # Permohonan Izin/Cuti
│   │   ├── Payroll.php                          # Periode Penggajian
│   │   ├── PayrollDetail.php                    # Rincian Gaji Relawan
│   │   ├── SalaryScheme.php                     # Master Tarif Honorarium
│   │   ├── Setting.php                          # Pengaturan Sistem
│   │   ├── User.php                             # Model Pengguna & Role
│   │   └── WorkSchedule.php                     # Jam Kerja Divisi
│   └── Services/
│       ├── FaceRecognitionService.php           # Integrasi ARSA API & Dummy
│       ├── GeofenceService.php                  # Perhitungan Haversine Formula
│       ├── WatermarkService.php                 # Image Processing Watermark GD
│       └── WhatsAppService.php                  # Integrasi Fonnte API Gateway
├── database/
│   ├── migrations/                              # Skema Tabel Database
│   └── seeders/
│       └── InitialSystemSeeder.php              # Akun & Data Awal Lengkap
├── resources/views/
│   ├── layouts/                                 # Layout Bootstrap 5 & Sidebar
│   ├── admin/                                   # Halaman Dashboard, Payroll, Divisi
│   ├── relawan/                                 # Halaman Absensi, Izin, Slip
│   └── auth/                                    # Halaman Login
└── routes/
    └── web.php                                  # 45 Rute Lengkap Terproteksi
```

---

## 🎓 Nilai Akademis (Sempro & Jurnal)

Aplikasi ini dirancang memenuhi kaidah rekayasa perangkat lunak dan metodologi penelitian ilmiah:

1. **Multi-Layer Authentication Methodology**:
   Kombinasi verifikasi biometrik (*Face Recognition*), uji keaktifan (*Liveness Detection*), verifikasi posisi spasial (*Geofencing Haversine*), dan penjaminan waktu mutlak (*Server Timestamp*).
2. **Immutable Visual Evidence**:
   Penerapan cap digital server-side sebagai bukti forensik digital kehadiran yang tidak dapat dimanipulasi dari sisi klien.
3. **Decision Support & Financial Automation**:
   Integrasi langsung antara data mentah kehadiran menjadi laporan penggajian dan slip honorarium digital siap audit.
4. **Cost-Effective Distributed Architecture**:
   Membuktikan bahwa teknologi kecerdasan buatan dan notifikasi pesan instan dapat diimplementasikan pada infrastruktur berbiaya terjangkau (*Shared Hosting*) melalui pendekatan *API-as-a-Service*.

---

<div align="center">
  Dikembangkan untuk <strong>SPPG Curug Cukanggalih 02</strong> &bull; 2026
</div>
