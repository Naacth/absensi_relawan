@extends('layouts.app')
@section('title', 'Edit Divisi & Jadwal — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.divisions.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0">Pengaturan Divisi: {{ $division->name }}</h4>
        <small class="text-muted">Konfigurasi nama, jadwal kerja, dan komponen honor divisi</small>
    </div>
</div>

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.divisions.update', $division) }}">
    @csrf @method('PUT')

    {{-- ROW 1: Informasi Divisi + Jadwal Kerja --}}
    <div class="row g-4 mb-4">
        {{-- Informasi Divisi --}}
        <div class="col-lg-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-diagram-3 me-1 text-primary"></i> Informasi Divisi
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Divisi <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $division->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="Uraian tugas divisi...">{{ old('description', $division->description) }}</textarea>
                    </div>
                    {{-- Honor Harian --}}
                    <div class="mb-0">
                        <label class="form-label fw-semibold">
                            <i class="bi bi-cash-coin me-1 text-success"></i>
                            Honor Harian <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">Rp</span>
                            <input type="number" name="daily_rate"
                                   class="form-control @error('daily_rate') is-invalid @enderror"
                                   value="{{ old('daily_rate', (int)$division->daily_rate) }}"
                                   min="0" step="1000" placeholder="Contoh: 75000" required>
                            <span class="input-group-text text-muted">/ hari hadir</span>
                        </div>
                        <small class="text-muted">Honor pokok: tarif ini × jumlah hari hadir relawan.</small>
                        @error('daily_rate')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Jadwal Kerja --}}
        <div class="col-lg-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3 fw-bold">
                    <i class="bi bi-clock me-1 text-primary"></i> Jadwal Kerja Harian
                    <span class="badge bg-info-subtle text-info border border-info ms-2 fw-normal small">Berlaku setiap hari</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 bg-success-subtle rounded border border-success-subtle">
                                <div class="fw-semibold text-success mb-2">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Jam Absen Masuk
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold mb-1">Buka</label>
                                        <input type="time" name="check_in_start"
                                               class="form-control @error('check_in_start') is-invalid @enderror"
                                               value="{{ old('check_in_start', substr($schedule->check_in_start, 0, 5)) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold mb-1">Tutup</label>
                                        <input type="time" name="check_in_end"
                                               class="form-control @error('check_in_end') is-invalid @enderror"
                                               value="{{ old('check_in_end', substr($schedule->check_in_end, 0, 5)) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-danger-subtle rounded border border-danger-subtle">
                                <div class="fw-semibold text-danger mb-2">
                                    <i class="bi bi-box-arrow-right me-1"></i> Jam Absen Pulang
                                </div>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold mb-1">Buka</label>
                                        <input type="time" name="check_out_start"
                                               class="form-control @error('check_out_start') is-invalid @enderror"
                                               value="{{ old('check_out_start', substr($schedule->check_out_start, 0, 5)) }}" required>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-semibold mb-1">Tutup</label>
                                        <input type="time" name="check_out_end"
                                               class="form-control @error('check_out_end') is-invalid @enderror"
                                               value="{{ old('check_out_end', substr($schedule->check_out_end, 0, 5)) }}" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-warning-subtle rounded border border-warning-subtle">
                                <div class="fw-semibold text-warning-emphasis mb-2">
                                    <i class="bi bi-hourglass-split me-1"></i> Toleransi Keterlambatan
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <input type="number" name="late_tolerance"
                                           class="form-control @error('late_tolerance') is-invalid @enderror"
                                           value="{{ old('late_tolerance', $schedule->late_tolerance) }}"
                                           min="0" max="120" style="max-width: 120px;" required>
                                    <span class="text-muted">menit setelah jam buka masuk</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ROW 2: Komponen Honor (Transport, Makan, Lembur, Potongan) --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 fw-bold">
            <i class="bi bi-wallet2 me-1 text-warning"></i> Komponen Honor Tambahan
            <span class="badge bg-secondary-subtle text-muted border fw-normal ms-2 small">
                Ditambahkan di atas honor harian saat generate payroll
            </span>
        </div>
        <div class="card-body">
            <div class="row g-3">
                {{-- Transport --}}
                <div class="col-md-3">
                    <div class="p-3 bg-primary-subtle rounded border border-primary-subtle h-100">
                        <label class="form-label fw-semibold text-primary small">
                            <i class="bi bi-bus-front me-1"></i> Uang Transport
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-primary-subtle text-primary fw-bold">Rp</span>
                            <input type="number" name="daily_transport"
                                   class="form-control @error('daily_transport') is-invalid @enderror"
                                   value="{{ old('daily_transport', (int)$division->daily_transport) }}"
                                   min="0" step="1000" placeholder="0">
                        </div>
                        <small class="text-muted d-block mt-1">Per hari hadir</small>
                    </div>
                </div>

                {{-- Uang Makan --}}
                <div class="col-md-3">
                    <div class="p-3 bg-success-subtle rounded border border-success-subtle h-100">
                        <label class="form-label fw-semibold text-success small">
                            <i class="bi bi-egg-fried me-1"></i> Uang Makan
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">Rp</span>
                            <input type="number" name="meal_allowance"
                                   class="form-control @error('meal_allowance') is-invalid @enderror"
                                   value="{{ old('meal_allowance', (int)$division->meal_allowance) }}"
                                   min="0" step="1000" placeholder="0">
                        </div>
                        <small class="text-muted d-block mt-1">Per hari ≥ 6 jam kerja</small>
                    </div>
                </div>

                {{-- Lembur --}}
                <div class="col-md-3">
                    <div class="p-3 bg-warning-subtle rounded border border-warning-subtle h-100">
                        <label class="form-label fw-semibold text-warning-emphasis small">
                            <i class="bi bi-clock-history me-1"></i> Tarif Lembur
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-warning-subtle text-warning-emphasis fw-bold">Rp</span>
                            <input type="number" name="overtime_hourly_rate"
                                   class="form-control @error('overtime_hourly_rate') is-invalid @enderror"
                                   value="{{ old('overtime_hourly_rate', (int)$division->overtime_hourly_rate) }}"
                                   min="0" step="1000" placeholder="0">
                        </div>
                        <small class="text-muted d-block mt-1">Per jam (jam &gt; 8/hari)</small>
                    </div>
                </div>

                {{-- Potongan --}}
                <div class="col-md-3">
                    <div class="p-3 bg-danger-subtle rounded border border-danger-subtle h-100">
                        <label class="form-label fw-semibold text-danger small">
                            <i class="bi bi-dash-circle me-1"></i> Potongan Telat
                        </label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-danger-subtle text-danger fw-bold">Rp</span>
                            <input type="number" name="late_deduction"
                                   class="form-control @error('late_deduction') is-invalid @enderror"
                                   value="{{ old('late_deduction', (int)$division->late_deduction) }}"
                                   min="0" step="1000" placeholder="0">
                        </div>
                        <small class="text-muted d-block mt-1">Per kejadian terlambat</small>
                    </div>
                </div>
            </div>

            {{-- Preview Total Honor --}}
            <div class="mt-3 p-3 bg-light rounded border small">
                <div class="fw-semibold text-muted mb-1"><i class="bi bi-calculator me-1"></i> Estimasi Honor 1 Hari Penuh (≥ 6 jam, tidak telat):</div>
                <span class="fs-5 fw-bold text-success" id="previewTotal">Rp 0</span>
                <span class="text-muted ms-2" id="previewDetail"></span>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Simpan Divisi & Jadwal Kerja
        </button>
        <a href="{{ route('admin.divisions.index') }}" class="btn btn-outline-secondary px-4 py-2">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
function formatRp(n) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(n || 0));
}

function updatePreview() {
    const honor     = parseFloat(document.querySelector('[name=daily_rate]').value)           || 0;
    const transport = parseFloat(document.querySelector('[name=daily_transport]').value)       || 0;
    const makan     = parseFloat(document.querySelector('[name=meal_allowance]').value)        || 0;
    const total     = honor + transport + makan;

    document.getElementById('previewTotal').textContent  = formatRp(total);
    document.getElementById('previewDetail').textContent =
        `(Honor ${formatRp(honor)} + Transport ${formatRp(transport)} + Makan ${formatRp(makan)})`;
}

document.addEventListener('DOMContentLoaded', function () {
    ['daily_rate','daily_transport','meal_allowance','overtime_hourly_rate','late_deduction']
        .forEach(n => document.querySelector(`[name=${n}]`)?.addEventListener('input', updatePreview));
    updatePreview();
});
</script>
@endpush
