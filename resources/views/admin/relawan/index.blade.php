@extends('layouts.app')
@section('title', 'Data Relawan & Pengguna — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-people me-2 text-primary"></i>Data Relawan & Pengguna</h4>
        <small class="text-muted">Kelola akun, divisi, nomor WhatsApp, dan status biometrik wajah</small>
    </div>
    <a href="{{ route('admin.relawan.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Tambah Relawan / User
    </a>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-center">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama atau NIK..." value="{{ request('search') }}">
            </div>
            <div class="col-md-4">
                <select name="division_id" class="form-select form-select-sm">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button class="btn btn-sm btn-outline-primary flex-grow-1"><i class="bi bi-search me-1"></i>Filter</button>
                <a href="{{ route('admin.relawan.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama & NIK</th>
                        <th>Divisi</th>
                        <th>Kontak (WA)</th>
                        <th>Role</th>
                        <th>Wajah (ARSA)</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($relawan as $r)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $r->name }}</div>
                        <small class="text-muted">{{ $r->nik ?: 'NIK belum diisi' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-dark border">
                            {{ $r->division?->name ?? 'Belum Ditentukan' }}
                        </span>
                    </td>
                    <td>
                        <div class="small"><i class="bi bi-envelope me-1"></i>{{ $r->email }}</div>
                        <div class="small text-muted"><i class="bi bi-whatsapp me-1 text-success"></i>{{ $r->phone ?: '-' }}</div>
                    </td>
                    <td>
                        <span class="badge bg-{{ $r->isSuperAdmin() ? 'danger' : ($r->isAdmin() ? 'primary' : 'secondary') }}">
                            {{ strtoupper(str_replace('_', ' ', $r->role)) }}
                        </span>
                    </td>
                    <td>
                        @if($r->face_enrolled)
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Terdaftar</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Belum</span>
                        @endif
                    </td>
                    <td>
                        @if($r->is_active)
                            <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Nonaktif</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.relawan.edit', $r) }}" class="btn btn-outline-secondary" title="Edit Data"><i class="bi bi-pencil"></i></a>
                            <form method="POST" action="{{ route('admin.relawan.toggleActive', $r) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-outline-{{ $r->is_active ? 'warning' : 'success' }}" title="{{ $r->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                    <i class="bi bi-{{ $r->is_active ? 'pause' : 'play' }}"></i>
                                </button>
                            </form>
                            @if($r->face_enrolled)
                            <form method="POST" action="{{ route('admin.relawan.resetWajah', $r) }}" class="d-inline"
                                  onsubmit="return confirm('Reset pendaftaran wajah {{ $r->name }}?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger" title="Reset Wajah"><i class="bi bi-camera-video-off"></i></button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data pengguna / relawan.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($relawan->hasPages())
    <div class="card-footer bg-white">{{ $relawan->links() }}</div>
    @endif
</div>
@endsection
