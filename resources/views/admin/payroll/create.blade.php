@extends('layouts.app')
@section('title', 'Generate Payroll Baru — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="fw-bold mb-0">Generate Payroll Baru</h4>
        <small class="text-muted">Pilih rentang tanggal untuk menghitung seluruh honor relawan secara otomatis</small>
    </div>
</div>

<div class="card shadow-sm" style="max-width: 650px;">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('admin.payroll.generate') }}">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Judul Payroll (Opsional)</label>
                <input type="text" name="title" class="form-control" placeholder="Contoh: Payroll Relawan Periode November 2026">
                <small class="text-muted">Biarkan kosong untuk judul otomatis berdasarkan rentang tanggal.</small>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Mulai Periode *</label>
                    <input type="date" name="period_start" class="form-control" value="{{ now()->startOfMonth()->toDateString() }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Selesai Periode *</label>
                    <input type="date" name="period_end" class="form-control" value="{{ now()->endOfMonth()->toDateString() }}" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Catatan / Keterangan Tambahan</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan internal pengurus/bendahara..."></textarea>
            </div>

            <div class="alert alert-info border-info small mb-4">
                <i class="bi bi-info-circle me-1"></i>
                <strong>Proses Otomatis:</strong> Sistem akan mengumpulkan data seluruh jam kerja relawan aktif dari tabel <code>attendances</code>, mengkalkulasi tunjangan transport harian, uang makan, lembur, dan izin resmi yang telah disetujui. Payroll yang dihasilkan berstatus <strong>Draft</strong> dan dapat ditinjau terlebih dahulu sebelum di-approve.
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="bi bi-gear-wide-connected me-1"></i> Kalkulasi & Generate Payroll
            </button>
        </form>
    </div>
</div>
@endsection

