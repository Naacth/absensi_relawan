@extends('layouts.app')
@section('title', 'Edit Divisi & Jadwal — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.divisions.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="fw-bold mb-0">Pengaturan Divisi: {{ $division->name }}</h4>
        <small class="text-muted">Konfigurasi nama divisi dan jadwal kerja per hari</small>
    </div>
</div>

<form method="POST" action="{{ route('admin.divisions.update', $division) }}">
    @csrf @method('PUT')

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-bold">Informasi Divisi</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Divisi *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $division->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $division->description) }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3 fw-bold">Jadwal Kerja per Hari</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-bordered align-middle mb-0 text-center">
                            <thead class="table-light">
                                <tr class="small">
                                    <th>Hari</th>
                                    <th>Buka Masuk</th>
                                    <th>Tutup Masuk</th>
                                    <th>Buka Pulang</th>
                                    <th>Tutup Pulang</th>
                                    <th>Toleransi (Mnt)</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($division->schedules->sortBy('day_of_week') as $sched)
                            <tr>
                                <td class="fw-bold">{{ $sched->day_name }}</td>
                                <td>
                                    <input type="time" name="schedules[{{ $sched->id }}][check_in_start]" class="form-control form-control-sm" value="{{ substr($sched->check_in_start, 0, 5) }}" required>
                                </td>
                                <td>
                                    <input type="time" name="schedules[{{ $sched->id }}][check_in_end]" class="form-control form-control-sm" value="{{ substr($sched->check_in_end, 0, 5) }}" required>
                                </td>
                                <td>
                                    <input type="time" name="schedules[{{ $sched->id }}][check_out_start]" class="form-control form-control-sm" value="{{ substr($sched->check_out_start, 0, 5) }}" required>
                                </td>
                                <td>
                                    <input type="time" name="schedules[{{ $sched->id }}][check_out_end]" class="form-control form-control-sm" value="{{ substr($sched->check_out_end, 0, 5) }}" required>
                                </td>
                                <td>
                                    <input type="number" name="schedules[{{ $sched->id }}][late_tolerance]" class="form-control form-control-sm text-center" value="{{ $sched->late_tolerance }}" min="0" max="120" style="width: 70px; margin: auto;" required>
                                </td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
        <i class="bi bi-save me-1"></i> Simpan Divisi & Jadwal Kerja
    </button>
</form>
@endsection

