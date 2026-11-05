@extends('layouts.app')
@section('title', 'Pengaturan Sistem — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-sliders me-2 text-primary"></i>Pengaturan Sistem & Master Gaji</h4>
        <small class="text-muted">Konfigurasi Geofencing, Layanan Cloud API, dan Komponen Honor Relawan</small>
    </div>
</div>

<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf

    <div class="row g-4">
        {{-- Geofencing & Koordinat SPPG --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold text-dark"><i class="bi bi-geo-alt-fill text-danger me-1"></i> Lokasi SPPG & Geofencing</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Instansi / Titik Lokasi</label>
                        <input type="text" name="sppg_name" class="form-control" value="{{ old('sppg_name', $settings['sppg_name'] ?? 'SPPG Curug Cukanggalih 02') }}" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Latitude Titik SPPG</label>
                            <input type="text" name="sppg_latitude" class="form-control" value="{{ old('sppg_latitude', $settings['sppg_latitude'] ?? '-6.262500') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Longitude Titik SPPG</label>
                            <input type="text" name="sppg_longitude" class="form-control" value="{{ old('sppg_longitude', $settings['sppg_longitude'] ?? '106.564700') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Radius Toleransi Geofencing (Meter)</label>
                        <div class="input-group">
                            <input type="number" name="geofence_radius" class="form-control" value="{{ old('geofence_radius', $settings['geofence_radius'] ?? '100') }}" min="10" max="5000" required>
                            <span class="input-group-text">Meter</span>
                        </div>
                        <small class="text-muted">Jarak maksimal relawan dari titik SPPG yang diizinkan untuk absensi.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Skema Honor & Payroll --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold text-dark"><i class="bi bi-cash-coin text-success me-1"></i> Skema Honorarium Relawan</span>
                </div>
                <div class="card-body">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Honor per Jam Kerja</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="hourly_rate" class="form-control" value="{{ old('hourly_rate', $salaryScheme->hourly_rate ?? 15000) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Uang Transport / Hari Hadir</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="daily_transport" class="form-control" value="{{ old('daily_transport', $salaryScheme->daily_transport ?? 20000) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Uang Makan (Hadir &ge; 6 Jam)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="meal_allowance" class="form-control" value="{{ old('meal_allowance', $salaryScheme->meal_allowance ?? 15000) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tarif Lembur (> 8 Jam / Jam)</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="overtime_hourly_rate" class="form-control" value="{{ old('overtime_hourly_rate', $salaryScheme->overtime_hourly_rate ?? 20000) }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Potongan Keterlambatan</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="late_deduction" class="form-control" value="{{ old('late_deduction', $salaryScheme->late_deduction ?? 5000) }}" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- API Keys (ARSA & Fonnte) --}}
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold text-dark"><i class="bi bi-cloud-check text-primary me-1"></i> Kunci Akses Cloud API Pihak Ketiga</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">ARSA Face API Key</label>
                            <input type="password" name="arsa_api_key" class="form-control font-monospace" value="{{ old('arsa_api_key', $settings['arsa_api_key'] ?? '') }}" placeholder="Kosongkan jika menggunakan FACE_API_MODE=dummy">
                            <small class="text-muted">Untuk verifikasi wajah & anti-spoofing liveness.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Fonnte WhatsApp API Token</label>
                            <input type="password" name="fonnte_api_token" class="form-control font-monospace" value="{{ old('fonnte_api_token', $settings['fonnte_api_token'] ?? '') }}" placeholder="Token akun Fonnte Anda">
                            <small class="text-muted">Untuk pengiriman otomatis notifikasi WA slip gaji & alert fraud.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Simpan Perubahan Pengaturan
        </button>
    </div>
</form>
@endsection

