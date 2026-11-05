@extends('layouts.app')
@section('title', 'Slip Honor Saya — SPPG')

@section('sidebar')
    @include('layouts.relawan_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-receipt me-2 text-primary"></i>Slip Honorarium Digital Saya</h4>
        <small class="text-muted">Unduh dan pantau riwayat kompensasi kehadiran Anda di SPPG Curug Cukanggalih 02</small>
    </div>
</div>

<div class="row g-3">
    @forelse($slips as $s)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100 border-start border-4 {{ $s->payment_status === 'paid' ? 'border-success' : 'border-primary' }}">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold small text-dark text-truncate">{{ $s->payroll->title }}</span>
                @if($s->payment_status === 'paid')
                    <span class="badge bg-success">LUNAS</span>
                @else
                    <span class="badge bg-primary">APPROVED</span>
                @endif
            </div>
            <div class="card-body">
                <div class="text-muted small mb-1">Periode Kehadiran:</div>
                <div class="fw-semibold small mb-3">
                    {{ $s->payroll->period_start->translatedFormat('d M Y') }} - {{ $s->payroll->period_end->translatedFormat('d M Y') }}
                </div>

                <div class="bg-light p-2 rounded mb-3 small">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Total Hadir:</span>
                        <strong>{{ $s->total_days_attended }} hari</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span>Total Jam Kerja:</span>
                        <strong>{{ number_format($s->total_hours, 1) }} jam</strong>
                    </div>
                    <div class="d-flex justify-content-between text-success fw-bold border-top pt-1 mt-1">
                        <span>Total Bersih:</span>
                        <span class="fs-6">Rp {{ number_format($s->net_salary, 0, ',', '.') }}</span>
                    </div>
                </div>

                <a href="{{ route('relawan.payroll.slip', $s) }}" class="btn btn-outline-danger w-100 btn-sm">
                    <i class="bi bi-file-earmark-pdf me-1"></i> Unduh Slip Gaji (PDF)
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">
        <i class="bi bi-receipt fs-1 d-block text-secondary mb-2"></i>
        Belum ada slip honorarium yang disetujui pada akun Anda.
    </div>
    @endforelse
</div>

@if($slips->hasPages())
<div class="mt-4">{{ $slips->links() }}</div>
@endif
@endsection

