# Sistem Absensi & Payroll Relawan SPPG Curug Cukanggalih 02

Aplikasi web absensi dan penggajian (payroll) otomatis untuk relawan SPPG Curug Cukanggalih 02 berbasis **Laravel 12**. Sistem menjamin validitas kehadiran melalui 3 lapis keamanan: **Verifikasi Wajah (ARSA API)**, **Geofencing (Haversine Formula)**, dan **Watermark Foto Server-Side**.

---

## Stack Teknologi

| Komponen | Teknologi |
|---|---|
| Backend | Laravel 12, PHP 8.2 |
| Frontend | Bootstrap 5, Bootstrap Icons, Chart.js |
| Database | MySQL (production), SQLite (development) |
| Autentikasi | Laravel Session-based Auth |
| Verifikasi Wajah | ARSA Face API (mode dummy tersedia) |
| Notifikasi WA | Fonnte API (mode dummy tersedia) |
| PDF | barryvdh/laravel-dompdf |
| Watermark | PHP GD (native, tanpa library eksternal) |
| Hosting | Shared Hosting ready (Hostinger) |

---

## Fitur Utama

### 3 Lapis Keamanan Absensi
1. **Verifikasi Wajah + Liveness** — via ARSA Face API, threshold 0.85
2. **Geofencing Haversine** — radius dikonfigurasi admin (default 100m)
3. **Server Timestamp** — waktu absensi dari server, tidak bisa dimanipulasi klien

### Modul yang Tersedia
- ✅ Autentikasi 3 role: Super Admin, Admin, Relawan
- ✅ Enrollment wajah relawan
- ✅ Absensi masuk & pulang (wajah + GPS)
- ✅ Watermark foto server-side (nama, NIK, divisi, waktu, koordinat, jarak)
- ✅ Manajemen divisi & jadwal kerja (1 jadwal tunggal per divisi, berlaku semua hari)
- ✅ Komponen honor per divisi (honor harian, transport, uang makan, lembur, potongan)
- ✅ Pengajuan izin / sakit / cuti + upload lampiran
- ✅ Generate payroll otomatis per periode
- ✅ Approve payroll + notifikasi WA slip gaji ke relawan
- ✅ Download slip gaji PDF per relawan
- ✅ Export rekap payroll ke CSV
- ✅ Laporan kehadiran bulanan (export PDF & Excel/CSV)
- ✅ Dashboard admin dengan grafik tren 7 hari & fraud detection
- ✅ Audit log semua percobaan absensi (berhasil & gagal)
- ✅ Alert WA admin saat relawan gagal absen 3x berturut-turut

---

## Struktur Role & Akses

| Fitur | Super Admin | Admin | Relawan |
|---|:---:|:---:|:---:|
| Konfigurasi Sistem (koordinat, API key, radius) | ✅ | ❌ | ❌ |
| Manajemen User | ✅ | ✅ | ❌ |
| Manajemen Divisi & Jadwal & Honor | ✅ | ✅ | ❌ |
| Approve Izin | ✅ | ✅ | ❌ |
| Generate & Approve Payroll | ✅ | ✅ | ❌ |
| Laporan Kehadiran | ✅ | ✅ | ❌ |
| Absensi (Wajah & GPS) | ❌ | ❌ | ✅ |
| Pengajuan Izin | ❌ | ❌ | ✅ |
| Lihat & Download Slip Gaji | ❌ | ❌ | ✅ |

---

## Instalasi

### Prasyarat
- PHP >= 8.2
- Composer
- MySQL
- Extension PHP: GD, cURL, mbstring, xml

### Langkah Instalasi

```bash
# 1. Clone / extract project
cd Absensi_vermuk

# 2. Install dependencies
composer install

# 3. Copy environment
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Konfigurasi database di .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensisppg
DB_USERNAME=root
DB_PASSWORD=your_password

# 6. Jalankan migration + seeder
php artisan migrate --seed

# 7. Buat symlink storage
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

---

## Konfigurasi .env Penting

```env
# Mode development — bypass validasi jadwal (false = bebas absen kapanpun)
SCHEDULE_CHECK=false

# ARSA Face API (dummy = tidak pakai kuota, real = gunakan API asli)
FACE_API_MODE=dummy
ARSA_API_KEY=

# Fonnte WhatsApp API (dummy = log saja, real = kirim WA sungguhan)
FONNTE_API_MODE=dummy
FONNTE_API_TOKEN=

