# Rekomendasi Pengembangan Sistem Absensi Relawan SPPG Curug Cukanggalih 02
**Dokumen Analisis Fitur Lanjutan & Peningkatan Mutu Skripsi / Jurnal**

---

## 📌 Ringkasan Eksekutif

Sistem dasar absensi berbasis verifikasi wajah (*face recognition*) dan *server timestamp* yang telah dibangun saat ini sudah memenuhi syarat fungsional inti. Namun, untuk kebutuhan operasional nyata di SPPG Curug Cukanggalih 02 serta untuk memperkuat bobot akademis pada **Seminar Proposal (Sempro)** dan **Publikasi Jurnal Ilmiah**, penambahan modul terintegrasi akan membuat sistem ini jauh lebih bernilai.

Ide Anda mengenai **otomatisasi rekap gaji/honor relawan** adalah langkah yang **sangat tepat dan strategis**, karena menghubungkan langsung *kehadiran autentik* dengan *tata kelola kompensasi*. 

Berikut adalah rancangan mendalam modul rekap gaji beserta **6 rekomendasi fitur unggulan** lainnya yang relevan.

---

## 1. Modul Utama Usulan Anda: Otomatisasi Rekap Gaji & Honor Relawan (Automated Payroll)

### 1.1 Latar Belakang & Masalah
Di SPPG (Sentra Pelayanan / Pengelolaan Pertanian / Paguyuban), honor relawan umumnya bersifat harian, per jam kerja (*hourly*), atau berbasis insentif kehadiran/transport. Perhitungan manual dari buku absen sering menimbulkan:
- Salah hitung jam kerja desimal (misal 5 jam 45 menit).
- Pembengkakan anggaran akibat ketidaksesuaian jam masuk/pulang.
- Keterlambatan pembuatan slip honor bulanan bagi bendahara.

### 1.2 Skema Perhitungan yang Fleksibel
Sistem harus mampu mendukung komponen gaji yang dapat dikonfigurasi admin:
1. **Uang Kehadiran / Transport Harian**: Dihitung otomatis per tanggal jika relawan memiliki `check_in_at` dan `check_out_at` yang valid.
2. **Honor Jam Kerja**: Dihitung dari `total_hours * tarif_per_jam`.
3. **Uang Makan / Lembur**: Diberikan jika `total_hours > 8 jam`.
4. **Potongan Keterlambatan / Pulang Cepat**: Mengurangi honor jika check-in melewati batas toleransi keterlambatan.

### 1.3 Rancangan Tabel Database Tambahan
```sql
-- 1. Master Skema Gaji/Honor
CREATE TABLE salary_schemes (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),                -- Contoh: 'Relawan Operasional', 'Relawan Teknis'
    hourly_rate DECIMAL(10,2),        -- Tarif per jam (misal Rp 15.000)
    daily_transport DECIMAL(10,2),    -- Uang transport per hari (misal Rp 20.000)
    meal_allowance DECIMAL(10,2),     -- Uang makan (misal Rp 15.000)
    overtime_hourly_rate DECIMAL(10,2), -- Tarif lembur
    created_at TIMESTAMP
);

-- 2. Rekap Penggajian Periode (Bulanan/Mingguan)
CREATE TABLE payrolls (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    period_start DATE,                -- Tanggal awal (misal 2026-11-01)
    period_end DATE,                  -- Tanggal akhir (misal 2026-11-30)
    status ENUM('draft', 'approved', 'paid') DEFAULT 'draft',
    total_disbursed DECIMAL(12,2) DEFAULT 0,
    created_by BIGINT,                -- Admin pengurus
    created_at TIMESTAMP
);

-- 3. Rincian Gaji per Relawan
CREATE TABLE payroll_details (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    payroll_id BIGINT FOREIGN KEY,
    user_id BIGINT FOREIGN KEY,
    total_days_attended INT,          -- Total hari masuk valid
    total_hours DECIMAL(6,2),         -- Total akumulasi jam kerja
    base_salary DECIMAL(10,2),        -- Total honor jam
    transport_allowance DECIMAL(10,2),-- Total uang transport
    deductions DECIMAL(10,2) DEFAULT 0, -- Potongan
    net_salary DECIMAL(10,2),         -- Total gaji bersih diterima
    payment_status ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    paid_at TIMESTAMP NULL
);
```

