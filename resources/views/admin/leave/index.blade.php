@extends('layouts.app')
@section('title', 'Persetujuan Izin & Sakit — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-envelope-paper me-2 text-primary"></i>Permohonan Izin / Sakit / Cuti</h4>
        <small class="text-muted">Verifikasi pengajuan ketidakhadiran relawan agar tidak dihitung alpha pada penggajian</small>
    </div>
</div>

{{-- Filter Status --}}
<div class="card shadow-sm mb-4">
    <div class="card-body py-2">
        <div class="nav nav-pills small">
            <a class="nav-link {{ $status === 'pending' ? 'active' : '' }}" href="{{ route('admin.leave.index', ['status' => 'pending']) }}">
                <i class="bi bi-hourglass-split me-1"></i>Menunggu Verifikasi
            </a>
            <a class="nav-link {{ $status === 'approved' ? 'active' : '' }}" href="{{ route('admin.leave.index', ['status' => 'approved']) }}">
                <i class="bi bi-check-circle me-1"></i>Disetujui
            </a>
            <a class="nav-link {{ $status === 'rejected' ? 'active' : '' }}" href="{{ route('admin.leave.index', ['status' => 'rejected']) }}">
                <i class="bi bi-x-circle me-1"></i>Ditolak
            </a>
            <a class="nav-link {{ $status === 'all' ? 'active' : '' }}" href="{{ route('admin.leave.index', ['status' => 'all']) }}">
                Semua Riwayat
            </a>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Tgl Pengajuan</th>
                        <th>Relawan & Divisi</th>
                        <th>Jenis</th>
                        <th>Rentang Tanggal</th>
                        <th>Alasan</th>
                        <th>Dokumen / Bukti</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($requests as $req)
                <tr>
                    <td><small>{{ $req->created_at->format('d/m/Y H:i') }}</small></td>
                    <td>
                        <div class="fw-bold">{{ $req->user->name }}</div>
                        <small class="text-muted">{{ $req->user->division?->name ?? 'Umum' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-{{ $req->type === 'sakit' ? 'danger' : ($req->type === 'cuti' ? 'info' : 'warning text-dark') }}">
                            {{ strtoupper($req->type) }}
                        </span>
                    </td>
                    <td>
                        <div class="small fw-semibold">{{ $req->start_date->format('d M Y') }}</div>
                        <small class="text-muted">s/d {{ $req->end_date->format('d M Y') }}</small>
                    </td>
                    <td style="max-width: 250px;">
                        <small>{{ $req->reason }}</small>
                        @if($req->rejection_note)
                            <div class="text-danger small mt-1"><strong>Alasan Tolak:</strong> {{ $req->rejection_note }}</div>
                        @endif
                    </td>
                    <td>
                        @if($req->attachment_path)
                            <a href="{{ storage_url($req->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="bi bi-paperclip me-1"></i>Lihat Bukti
                            </a>
                        @else
                            <span class="text-muted small">-</span>
                        @endif
                    </td>
                    <td>
                        @if($req->status === 'approved')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                        @elseif($req->status === 'rejected')
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Pending</span>
                        @endif
                    </td>
                    <td>
                        @if($req->status === 'pending')
                        <div class="btn-group btn-group-sm">
                            <form method="POST" action="{{ route('admin.leave.approve', $req) }}" class="d-inline">
                                @csrf @method('PATCH')
                                <button class="btn btn-success" title="Setujui" onclick="return confirm('Setujui permohonan izin ini?')">
                                    <i class="bi bi-check-lg"></i>
                                </button>
                            </form>
                            <button type="button" class="btn btn-danger" title="Tolak" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $req->id }}">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>

                        {{-- Modal Tolak --}}
                        <div class="modal fade" id="rejectModal{{ $req->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.leave.reject', $req) }}">
                                        @csrf @method('PATCH')
                                        <div class="modal-header">
                                            <h6 class="modal-title fw-bold">Tolak Permohonan {{ $req->user->name }}</h6>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <label class="form-label small fw-semibold">Alasan Penolakan *</label>
                                            <textarea name="rejection_note" class="form-control" rows="3" required placeholder="Contoh: Bukti surat dokter tidak jelas / jadwal mendesak"></textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-sm btn-danger">Tolak Izin</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @else
                            <small class="text-muted">Diproses oleh {{ $req->reviewer?->name ?? 'Admin' }}</small>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Tidak ada permohonan izin pada status ini.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($requests->hasPages())
    <div class="card-footer bg-white">{{ $requests->links() }}</div>
    @endif
</div>
@endsection