# Koordinat SPPG (bisa diubah via Pengaturan Sistem)
SPPG_LATITUDE=-6.2577369
SPPG_LONGITUDE=106.5500299
GEOFENCE_RADIUS_METERS=100
```

> **Sebelum demo/sidang:** ubah `FACE_API_MODE=real`, `FONNTE_API_MODE=real`, dan `SCHEDULE_CHECK=true`

---

## Akun Default (Seeder)

| Role | Email | Password |
|---|---|---|
| Super Admin | superadmin@sppg.local | superadmin123 |
| Admin | admin@sppg.local | admin123 |
| Relawan 1 | siti@sppg.local | relawan123 |
| Relawan 2 | budi@sppg.local | relawan123 |
| Relawan 3 | rina@sppg.local | relawan123 |

---

## Struktur Database

| Tabel | Keterangan |
|---|---|
| `users` | Data user semua role |
| `divisions` | Divisi + jadwal + **semua komponen honor** (daily_rate, daily_transport, meal_allowance, overtime_hourly_rate, late_deduction) |
| `work_schedules` | Jadwal kerja per divisi (1 record NULL = berlaku semua hari) |
| `face_embeddings` | Embedding wajah terenkripsi |
| `attendances` | Data absensi masuk & pulang |
| `attendance_logs` | Audit log semua percobaan absensi |
| `leave_requests` | Pengajuan izin / sakit / cuti |
| `payrolls` | Header payroll per periode |
| `payroll_details` | Detail honor per relawan per payroll |
| `salary_schemes` | Skema fallback global (jika divisi belum diatur) |
| `settings` | Konfigurasi sistem (koordinat, radius, API keys) |

---

## Kalkulasi Honor Payroll

```
Honor Pokok    = daily_rate (divisi) × Hari Hadir
Transport      = daily_transport (divisi) × Hari Hadir
Uang Makan     = meal_allowance (divisi) × Hari dengan jam kerja ≥ 6 jam
Lembur         = overtime_hourly_rate (divisi) × Total jam > 8/hari
Potongan       = late_deduction (divisi) × Jumlah keterlambatan
─────────────────────────────────────────────────────────────────
Total Bersih   = Honor Pokok + Transport + Uang Makan + Lembur - Potongan
```

Semua komponen honor dikonfigurasi **per divisi** di halaman **Divisi & Jadwal → Edit Divisi**.

---

## Routes Utama

### Admin (`/admin/*`)
| Method | URL | Keterangan |
|---|---|---|
| GET | `/admin/dashboard` | Dashboard & statistik |
| GET/POST | `/admin/divisions` | Manajemen divisi |
| GET/PUT | `/admin/divisions/{id}/edit` | Edit divisi, jadwal, & honor |
| GET | `/admin/relawan` | Daftar relawan |
| GET | `/admin/izin` | Permohonan izin |
| GET | `/admin/payroll` | Daftar payroll |
| GET | `/admin/payroll/generate` | Form generate payroll |
| POST | `/admin/payroll/generate` | Proses generate payroll |
| GET | `/admin/payroll/{id}` | Detail payroll |
| PATCH | `/admin/payroll/{id}/approve` | Approve + kirim WA |
| GET | `/admin/payroll/{id}/export-csv` | Export CSV |
| GET | `/admin/payroll/slip/{detail}/pdf` | Download slip PDF |
| GET | `/admin/laporan` | Laporan kehadiran |
| GET | `/admin/laporan/export-pdf` | Export PDF laporan |
| GET | `/admin/laporan/export-excel` | Export CSV laporan |
| GET/POST | `/admin/pengaturan` | Pengaturan sistem (Super Admin only) |

### Relawan (`/relawan/*`)
| Method | URL | Keterangan |
|---|---|---|
| GET | `/relawan/dashboard` | Dashboard relawan |
| GET/POST | `/relawan/enrollment` | Pendaftaran wajah |
| GET/POST | `/relawan/absensi` | Halaman & proses absensi |
| GET/POST | `/relawan/izin` | Pengajuan izin |
| GET | `/relawan/payroll` | Daftar slip honor |
| GET | `/relawan/payroll/slip/{id}` | Download slip PDF |

---

## Cara Penggunaan

### Alur Admin
1. Login sebagai Admin
2. **Divisi & Jadwal** → Tambah divisi → isi **Honor Harian, Transport, Uang Makan, Lembur**
3. **Data Relawan** → Tambah relawan → assign ke divisi
4. Relawan enrollment wajah & mulai absensi
5. **Payroll & Honor** → Generate Payroll → pilih periode → Kalkulasi
6. Review draft → **Approve & Kirim WA** → relawan terima notifikasi WA
7. Setelah transfer → **Tandai Lunas**

### Alur Relawan
1. Login → **Daftarkan Wajah** (enrollment)
2. Buka **Absensi** → izinkan kamera & GPS
3. Klik **Absen Masuk** → wajah + GPS diverifikasi → foto berwatermark tersimpan
4. Setelah jam kerja → **Absen Pulang**
5. Lihat slip honor di **Slip Honor Saya**

---

## Mitigasi untuk Shared Hosting

| Masalah | Solusi |
|---|---|
| Kuota ARSA (100/bln) habis saat testing | `FACE_API_MODE=dummy` di .env, ganti ke `real` H-1 sidang |
| Timeout upload foto | Set `max_execution_time=120` & `upload_max_filesize=10M` di PHP config hPanel |
| GPS tidak akurat di dalam gedung | Naikkan radius geofencing via Pengaturan Sistem |

---

## Changelog

### v2.0 (November 2026)
- Refactor jadwal kerja: dari 6 jadwal/hari menjadi 1 jadwal tunggal per divisi
- Honor per divisi: tambah kolom `daily_rate`, `daily_transport`, `meal_allowance`, `overtime_hourly_rate`, `late_deduction` di tabel `divisions`
- Semua komponen honor kini dikonfigurasi langsung di halaman Edit Divisi
- Hapus menu "Skema Honor" terpisah di sidebar
- Tambah grafik tren 7 hari (Chart.js) di dashboard admin
- Tambah export laporan kehadiran ke PDF & CSV
- Tambah rate limiting: login (5 req/menit), absensi (10 req/menit)
- Fix: `use Illuminate\Support\Str` missing di PayrollController
- Fix: N+1 query di approve payroll
- Fix: WhatsApp alert hanya dikirim tepat di percobaan gagal ke-3
- Fix: Relawan tanpa divisi tidak bisa absen
- Fix: Migration kompatibel SQLite & MySQL
- Tambah `SCHEDULE_CHECK=false` di .env untuk bypass validasi jadwal saat development

### v1.0 (November 2026)
- Initial release: absensi wajah + GPS, watermark, payroll, izin, notifikasi WA
