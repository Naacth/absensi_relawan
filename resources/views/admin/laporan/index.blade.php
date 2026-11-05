@extends('layouts.app')
@section('title', 'Laporan Kehadiran')
@section('sidebar')
<ul class="nav flex-column gap-1">
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('admin.relawan.index') }}"><i class="bi bi-people me-2"></i>Data Relawan</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('admin.laporan.index') }}"><i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan</a></li>
</ul>
@endsection
@section('content')
<h4 class="fw-bold mb-4"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Laporan Kehadiran</h4>

<div class="card border-0 shadow-sm mb-3">
    <div class="card-body py-2">
        <form class="row g-2 align-items-center">
            <div class="col-auto">
                <input type="month" name="bulan" class="form-control form-control-sm" value="{{ $bulan }}">
            </div>
            <div class="col-auto">
                <button class="btn btn-sm btn-primary"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Nama Relawan</th><th>Total Hari Hadir</th><th>Total Jam Kerja</th><th>Rata-rata Jam/Hari</th></tr>
            </thead>
            <tbody>
            @forelse($data as $relawan)
            @php
                $totalHari = $relawan->attendances->whereNotNull('check_in_at')->count();
                $totalJam  = $relawan->attendances->sum('total_hours');
                $rataRata  = $totalHari > 0 ? round($totalJam / $totalHari, 2) : 0;
            @endphp
            <tr>
                <td class="fw-semibold">{{ $relawan->name }}</td>
                <td>{{ $totalHari }} hari</td>
                <td>{{ number_format($totalJam, 2) }} jam</td>
                <td>{{ $rataRata }} jam</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted py-3">Tidak ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
