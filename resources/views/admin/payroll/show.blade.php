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
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.payroll.exportCsv', $payroll) }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-excel me-1"></i> Ekspor CSV
        </a>
        @if($payroll->status === 'draft')
        <form method="POST" action="{{ route('admin.payroll.approve', $payroll) }}"
              onsubmit="return confirm('Setujui payroll ini? Sistem akan mengirimkan notifikasi WhatsApp ke seluruh relawan.')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-check2-circle me-1"></i> Approve & Kirim WA
            </button>
        </form>
        @elseif($payroll->status === 'approved')
        <form method="POST" action="{{ route('admin.payroll.markPaid', $payroll) }}"
              onsubmit="return confirm('Tandai seluruh honor telah dibayarkan?')">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-success btn-sm">
                <i class="bi bi-cash me-1"></i> Tandai Lunas (Paid)
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
                Disetujui: {{ $payroll->approver?->name
                    ? $payroll->approver->name . ' (' . $payroll->approved_at?->format('d/m/Y H:i') . ')'
                    : 'Menunggu Approval' }}
            </div>
        </div>
    </div>
</div>

{{-- Tabel Rincian per Relawan --}}
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold">
            <i class="bi bi-table me-1 text-primary"></i> Rincian Honorarium per Relawan
        </span>
        <span class="badge bg-secondary-subtle text-dark border small">
            Tarif Honor = <strong>Tarif Harian Divisi × Hari Hadir</strong>
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0 text-center">
                <thead class="table-light small">
                    <tr>
                        <th class="text-start" rowspan="2">Nama Relawan</th>
                        <th rowspan="2">Divisi</th>
                        <th rowspan="2">Tarif / Hari</th>
                        <th rowspan="2">Hari<br>Hadir</th>
                        <th rowspan="2">Izin<br>Sah</th>
                        <th colspan="4" class="bg-success-subtle text-success">Komponen Honor (Rp)</th>
                        <th rowspan="2" class="bg-primary-subtle text-primary">Total<br>Bersih</th>
                        <th rowspan="2">Aksi</th>
                    </tr>
                    <tr>
                        <th class="bg-success-subtle small text-muted">Pokok</th>
                        <th class="bg-success-subtle small text-muted">Transport</th>
                        <th class="bg-success-subtle small text-muted">Makan</th>
                        <th class="bg-success-subtle small text-muted">Lembur</th>
                    </tr>
                </thead>
                <tbody class="small">
                @forelse($payroll->details as $d)
                @php
                    $dailyRate = (float)($d->user->division?->daily_rate ?? 0);
                @endphp
                <tr>
                    <td class="text-start">
                        <div class="fw-bold">{{ $d->user->name }}</div>
                        <small class="text-muted">{{ $d->user->nik ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-dark border">
                            {{ $d->user->division?->name ?? 'Umum' }}
                        </span>
                    </td>
                    <td>
                        @if($dailyRate > 0)
                            <span class="badge bg-success-subtle text-success border border-success fw-semibold">
                                Rp {{ number_format($dailyRate, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="badge bg-warning-subtle text-warning border">Per Jam</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge bg-light text-dark border">{{ $d->total_days_attended }} hari</span>
                    </td>
                    <td>
                        <span class="badge bg-info-subtle text-info border">{{ $d->total_days_leave }} hari</span>
                    </td>
                    <td class="fw-semibold">{{ number_format($d->base_salary, 0, ',', '.') }}</td>
                    <td>{{ number_format($d->transport_allowance, 0, ',', '.') }}</td>
                    <td>{{ number_format($d->meal_allowance, 0, ',', '.') }}</td>
                    <td>{{ number_format($d->overtime_pay, 0, ',', '.') }}</td>
                    <td class="fw-bold text-success">
                        Rp {{ number_format($d->net_salary, 0, ',', '.') }}
                    </td>
                    <td>
                        <a href="{{ route('admin.payroll.slipPdf', $d) }}"
                           class="btn btn-sm btn-outline-danger py-0 px-2" title="Unduh Slip Gaji PDF">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Slip
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="text-center text-muted py-4">Tidak ada data detail payroll.</td>
                </tr>
                @endforelse
                </tbody>
                @if($payroll->details->count() > 0)
                <tfoot class="table-light fw-bold">
                    <tr>
                        <td colspan="5" class="text-end">TOTAL:</td>
                        <td>{{ number_format($payroll->details->sum('base_salary'), 0, ',', '.') }}</td>
                        <td>{{ number_format($payroll->details->sum('transport_allowance'), 0, ',', '.') }}</td>
                        <td>{{ number_format($payroll->details->sum('meal_allowance'), 0, ',', '.') }}</td>
                        <td>{{ number_format($payroll->details->sum('overtime_pay'), 0, ',', '.') }}</td>
                        <td class="text-success">Rp {{ number_format($payroll->total_disbursed, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
