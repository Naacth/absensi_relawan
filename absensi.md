Berikut **PRD (Product Requirements Document)** yang siap dipakai untuk **Seminar Proposal** dan bahan **tugas jurnal**. Dokumen ini sudah disesuaikan dengan stack: **Laravel 12, MySQL, Bootstrap 5, Laravel Sanctum**, serta fitur **absensi relawan SPPG Curug Cukanggalih 02 dengan verifikasi wajah dan timestamp server**.

---

# PRODUCT REQUIREMENTS DOCUMENT (PRD)
## Aplikasi Absensi Relawan SPPG Curug Cukanggalih 02 Berbasis Web dengan Verifikasi Wajah dan Timestamp

| Item | Keterangan |
|---|---|
| Nama Proyek | Sistem Absensi Relawan SPPG Curug Cukanggalih 02 |
| Versi | 1.0 |
| Tanggal | 5 November 2026 |
| Penulis | [Nama Mahasiswa] |
| Stack | Laravel 12, MySQL, Bootstrap 5, Laravel Sanctum, Face Recognition API |
| Status | Draft Final untuk Sempro |

---

## 1. Ringkasan Eksekutif

SPPG Curug Cukanggalih 02 memiliki relawan yang bekerja secara harian. Saat ini proses absensi masih dilakukan secara manual sehingga rawan titip absen, kehilangan data, dan sulit direkap. PRD ini merancang aplikasi web absensi relawan yang menggunakan **verifikasi wajah** dan **timestamp server** untuk memastikan kehadiran autentik dan tidak dapat dimanipulasi.

Aplikasi dibangun dengan **Laravel 12** sebagai backend, **MySQL** sebagai database, **Bootstrap 5** untuk antarmuka responsif, dan **Laravel Sanctum** untuk autentikasi SPA. Verifikasi wajah menggunakan **Face Recognition API** yang mendukung pencocokan wajah dan **liveness detection**.

---

## 2. Latar Belakang Masalah

1. Absensi manual memungkinkan praktik **titip absen**.
2. Rekapitulasi jam kerja dilakukan manual sehingga memakan waktu dan rawan salah hitung.
3. Tidak ada jaminan absensi dilakukan pada waktu sebenarnya.
4. Data absensi kertas mudah hilang atau rusak.
5. Pengelola SPPG membutuhkan data kehadiran yang akurat untuk evaluasi disiplin dan perhitungan honor relawan.

---

## 3. Tujuan

1. Membangun aplikasi absensi relawan berbasis web.
2. Menerapkan verifikasi wajah untuk memastikan relawan yang absen adalah benar pemilik akun.
3. Mencatat waktu absensi menggunakan **timestamp server** agar tidak dapat dimanipulasi dari sisi klien.
4. Menyediakan dashboard dan laporan kehadiran bagi admin.
5. Menghasilkan data yang siap digunakan untuk evaluasi dan perhitungan honor relawan.

---

## 4. Ruang Lingkup

### 4.1 In-Scope

- Aplikasi web SPA dengan Laravel 12 + Bootstrap 5.
- Autentikasi dan otorisasi menggunakan Laravel Sanctum.
- Manajemen pengguna: admin dan relawan.
- Pendaftaran wajah relawan / enrollment.
- Absensi masuk dan pulang dengan verifikasi wajah.
- Validasi waktu absensi.
- Pencatatan timestamp server.
- Dashboard admin.
- Laporan harian, mingguan, dan bulanan.
- Ekspor laporan ke PDF dan Excel.
- Log audit percobaan absensi.
- Riwayat absensi relawan.

### 4.2 Out-of-Scope

- Aplikasi mobile native.
- Perhitungan gaji otomatis.
- Integrasi fingerprint atau RFID.
- Multi-cabang SPPG.
- Mode offline.
- Video streaming real-time untuk verifikasi wajah.

---

## 5. Stakeholder

| Stakeholder | Peran | Kebutuhan |
|---|---|---|
| Pengelola SPPG | Admin | Dashboard, laporan, manajemen relawan |
| Relawan | Pengguna akhir | Absensi cepat, riwayat kehadiran |
| Mahasiswa | Pengembang | Dokumentasi teknis, data pengujian |
| Dosen Pembimbing | Reviewer | Kesesuaian metode dan kualitas rekayasa perangkat lunak |

