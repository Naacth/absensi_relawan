@extends('layouts.app')
@section('title', 'Riwayat Izin & Sakit — SPPG')

@section('sidebar')
    @include('layouts.relawan_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-envelope-paper me-2 text-primary"></i>Pengajuan Izin / Sakit / Cuti</h4>
        <small class="text-muted">Pantau status persetujuan permohonan ketidakhadiran Anda</small>
    </div>
    <a href="{{ route('relawan.leave.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-lg me-1"></i> Ajukan Permohonan Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light small">
                    <tr>
                        <th class="text-start">Tanggal Pengajuan</th>
                        <th>Jenis</th>
                        <th>Periode</th>
                        <th class="text-start">Alasan</th>
                        <th>Dokumen / Bukti</th>
                        <th>Status</th>
                        <th>Keterangan Admin</th>
                    </tr>
                </thead>
                <tbody class="small">
                @forelse($leaves as $l)
                <tr>
                    <td class="text-start">{{ $l->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <span class="badge bg-{{ $l->type === 'sakit' ? 'danger' : ($l->type === 'cuti' ? 'info' : 'warning text-dark') }} text-uppercase">
                            {{ $l->type }}
                        </span>
                    </td>
                    <td class="fw-semibold">
                        {{ $l->start_date->format('d M Y') }} s/d {{ $l->end_date->format('d M Y') }}
                    </td>
                    <td class="text-start" style="max-width: 200px;">{{ $l->reason }}</td>
                    <td>
                        @if($l->attachment_path)
                            <a href="{{ asset('storage/' . $l->attachment_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                <i class="bi bi-paperclip me-1"></i>Lihat
                            </a>
                        @else -
                        @endif
                    </td>
                    <td>
                        @if($l->status === 'approved')
                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Disetujui</span>
                        @elseif($l->status === 'rejected')
                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Ditolak</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i>Menunggu</span>
                        @endif
                    </td>
                    <td>
                        <small class="text-muted">{{ $l->rejection_note ?: ($l->status === 'approved' ? 'Telah diverifikasi' : '-') }}</small>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-muted py-5 text-center">Belum ada riwayat permohonan izin.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($leaves->hasPages())
    <div class="card-footer bg-white">{{ $leaves->links() }}</div>
    @endif
</div>
@endsection

