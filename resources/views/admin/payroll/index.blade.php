@extends('layouts.app')
@section('title', 'Payroll & Penggajian Relawan — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-cash-stack me-2 text-primary"></i>Daftar Rekapitulasi Payroll & Honor</h4>
        <small class="text-muted">Kalkulasi honor otomatis berbasis kehadiran valid, jam kerja, dan tunjangan harian</small>
    </div>
    <a href="{{ route('admin.payroll.create') }}" class="btn btn-primary">
        <i class="bi bi-calculator me-1"></i> Generate Payroll Baru
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Judul & Periode</th>
                        <th>Jumlah Relawan</th>
                        <th>Total Honor Dicairkan</th>
                        <th>Status</th>
                        <th>Dibuat Oleh</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($payrolls as $p)
                <tr>
                    <td>
                        <div class="fw-bold fs-6">{{ $p->title }}</div>
                        <small class="text-muted">
                            <i class="bi bi-calendar-range me-1"></i>
                            {{ $p->period_start->translatedFormat('d M Y') }} s/d {{ $p->period_end->translatedFormat('d M Y') }}
                        </small>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-dark border">
                            {{ $p->details_count }} Relawan
                        </span>
                    </td>
                    <td>
                        <span class="fw-bold fs-6 text-success">
                            Rp {{ number_format($p->total_disbursed, 0, ',', '.') }}
                        </span>
                    </td>
                    <td>
                        @if($p->status === 'paid')
                            <span class="badge bg-success"><i class="bi bi-check-all me-1"></i>Lunas (Paid)</span>
                        @elseif($p->status === 'approved')
                            <span class="badge bg-primary"><i class="bi bi-check-circle me-1"></i>Disetujui (Approved)</span>
                        @else
                            <span class="badge bg-warning text-dark"><i class="bi bi-pencil me-1"></i>Draft</span>
                        @endif
                    </td>
                    <td>
                        <small>{{ $p->creator?->name ?? 'Admin' }}</small>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ $p->created_at->format('d/m/Y') }}</div>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.payroll.show', $p) }}" class="btn btn-outline-primary" title="Lihat Rincian">
                                <i class="bi bi-eye me-1"></i> Rincian
                            </a>
                            <a href="{{ route('admin.payroll.exportCsv', $p) }}" class="btn btn-outline-success" title="Ekspor CSV/Excel">
                                <i class="bi bi-file-earmark-excel"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-5">
                        <i class="bi bi-receipt fs-1 d-block text-secondary mb-2"></i>
                        Belum ada data payroll yang digenerate.<br>
                        <a href="{{ route('admin.payroll.create') }}" class="btn btn-sm btn-primary mt-2">Generate Payroll Sekarang</a>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($payrolls->hasPages())
    <div class="card-footer bg-white">{{ $payrolls->links() }}</div>
    @endif
</div>
@endsection