---

## 6. Kebutuhan Fungsional

### 6.1 Autentikasi dan Otorisasi

| Kode | Requirement |
|---|---|
| F-01 | Sistem menyediakan login untuk admin dan relawan. |
| F-02 | Autentikasi SPA menggunakan Laravel Sanctum dengan konfigurasi stateful. |
| F-03 | Sistem menerapkan CSRF protection dan cookie HttpOnly. |
| F-04 | Sistem menerapkan role-based access control: admin dan relawan. |

### 6.2 Manajemen Relawan

| Kode | Requirement |
|---|---|
| F-05 | Admin dapat menambah, mengedit, dan menonaktifkan relawan. |
| F-06 | Data relawan mencakup nama, NIK, nomor telepon, alamat, email, dan status aktif. |
| F-07 | Admin dapat melihat daftar relawan dan status pendaftaran wajah. |
| F-08 | Admin dapat mereset pendaftaran wajah relawan jika diperlukan. |

### 6.3 Pendaftaran Wajah / Enrollment

| Kode | Requirement |
|---|---|
| F-09 | Relawan wajib mendaftarkan wajah sebelum dapat melakukan absensi. |
| F-10 | Halaman enrollment mengakses kamera perangkat dan mengambil foto wajah. |
| F-11 | Sistem mengirim foto ke Face Recognition API untuk mengekstrak face embedding. |
| F-12 | Face embedding disimpan di database dalam bentuk terenkripsi. |
| F-13 | Sistem menampilkan panduan pengambilan foto: pencahayaan cukup, wajah terlihat jelas, tanpa masker. |

### 6.4 Absensi dengan Verifikasi Wajah dan Timestamp

| Kode | Requirement |
|---|---|
| F-14 | Relawan dapat melakukan absen masuk dan absen pulang. |
| F-15 | Saat absen, sistem mengambil snapshot wajah melalui kamera. |
| F-16 | Sistem memverifikasi wajah relawan yang login terhadap embedding yang tersimpan. |
| F-17 | Sistem memeriksa status liveness untuk mencegah foto/video palsu. |
| F-18 | Jika verifikasi berhasil, sistem mencatat absensi dengan `server_timestamp` dari Laravel `now()`. |
| F-19 | `client_timestamp` hanya dicatat untuk audit, bukan sebagai waktu utama. |
| F-20 | Absen masuk hanya dapat dilakukan pada rentang waktu yang ditentukan, misalnya 06:00–08:00. |
| F-21 | Absen pulang hanya dapat dilakukan setelah jam tertentu, misalnya setelah 14:00. |
| F-22 | Sistem mencegah duplikasi absen masuk/pulang pada hari yang sama. |
| F-23 | Setiap percobaan absensi, berhasil atau gagal, dicatat di `attendance_logs`. |

### 6.5 Dashboard Admin

| Kode | Requirement |
|---|---|
| F-24 | Dashboard menampilkan jumlah relawan terdaftar, hadir, belum absen, dan persentase kehadiran hari ini. |
| F-25 | Dashboard menampilkan daftar relawan yang sudah absen masuk beserta jam dan skor verifikasi. |
| F-26 | Dashboard menampilkan grafik tren kehadiran 7 hari terakhir. |
| F-27 | Admin dapat memfilter log absensi berdasarkan tanggal dan nama relawan. |

### 6.6 Laporan

| Kode | Requirement |
|---|---|
| F-28 | Admin dapat membuat laporan rekap absensi harian, mingguan, dan bulanan. |
| F-29 | Laporan memuat nama relawan, tanggal, jam masuk, jam pulang, total jam kerja, dan status verifikasi. |
| F-30 | Laporan dapat diekspor ke PDF dan Excel. |

### 6.7 Riwayat Relawan

| Kode | Requirement |
|---|---|
| F-31 | Relawan dapat melihat riwayat absensi pribadi. |
| F-32 | Riwayat menampilkan tanggal, jam masuk, jam pulang, dan total jam kerja. |

---

## 7. Kebutuhan Non-Fungsional