### 1.4 Fitur yang Dibangun
- **One-Click Payroll Generator**: Admin cukup memilih bulan (misal: "November 2026"), sistem menghitung seluruh jam kerja relawan dan mengeluarkan kalkulasi total gaji dalam 1 detik.
- **Cetak Slip Gaji Digital (PDF)**: Relawan bisa melihat rincian honor langsung dari dashboard pribadi dan mengunduh slip resmi bertanda tangan bendahara/ketua SPPG.
- **Ekspor Excel Bank Transfer**: Format rekap yang kompatibel untuk transfer kolektif kas daerah/bank.

---

## 2. Fitur Rekomendasi Lanjutan (Peningkat Nilai Ilmiah & Akurasi)

### 💡 Rekomendasi 1: Validasi Geolocation & Geofencing (Radius GPS)
* **Mengapa Sangat Penting?**
  Verifikasi wajah hanya menjamin *siapa yang absen*, bukan *di mana dia berada*. Relawan bisa saja melakukan verifikasi wajah dari rumah atau tempat tidur menggunakan kamera smartphone/laptop.
* **Solusi**:
  Kunci lokasi absensi dengan koordinat SPPG Curug Cukanggalih 02 (Latitude & Longitude). Terapkan radius toleransi (misal 50–100 meter).
* **Nilai Akademis untuk Jurnal**:
  Menggunakan rumus matematis **Haversine Formula** untuk menghitung jarak lengkung bumi antara koordinat perangkat relawan dengan titik pusat SPPG:
  $$d = 2r \arcsin\left(\sqrt{\sin^2\left(\frac{\Delta\phi}{2}\right) + \cos(\phi_1)\cos(\phi_2)\sin^2\left(\frac{\Delta\lambda}{2}\right)}\right)$$
  Ini menjadi kontribusi ilmiah yang kuat untuk bab metode penelitian pada naskah skripsi dan jurnal!

---

### 💡 Rekomendasi 2: Interactive Liveness Detection (Anti-Spoofing Challenge)
* **Mengapa Sangat Penting?**
  Pada sidang Sempro, dosen penguji sering menanyakan: *"Bagaimana jika relawan mengarahkan kamera ke foto cetak atau layar HP rekannya?"*
* **Solusi**:
  Terapkan uji keaktifan interaktif (*challenge-response*):
  - Sistem secara acak meminta aksi: *"Silakan kedipkan mata"* atau *"Tengokkan kepala sedikit ke kanan"* sebelum foto diambil.
  - Atau analisis rasio kedipan mata (*Eye Aspect Ratio / EAR*) menggunakan landmark 68 titik wajah.
* **Nilai untuk Jurnal**:
  Menyajikan tabel hasil uji *Confusion Matrix* (True Positive, False Positive, False Acceptance Rate/FAR, False Rejection Rate/FRR).

---

### 💡 Rekomendasi 3: Modul Pengajuan Izin / Sakit / Cuti (Leave Management)
* **Mengapa Sangat Penting?**
  Relawan yang tidak hadir tidak selalu mangkir (*alpha*). Terkadang relawan sakit, dinas luar, atau berhalangan hadir.
* **Solusi**:
  - Relawan memiliki menu untuk mengunggah surat keterangan dokter atau permohonan izin dengan foto lampiran.
  - Admin melakukan verifikasi (*Approve/Reject*).
  - Terintegrasi langsung ke modul penggajian: relawan yang berstatus *Izin Resmi* tidak dikenai penalti alpha atau tetap memperoleh tunjangan pokok (sesuai kebijakan SPPG).

---

### 💡 Rekomendasi 4: Multi-Shift & Manajemen Penjadwalan (Shift Roster)
* **Mengapa Sangat Penting?**
  Jam operasional SPPG saat ini dibatasi statis (masuk 06:00–08:00 dan pulang setelah 14:00). Pada praktiknya, relawan mungkin terbagi menjadi:
  - Shift Pagi (06:00 – 14:00)
  - Shift Siang/Sore (13:00 – 21:00)
  - Relawan Piket Akhir Pekan
* **Solusi**:
  Tabel jadwal dinamis (*schedules/shifts*) sehingga toleransi keterlambatan dan perhitungan lembur dihitung berdasarkan shift masing-masing relawan.

---

### 💡 Rekomendasi 5: Notifikasi WhatsApp Gateway Otomatis
* **Mengapa Sangat Penting?**
  Relawan sering lupa melakukan absen masuk atau absen pulang.
* **Solusi**:
  Integrasikan gateway WhatsApp (misal menggunakan API Fonnte, Wablas, atau Twilio):
  - **H-15 Menit**: Mengirim pesan pengingat ke WA relawan: *"Halo Budi, waktu absen masuk berakhir 15 menit lagi."*
  - **Pemberitahuan Gaji**: *"Honor Anda periode November 2026 sebesar Rp 1.250.000 telah disetujui. Unduh slip di sini."*
  - **Alert Admin**: Jika ada 3x percobaan wajah gagal berturut-turut pada akun tertentu.

