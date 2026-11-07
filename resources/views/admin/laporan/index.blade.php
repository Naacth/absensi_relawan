@extends('layouts.app')
@section('title', 'Laporan Kehadiran — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Rekap Kehadiran</h4>
        <small class="text-muted">Rekap total jam kerja dan kehadiran per relawan</small>
    </div>
</div>

{{-- Filter Bulan + Tombol Export --}}
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label col-form-label-sm fw-semibold">Periode:</label>
            </div>
            <div class="col-auto">
                <input type="month" name="bulan" class="form-control form-control-sm" value="{{ $bulan }}">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Filter
                </button>
            </div>
            <div class="col-auto ms-auto d-flex gap-2">
                <a href="{{ route('admin.laporan.exportExcel', ['bulan' => $bulan]) }}"
                   class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i>Export Excel
                </a>
                <a href="{{ route('admin.laporan.exportPdf', ['bulan' => $bulan]) }}"
                   class="btn btn-sm btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Export PDF
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Rekap --}}
<div class="card shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <span class="fw-bold">
            <i class="bi bi-table me-1 text-primary"></i>
            Rekap Bulan {{ \Carbon\Carbon::createFromFormat('Y-m', $bulan)->translatedFormat('F Y') }}
        </span>
        <span class="badge bg-primary-subtle text-primary border border-primary">
            {{ $data->count() }} Relawan
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="5%" class="text-center">No</th>
                        <th>Nama Relawan</th>
                        <th>Divisi</th>
                        <th class="text-center">Hari Hadir</th>
                        <th class="text-center">Total Jam Kerja</th>
                        <th class="text-center">Rata-rata Jam/Hari</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($data as $i => $relawan)
                @php
                    $totalHari = $relawan->attendances->whereNotNull('check_in_at')->count();
                    $totalJam  = (float) $relawan->attendances->sum('total_hours');
                    $rataRata  = $totalHari > 0 ? round($totalJam / $totalHari, 2) : 0;
                @endphp
                <tr>
                    <td class="text-center text-muted">{{ $i + 1 }}</td>
                    <td>
                        <div class="fw-semibold">{{ $relawan->name }}</div>
                        <small class="text-muted">{{ $relawan->nik ?? '-' }}</small>
                    </td>
                    <td>
                        <span class="badge bg-secondary-subtle text-dark border">
                            {{ $relawan->division?->name ?? 'Umum' }}
                        </span>
                    </td>
                    <td class="text-center">
                        @if($totalHari > 0)
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check-circle me-1"></i>{{ $totalHari }} hari
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-muted border">0 hari</span>
                        @endif
                    </td>
                    <td class="text-center fw-semibold">{{ number_format($totalJam, 2) }} jam</td>
                    <td class="text-center text-muted">{{ number_format($rataRata, 2) }} jam</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                        Tidak ada data kehadiran untuk periode ini.
                    </td>
                </tr>
                @endforelse
                </tbody>
                @if($data->count() > 0)
                <tfoot class="table-light">
                @php
                    $grandTotalHari = 0;
                    $grandTotalJam  = 0;
                    foreach ($data as $r) {
                        $grandTotalHari += $r->attendances->whereNotNull('check_in_at')->count();
                        $grandTotalJam  += (float) $r->attendances->sum('total_hours');
                    }
                @endphp
                <tr>
                    <td colspan="3" class="text-end fw-bold text-muted">Total Keseluruhan:</td>
                    <td class="text-center fw-bold">{{ $grandTotalHari }} hari</td>
                    <td class="text-center fw-bold">{{ number_format($grandTotalJam, 2) }} jam</td>
                    <td></td>
                </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
