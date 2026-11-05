@extends('layouts.app')
@section('title', 'Ajukan Izin / Sakit — SPPG')

@section('sidebar')
    @include('layouts.relawan_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('relawan.leave.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="fw-bold mb-0">Formulir Pengajuan Izin / Sakit / Cuti</h4>
        <small class="text-muted">Isi keterangan dan lampirkan surat dokter jika berhalangan hadir</small>
    </div>
</div>

<div class="card shadow-sm" style="max-width: 600px;">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('relawan.leave.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-semibold">Jenis Permohonan *</label>
                <select name="type" class="form-select" required>
                    <option value="izin" {{ old('type') === 'izin' ? 'selected' : '' }}>Izin Keperluan Mendesak</option>
                    <option value="sakit" {{ old('type') === 'sakit' ? 'selected' : '' }}>Sakit (Wajib Surat Dokter jika > 1 hari)</option>
                    <option value="cuti" {{ old('type') === 'cuti' ? 'selected' : '' }}>Cuti Terjadwal</option>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Mulai *</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', today()->toDateString()) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Tanggal Selesai *</label>
                    <input type="date" name="end_date" class="form-control" value="{{ old('end_date', today()->toDateString()) }}" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Alasan / Penjelasan *</label>
                <textarea name="reason" class="form-control" rows="3" placeholder="Jelaskan alasan ketidakhadiran Anda..." required>{{ old('reason') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Lampiran Bukti (Opsional)</label>
                <input type="file" name="attachment" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                <small class="text-muted">Foto surat dokter atau surat keterangan (Format: JPG, PNG, PDF maks. 3MB)</small>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="bi bi-send me-1"></i> Kirim Permohonan Izin
            </button>
        </form>
    </div>
</div>
@endsection