| Kode | Kategori | Requirement |
|---|---|---|
| NF-01 | Performa | Verifikasi wajah maksimal 3 detik tergantung koneksi API. |
| NF-02 | Keamanan | Semua komunikasi menggunakan HTTPS. |
| NF-03 | Keamanan | Face embedding disimpan terenkripsi. |
| NF-04 | Keamanan | Endpoint absensi dibatasi rate limiting, misalnya 5 request/menit per user. |
| NF-05 | Usability | Antarmuka responsif dengan Bootstrap 5, dapat digunakan di desktop dan tablet. |
| NF-06 | Auditabilitas | Setiap percobaan absensi tercatat dan tidak dapat dihapus permanen. |
| NF-07 | Kompatibilitas | Berjalan pada Chrome, Firefox, dan Edge yang mendukung akses kamera. |
| NF-08 | Skalabilitas | Mampu menangani minimal 100 relawan dan 200 absensi per hari. |

---

## 8. Arsitektur Sistem

```text
Browser / SPA Bootstrap 5
        |
        | HTTPS + Cookie Sanctum
        v
Laravel 12
├── Auth Controller
├── Attendance Controller
├── Admin Controller
├── Report Controller
├── FaceRecognitionService
├── Models: User, FaceEmbedding, Attendance, AttendanceLog
└── Middleware Sanctum
        |
        ├──> MySQL
        |
        └──> Face Recognition API
                ├── Extract Embedding
                └── Verify Face + Liveness
```

### Alur Absensi

1. Relawan login.
2. Relawan membuka halaman absensi.
3. Relawan memilih absen masuk/pulang.
4. Browser mengambil snapshot wajah.
5. Snapshot dikirim ke backend Laravel.
6. Backend memvalidasi jadwal absensi dan duplikasi.
7. Backend memanggil Face Recognition API.
8. API mengembalikan skor kecocokan dan status liveness.
9. Jika valid, sistem menyimpan absensi dengan timestamp server.
10. Jika gagal, sistem menyimpan log kegagalan.

---

## 9. Rancangan Database Ringkas

### Tabel `users`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| name | varchar | Nama relawan/admin |
| email | varchar unique | Login |
| password | varchar | Hash password |
| role | enum('admin','relawan') | Role |
| is_active | boolean | Status akun |
| created_at | timestamp | |

### Tabel `face_embeddings`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | Relawan |
| embedding | json/text encrypted | Vektor wajah |
| created_at | timestamp | |

### Tabel `attendances`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK | Relawan |
| date | date | Tanggal absensi |
| check_in_at | timestamp nullable | Waktu masuk server |
| check_out_at | timestamp nullable | Waktu pulang server |
| check_in_score | float nullable | Skor verifikasi masuk |
| check_out_score | float nullable | Skor verifikasi pulang |
| check_in_liveness | boolean nullable | Status liveness masuk |
| check_out_liveness | boolean nullable | Status liveness pulang |
| total_hours | decimal(5,2) nullable | Total jam kerja |

### Tabel `attendance_logs`

| Kolom | Tipe | Keterangan |
|---|---|---|
| id | bigint PK | |
| user_id | bigint FK nullable | Null jika wajah tidak dikenali |
| type | enum('in','out') | Jenis absensi |
| status | enum('success','failed') | Status |
| reason | varchar nullable | Alasan gagal |
| match_score | float nullable | Skor kecocokan |
| ip_address | varchar | IP klien |
| user_agent | text | Browser klien |
| created_at | timestamp | Waktu log |

---

## 10. Integrasi Face Recognition API

### 10.1 Kebutuhan API

API harus mendukung:

1. Ekstraksi face embedding.
2. Verifikasi wajah 1:1 terhadap embedding relawan yang login.
3. Liveness detection.
4. Response berupa skor kecocokan dan status liveness.

### 10.2 Threshold

- Skor kecocokan minimal: **0.85** atau dapat dikonfigurasi.
- Liveness wajib bernilai valid.
- Jika skor di bawah threshold, absensi ditolak dan dicatat di log.

### 10.3 Penyimpanan Data Biometrik

- Foto mentah tidak disimpan permanen.
- Embedding disimpan terenkripsi.
- Relawan memberikan consent saat enrollment.

---

## 11. Keamanan dan Privasi

