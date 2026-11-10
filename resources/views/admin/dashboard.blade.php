@extends('layouts.app')
@section('title', 'Dashboard Admin — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard Monitoring</h4>
        <small class="text-muted">SPPG Curug Cukanggalih 02 &bull; {{ now()->translatedFormat('l, d F Y') }}</small>
    </div>
    <span class="badge bg-success-subtle text-success border border-success px-3 py-2">
        <i class="bi bi-broadcast me-1"></i> Sistem Aktif & Terlindungi Geofencing
    </span>
</div>

{{-- Anomali Fraud Warning jika ada --}}
@if($anomaliIp->count() > 0)
<div class="alert alert-warning border-warning shadow-sm mb-4">
    <div class="d-flex align-items-center">
        <i class="bi bi-exclamation-octagon-fill fs-3 text-danger me-3"></i>
        <div>
            <div class="fw-bold text-dark">Deteksi Anomali Jaringan (Potential Fraud Alert)</div>
            <div class="small text-secondary">
                Ditemukan {{ $anomaliIp->count() }} IP Address yang digunakan oleh lebih dari 1 relawan berbeda dalam kurun 24 jam terakhir. 
                Hal ini dapat mengindikasikan 1 perangkat digunakan bersamaan untuk titip absen.
            </div>
        </div>
    </div>
</div>
@endif

{{-- Kartu Statistik Kehadiran --}}
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-start border-4 border-primary">
            <div class="text-muted small fw-semibold">TOTAL RELAWAN AKTIF</div>
            <div class="fs-2 fw-bold text-primary">{{ $totalRelawan }}</div>
            <div class="small text-muted">Terdaftar di sistem</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-start border-4 border-success">
            <div class="text-muted small fw-semibold">HADIR HARI INI</div>
            <div class="fs-2 fw-bold text-success">{{ $hadirHariIni }}</div>
            <div class="small text-muted">{{ $persentaseHadir }}% dari total relawan</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-start border-4 border-info">
            <div class="text-muted small fw-semibold">IZIN / SAKIT SAH</div>
            <div class="fs-2 fw-bold text-info">{{ $izinHariIni }}</div>
            <div class="small text-muted">Disetujui pengurus</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 shadow-sm border-start border-4 border-warning">
            <div class="text-muted small fw-semibold">BELUM ABSEN</div>
            <div class="fs-2 fw-bold text-warning">{{ $belumAbsen }}</div>
            <div class="small text-muted">Perlu pemantauan</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Grafik Tren Kehadiran 7 Hari --}}
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-bar-chart-line me-1 text-primary"></i> Tren Kehadiran 7 Hari Terakhir</span>
                <span class="badge bg-primary-subtle text-primary border">Grafik Realtime</span>
            </div>
            <div class="card-body">
                <canvas id="trenChart" height="80"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-3">
    {{-- Tabel Absensi Masuk Hari Ini --}}
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-clock-history me-1 text-primary"></i> Kehadiran Relawan Hari Ini</span>
                <span class="badge bg-primary">{{ $absenHariIni->count() }} Absen Masuk</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Relawan</th>
                                <th>Divisi</th>
                                <th>Jam Masuk</th>
                                <th>Skor Wajah</th>
                                <th>Jarak GPS</th>
                                <th>Foto Bukti</th>
                            </tr>
                        </thead>
                        <tbody>
                        @forelse($absenHariIni as $a)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $a->user->name }}</div>
                                <small class="text-muted">{{ $a->user->nik ?? '-' }}</small>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-dark border">
                                    {{ $a->user->division?->name ?? 'Umum' }}
                                </span>
                            </td>
                            <td>
                                <span class="fw-bold text-success">{{ $a->check_in_at?->format('H:i:s') }}</span>
                                @if($a->is_manual)
                                    <span class="badge bg-warning text-dark ms-1 small">Manual</span>
                                @endif
                            </td>
                            <td>
                                @if($a->check_in_score)
                                    <span class="badge bg-success">
                                        {{ number_format($a->check_in_score * 100, 1) }}%
                                    </span>
                                @else -
                                @endif
                            </td>
                            <td>
                                <span class="small text-muted">
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    {{ $a->distance_m ? number_format($a->distance_m, 1) . ' m' : '-' }}
                                </span>
                            </td>
                            <td>
                                @if($a->photo_check_in_path)
                                    <a href="{{ storage_url($a->photo_check_in_path) }}" target="_blank" class="btn btn-sm btn-outline-primary py-0 px-2">
                                        <i class="bi bi-image me-1"></i>Lihat
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Belum ada relawan yang absen hari ini.</td>
                        </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Log Percobaan Gagal / Anomali --}}
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <span class="fw-bold text-danger"><i class="bi bi-shield-x me-1"></i> Log Percobaan Ditolak</span>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush small">
                @forelse($logGagal as $log)
                    <div class="list-group-item">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-semibold text-dark">{{ $log->user?->name ?? 'Wajah Tidak Dikenali' }}</span>
                            <span class="text-muted" style="font-size: 0.75rem;">{{ $log->created_at->format('H:i') }}</span>
                        </div>
                        <div class="text-danger small">{{ $log->reason }}</div>
                        <div class="text-muted" style="font-size: 0.72rem;">
                            IP: {{ $log->ip_address ?? '-' }} &bull; Tipe: {{ strtoupper($log->type) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">Tidak ada log percobaan gagal.</div>
                @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const trenData = @json($tren);

    const labels  = trenData.map(d => {
        const date = new Date(d.tanggal);
        return date.toLocaleDateString('id-ID', { weekday: 'short', day: 'numeric', month: 'short' });
    });
    const values  = trenData.map(d => d.hadir);
    const maxVal  = Math.max(...values, 1);

    const ctx = document.getElementById('trenChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Jumlah Hadir',
                data: values,
                backgroundColor: values.map((v, i) =>
                    i === values.length - 1 ? 'rgba(37, 99, 235, 0.85)' : 'rgba(37, 99, 235, 0.35)'
                ),
                borderColor: 'rgba(37, 99, 235, 1)',
                borderWidth: 1.5,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: ctx => ` ${ctx.parsed.y} relawan hadir`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: Math.ceil(maxVal * 1.3) || 5,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                    },
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
