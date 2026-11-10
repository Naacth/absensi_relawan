@extends('layouts.app')
@section('title', 'Dashboard Relawan — SPPG')

@section('sidebar')
    @include('layouts.relawan_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Halo, {{ $user->name }}! 👋</h4>
        <div class="text-muted">
            Divisi: <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $user->division?->name ?? 'Belum Ditentukan' }}</span> &bull;
            {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>
</div>

@if(!$user->face_enrolled)
<div class="alert alert-warning shadow-sm border-warning mb-4">
    <div class="d-flex align-items-center justify-content-between">
        <div>
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Wajah Anda belum terdaftar!</strong> Anda tidak dapat melakukan absensi sebelum mendaftarkan wajah.
        </div>
        <a href="{{ route('relawan.enrollment.index') }}" class="btn btn-sm btn-warning fw-semibold">
            Daftar Wajah Sekarang
        </a>
    </div>
</div>
@endif

{{-- Status Kehadiran Hari Ini --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-start border-4 border-success">
            <div class="text-muted small fw-semibold">ABSEN MASUK</div>
            <div class="fs-3 fw-bold {{ $absenHariIni?->check_in_at ? 'text-success' : 'text-secondary' }}">
                {{ $absenHariIni?->check_in_at?->format('H:i') ?? 'Belum Absen' }}
            </div>
            @if($absenHariIni?->check_in_at)
                <small class="text-muted">Jarak: {{ $absenHariIni->distance_m ?? '-' }}m dari SPPG</small>
            @elseif($schedule)
                <small class="text-muted">Jadwal: {{ substr($schedule->check_in_start, 0, 5) }} - {{ substr($schedule->check_in_end, 0, 5) }} WIB</small>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-start border-4 border-primary">
            <div class="text-muted small fw-semibold">ABSEN PULANG</div>
            <div class="fs-3 fw-bold {{ $absenHariIni?->check_out_at ? 'text-primary' : 'text-secondary' }}">
                {{ $absenHariIni?->check_out_at?->format('H:i') ?? 'Belum Pulang' }}
            </div>
            @if($absenHariIni?->check_out_at)
                <small class="text-muted">Total: {{ $absenHariIni->total_hours ?? '-' }} jam</small>
            @elseif($schedule)
                <small class="text-muted">Setelah: {{ substr($schedule->check_out_start, 0, 5) }} WIB</small>
            @endif
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 shadow-sm border-start border-4 border-info">
            <div class="text-muted small fw-semibold">JAM KERJA HARI INI</div>
            <div class="fs-3 fw-bold text-info">
                {{ $absenHariIni?->total_hours ? $absenHariIni->total_hours . ' Jam' : '0.0 Jam' }}
            </div>
            <small class="text-muted">Dihitung otomatis server</small>
        </div>
    </div>
</div>

{{-- Tombol Aksi Cepat --}}
<div class="d-flex gap-2 mb-4">
    @if($user->face_enrolled)
    <a href="{{ route('relawan.absensi.index') }}" class="btn btn-primary px-4 py-2 fw-semibold">
        <i class="bi bi-camera me-1"></i> Buka Kamera Absensi
    </a>
    @endif
    <a href="{{ route('relawan.leave.create') }}" class="btn btn-outline-secondary px-3 py-2">
        <i class="bi bi-envelope-plus me-1"></i> Ajukan Izin / Sakit
    </a>
    <a href="{{ route('relawan.payroll.index') }}" class="btn btn-outline-success px-3 py-2">
        <i class="bi bi-receipt me-1"></i> Lihat Slip Honor
    </a>
</div>

<div class="row g-4">
    {{-- Riwayat Absensi --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-clock-history me-1 text-primary"></i> Riwayat Absensi Terakhir
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light small">
                            <tr>
                                <th class="text-start">Tanggal</th>
                                <th>Masuk</th>
                                <th>Pulang</th>
                                <th>Jam Kerja</th>
                                <th>Bukti Foto</th>
                            </tr>
                        </thead>
                        <tbody class="small">
                        @forelse($riwayat as $r)
                        <tr>
                            <td class="text-start fw-semibold">{{ $r->date->translatedFormat('d M Y') }}</td>
                            <td>{{ $r->check_in_at?->format('H:i') ?? '-' }}</td>
                            <td>{{ $r->check_out_at?->format('H:i') ?? '-' }}</td>
                            <td><span class="badge bg-light text-dark border">{{ $r->total_hours ? $r->total_hours . ' jam' : '-' }}</span></td>
                            <td>
                                @if($r->photo_check_in_path)
                                    <a href="{{ storage_url($r->photo_check_in_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <i class="bi bi-image"></i>
                                    </a>
                                @else -
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted py-4">Belum ada riwayat absensi.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Pengajuan Izin Terakhir --}}
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-envelope-paper me-1 text-primary"></i> Pengajuan Izin</span>
                <a href="{{ route('relawan.leave.index') }}" class="small text-decoration-none">Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small">
                @forelse($pengajuanIzinTerbaru as $leave)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-{{ $leave->type === 'sakit' ? 'danger' : 'warning text-dark' }} text-uppercase">
                                {{ $leave->type }}
                            </span>
                            @if($leave->status === 'approved')
                                <span class="badge bg-success">Disetujui</span>
                            @elseif($leave->status === 'rejected')
                                <span class="badge bg-danger">Ditolak</span>
                            @else
                                <span class="badge bg-secondary">Pending</span>
                            @endif
                        </div>
                        <div class="text-muted">{{ $leave->start_date->format('d/m/Y') }} s/d {{ $leave->end_date->format('d/m/Y') }}</div>
                        <div class="text-dark mt-1 text-truncate">{{ $leave->reason }}</div>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">Belum ada pengajuan izin.</div>
                @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
