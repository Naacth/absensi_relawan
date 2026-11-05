@extends('layouts.app')
@section('title', 'Rincian Payroll — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
        <div>
            <h4 class="fw-bold mb-0">{{ $payroll->title }}</h4>
            <small class="text-muted">
                Periode: {{ $payroll->period_start->translatedFormat('d F Y') }} s/d {{ $payroll->period_end->translatedFormat('d F Y') }} &bull;
                Status: 
                @if($payroll->status === 'paid')
                    <span class="badge bg-success">LUNAS</span>
                @elseif($payroll->status === 'approved')
                    <span class="badge bg-primary">APPROVED</span>
                @else
                    <span class="badge bg-warning text-dark">DRAFT</span>
                @endif
            </small>
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.payroll.exportCsv', $payroll) }}" class="btn btn-outline-success">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor CSV
        </a>

        @if($payroll->status === 'draft')
        <form method="POST" action="{{ route('admin.payroll.approve', $payroll) }}" onsubmit="return confirm('Setujui payroll ini? Sistem otomatis mengirimkan pesan WhatsApp slip gaji ke seluruh relawan.')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle me-1"></i> Approve & Kirim WA Relawan
            </button>
        </form>
        @elseif($payroll->status === 'approved')
        <form method="POST" action="{{ route('admin.payroll.markPaid', $payroll) }}" onsubmit="return confirm('Tandai seluruh honor pada periode ini telah ditransfer / dibayarkan?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success">
                <i class="bi bi-cash me-1"></i> Tandai Sudah Lunas (Paid)
            </button>
        </form>
        @endif
    </div>
</div>

{{-- Ringkasan Total --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-start border-4 border-success">
            <div class="text-muted small fw-semibold">TOTAL DANA DICAIRKAN</div>
            <div class="fs-3 fw-bold text-success">Rp {{ number_format($payroll->total_disbursed, 0, ',', '.') }}</div>
            <div class="small text-muted">Akumulasi seluruh honor relawan</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-start border-4 border-primary">
            <div class="text-muted small fw-semibold">JUMLAH RELAWAN</div>
            <div class="fs-3 fw-bold text-primary">{{ $payroll->details->count() }} Orang</div>
            <div class="small text-muted">Relawan aktif dalam periode ini</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-start border-4 border-secondary">
            <div class="text-muted small fw-semibold">PENGESAHAN DOKUMEN</div>
            <div class="small fw-semibold mt-1">Dibuat: {{ $payroll->creator?->name ?? 'Admin' }}</div>
            <div class="small text-muted">
                Disetujui: {{ $payroll->approver?->name ? $payroll->approver->name . ' (' . $payroll->approved_at?->format('d/m/Y H:i') . ')' : 'Menunggu Approval' }}
            </div>
        </div>
    </div>
</div>

{{-- Tabel Rincian per Relawan --}}
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 fw-bold">
        <i class="bi bi-table me-1 text-primary"></i> Rincian Honorarium per Relawan
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0 text-center">
                <thead class="table-light small">
                    <tr>
                        <th class="text-start">Nama Relawan</th>
                        <th>Divisi</th>
                        <th>Hari Masuk</th>
                        <th>Izin Sah</th>
                        <th>Total Jam</th>
                        <th>Honor Jam</th>
                        <th>Transport</th>
                        <th>Uang Makan</th>
                        <th>Lembur</th>
                        <th>Total Bersih</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody class="small">
                @foreach($payroll->details as $d)
                <tr>
                    <td class="text-start">
                        <div class="fw-bold">{{ $d->user->name }}</div>
                        <small class="text-muted">{{ $d->user->nik ?? '-' }}</small>
                    </td>
                    <td>{{ $d->user->division?->name ?? 'Umum' }}</td>
                    <td><span class="badge bg-light text-dark border">{{ $d->total_days_attended }} hari</span></td>
                    <td><span class="badge bg-info-subtle text-info border">{{ $d->total_days_leave }} hari</span></td>
                    <td class="fw-bold">{{ number_format($d->total_hours, 1) }} jam</td>
                    <td>Rp {{ number_format($d->base_salary, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->transport_allowance, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->meal_allowance, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($d->overtime_pay, 0, ',', '.') }}</td>
                    <td class="fw-bold text-success fs-6">Rp {{ number_format($d->net_salary, 0, ',', '.') }}</td>
                    <td>
                        <a href="{{ route('admin.payroll.slipPdf', $d) }}" class="btn btn-sm btn-outline-danger py-0 px-2" title="Unduh Slip Gaji PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Slip
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