| Aspek | Mitigasi |
|---|---|
| Data biometrik | Embedding dienkripsi, akses terbatas |
| Timestamp | Menggunakan waktu server, bukan waktu klien |
| CSRF/XSS | Sanctum CSRF token dan escaping Blade |
| SQL Injection | Eloquent ORM dan parameter binding |
| Brute force | Rate limiting endpoint absensi |
| Audit | Log percobaan absensi dengan IP dan user agent |
| Privasi | Mengacu pada UU PDP, data tidak dibagikan selain ke API verifikasi |

---

## 12. User Stories

| ID | Sebagai | Saya ingin | Sehingga |
|---|---|---|---|
| US-01 | Relawan | absen masuk dengan wajah | kehadiran tercatat tanpa kartu |
| US-02 | Relawan | melihat riwayat absensi | saya tahu total jam kerja |
| US-03 | Admin | melihat relawan yang belum absen | saya bisa menindaklanjuti |
| US-04 | Admin | mengekspor laporan bulanan | saya bisa menghitung honor |
| US-05 | Admin | melihat log absensi gagal | saya bisa memantau kecurangan |

---

## 13. Kriteria Penerimaan

**F-18 — Timestamp Server**
- Given relawan berhasil verifikasi wajah
- When relawan absen masuk pukul 07.15
- Then sistem menyimpan `check_in_at` sesuai waktu server dan skor verifikasi.

**F-20 — Validasi Waktu**
- Given relawan mencoba absen masuk pukul 09.00
- When mengirim request absensi
- Then sistem menolak dengan pesan “Absen masuk hanya dapat dilakukan antara pukul 06.00 dan 08.00” dan mencatat log gagal.

**F-24 — Dashboard Admin**
- Given admin login
- When membuka dashboard
- Then admin melihat jumlah hadir, belum absen, dan grafik 7 hari terakhir.

---

## 14. Rencana Pengujian

| Jenis Pengujian | Fokus |
|---|---|
| Black-box testing | Login, absensi, validasi waktu, laporan |
| Feature testing Laravel | Endpoint Sanctum, absensi, log |
| UAT | Penggunaan langsung oleh pengelola SPPG |
| Pengujian akurasi wajah | Persentase keberhasilan verifikasi |
| Pengujian performa | Waktu respons verifikasi dan dashboard |
| Pengujian keamanan | CSRF, rate limit, akses role |

---

## 15. Timeline Pengembangan

| Fase | Aktivitas | Durasi |
|---|---|---|
| Analisis & Desain | Finalisasi PRD, ERD, mockup | 2 minggu |
| Setup & Autentikasi | Laravel 12, Sanctum, Bootstrap, CRUD user | 1 minggu |
| Integrasi Face API | Service embedding dan verifikasi | 2 minggu |
| Pengembangan Absensi | Kamera, verifikasi, timestamp, validasi waktu | 2 minggu |
| Dashboard & Laporan | Chart, filter, ekspor PDF/Excel | 1,5 minggu |
| Testing & UAT | Unit test, feature test, UAT | 1,5 minggu |
| Dokumentasi & Jurnal | Laporan sempro dan artikel jurnal | 2 minggu |
| Total | | ± 12 minggu |

---

## 16. Risiko dan Mitigasi

| Risiko | Dampak | Mitigasi |
|---|---|---|
| Kuota API gratis habis | Pengujian terhenti | Gunakan fallback FaceNet self-hosted atau optimasi panggilan |
| Akurasi rendah saat cahaya buruk | Absensi gagal | Panduan foto, threshold konfigurabel, verifikasi manual admin |
| Internet tidak stabil | Absensi gagal | Pesan error jelas, tidak ada mode offline |
| Kekhawatiran privasi | Relawan menolak | Sosialisasi, consent, enkripsi data |
| Timestamp klien dimanipulasi | Data tidak valid | Gunakan timestamp server sebagai acuan utama |

---

## 17. Kesimpulan

PRD ini menjadi acuan pengembangan aplikasi absensi relawan SPPG Curug Cukanggalih 02 berbasis web dengan Laravel 12, MySQL, Bootstrap 5, dan Sanctum. Fitur utama adalah verifikasi wajah dengan liveness detection dan pencatatan timestamp server untuk menjamin validitas kehadiran. Dokumen ini dapat dilampirkan pada laporan Sempro dan dijadikan dasar penulisan jurnal, terutama pada aspek perancangan sistem, keamanan data biometrik, dan pengujian akurasi verifikasi wajah.
