@extends('layouts.app')
@section('title', 'Tambah Relawan / User — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.relawan.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="fw-bold mb-0">Tambah Relawan / Pengguna Baru</h4>
        <small class="text-muted">Isi data akun relawan, divisi, dan nomor WhatsApp aktif</small>
    </div>
</div>

<div class="card shadow-sm" style="max-width: 650px">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.relawan.store') }}">
            @csrf
            
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">NIK (KTP)</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik') }}" maxlength="20" placeholder="16 digit">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Divisi Penugasan</label>
                    <select name="division_id" class="form-select">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. WhatsApp *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxxxxxxxxx" required>
                    <small class="text-muted">Untuk notifikasi slip honor & pengingat</small>
                </div>
            </div>

            @if($isSuperAdmin)
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Pengguna *</label>
                <select name="role" class="form-select">
                    <option value="relawan" {{ old('role') === 'relawan' ? 'selected' : '' }}>Relawan (Akses Absensi & Slip)</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Pengelola Absensi & Divisi)</option>
                    <option value="super_admin" {{ old('role') === 'super_admin' ? 'selected' : '' }}>Super Admin (Akses Penuh & Pengaturan)</option>
                </select>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Alamat Tempat Tinggal</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Login *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password Awal *</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="bi bi-save me-1"></i> Simpan Relawan / User
            </button>
        </form>
    </div>
</div>
@endsection