---

### 💡 Rekomendasi 6: Fraud Detection & Anomaly Log (Deteksi Kecurangan)
* **Mengapa Sangat Penting?**
  Menambah reputasi keamanan sistem dalam audit kepengurusan SPPG.
* **Solusi**:
  - **Deteksi Kesamaan Perangkat**: Mendeteksi jika 1 IP address atau 1 User-Agent browser dipakai oleh lebih dari 2 relawan berbeda dalam rentang 3 menit (indikasi 1 perangkat dipakai bergantian oleh rekan relawan).
  - Tampilkan penanda *Flag Warning* warna merah di dashboard admin untuk absensi yang dicurigai.

---

## 3. Matriks Prioritas Pengembangan

| Modul / Fitur | Tingkat Kesulitan | Dampak Pengguna | Relevansi Akademik (Skripsi/Jurnal) | Rekomendasi Prioritas |
|---|---|---|---|---|
| **Otomatisasi Rekap Gaji & Slip PDF** | Sedang | ⭐⭐⭐⭐⭐ (Sangat Tinggi) | ⭐⭐⭐⭐ (Tinggi) | **Prioritas 1 (Wajib Dibuat)** |
| **Geofencing / Radius GPS (Haversine)** | Rendah–Sedang | ⭐⭐⭐⭐⭐ (Sangat Tinggi) | ⭐⭐⭐⭐⭐ (Sangat Kuat) | **Prioritas 1 (Wajib Dibuat)** |
| **Pengajuan Izin / Sakit Relawan** | Rendah | ⭐⭐⭐⭐ (Tinggi) | ⭐⭐⭐ (Sedang) | **Prioritas 2** |
| **Interactive Liveness / Anti-Spoofing** | Sedang–Tinggi | ⭐⭐⭐⭐ (Tinggi) | ⭐⭐⭐⭐⭐ (Sangat Kuat) | **Prioritas 2 (Penting untuk Jurnal)** |
| **Manajemen Shift Kerja Dinamis** | Sedang | ⭐⭐⭐ (Sedang) | ⭐⭐⭐ (Sedang) | **Prioritas 3** |
| **Notifikasi WhatsApp Gateway** | Rendah | ⭐⭐⭐⭐ (Tinggi) | ⭐⭐ (Pelengkap) | **Prioritas 3** |
| **Fraud Anomaly Detection** | Sedang | ⭐⭐⭐ (Sedang) | ⭐⭐⭐⭐ (Tinggi) | **Prioritas 4** |

---

## 4. Gambaran Alur Integrasi (Absensi ➡️ Gaji)

```text
[ Relawan Absen Masuk & Pulang ]
      │
      ├── Verifikasi Wajah (Face Recognition)
      ├── Validasi Koordinat GPS (Radius SPPG)
      └── Timestamp Server Waktu Nyata
      │
      ▼
[ Tabel Attendances: check_in, check_out, total_hours ]
      │
      ▼
[ Akhir Bulan: Admin Klik "Generate Payroll" ]
      │
      ├── Ambil Total Hari Masuk
      ├── Hitung Akumulasi Total Jam Kerja
      ├── Tambah Uang Transport & Uang Makan
      ├── Kurangi Keterlambatan / Izin Tanpa Keterangan
      │
      ▼
[ Tabel Payrolls & Payroll Details ]
      │
      ├── Admin: Approve Pembayaran & Cetak Rekapitulasi Kas
      └── Relawan: Unduh Slip Gaji PDF Resmi
```

---

## 5. Kesimpulan & Saran Langkah Berikutnya

Jika Anda ingin mengangkat sistem ini menjadi **skripsi berkualitas tinggi dan tembus jurnal nasional (SINTA)**:
1. **Gabungkan Rekap Gaji + Geofencing GPS** sebagai nilai jual utama. Kombinasi verifikasi wajah, validasi lokasi GPS, dan kalkulasi honor otomatis memberikan solusi bisnis yang tuntas dan memiliki metodologi ilmiah yang jelas.
2. Dosen penguji dan reviewer jurnal akan sangat mengapresiasi karena sistem tidak hanya berhenti di "pencatatan absensi", melainkan sampai ke *"Decision Support & Financial Accountability"* bagi pengelola SPPG Curug Cukanggalih 02.

