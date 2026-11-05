@extends('layouts.app')
@section('title', 'Edit Relawan — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.relawan.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="fw-bold mb-0">Edit Data: {{ $relawan->name }}</h4>
        <small class="text-muted">Perbarui data profil, divisi, dan hak akses</small>
    </div>
</div>

<div class="card shadow-sm" style="max-width: 650px">
    <div class="card-body p-4">
        @if($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.relawan.update', $relawan) }}">
            @csrf @method('PUT')
            
            <div class="row g-3 mb-3">
                <div class="col-md-7">
                    <label class="form-label fw-semibold">Nama Lengkap *</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $relawan->name) }}" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-semibold">NIK (KTP)</label>
                    <input type="text" name="nik" class="form-control" value="{{ old('nik', $relawan->nik) }}" maxlength="20">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Divisi Penugasan</label>
                    <select name="division_id" class="form-select">
                        <option value="">-- Pilih Divisi --</option>
                        @foreach($divisions as $div)
                            <option value="{{ $div->id }}" {{ old('division_id', $relawan->division_id) == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">No. WhatsApp *</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $relawan->phone) }}" required>
                </div>
            </div>

            @if($isSuperAdmin)
            <div class="mb-3">
                <label class="form-label fw-semibold">Role Pengguna *</label>
                <select name="role" class="form-select">
                    <option value="relawan" {{ old('role', $relawan->role) === 'relawan' ? 'selected' : '' }}>Relawan</option>
                    <option value="admin" {{ old('role', $relawan->role) === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="super_admin" {{ old('role', $relawan->role) === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                </select>
            </div>
            @endif

            <div class="mb-3">
                <label class="form-label fw-semibold">Alamat</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $relawan->address) }}</textarea>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email Login *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $relawan->email) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password Baru <small class="text-muted">(kosongkan jika tidak diubah)</small></label>
                    <input type="password" name="password" class="form-control" minlength="6">
                </div>
            </div>

            <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                <i class="bi bi-save me-1"></i> Perbarui Data
            </button>
        </form>
    </div>
</div>
@endsection
