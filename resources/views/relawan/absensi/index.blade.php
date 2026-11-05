@extends('layouts.app')
@section('title', 'Absensi Wajah & GPS — SPPG')

@section('sidebar')
    @include('layouts.relawan_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2 text-primary"></i>Absensi Relawan (Wajah & GPS)</h4>
        <small class="text-muted">{{ now()->translatedFormat('l, d F Y') }} &bull; Divisi: <strong>{{ $user->division?->name ?? 'Belum Ditentukan' }}</strong></small>
    </div>
</div>

@if(!$user->face_enrolled)
<div class="alert alert-warning shadow-sm border-warning">
    <i class="bi bi-exclamation-triangle-fill me-2"></i>
    Wajah Anda belum terdaftar di sistem. Silakan <a href="{{ route('relawan.enrollment.index') }}" class="fw-bold alert-link">Daftarkan Wajah</a> terlebih dahulu agar dapat melakukan absensi.
</div>
@else

{{-- Info Jadwal Hari Ini --}}
@if($schedule)
<div class="alert alert-info border-info small py-2 mb-3">
    <i class="bi bi-calendar-check me-1"></i>
    <strong>Jadwal Divisi {{ $user->division->name }} Hari Ini:</strong>
    Masuk: <strong>{{ substr($schedule->check_in_start, 0, 5) }} - {{ substr($schedule->check_in_end, 0, 5) }} WIB</strong> &bull;
    Pulang: setelah <strong>{{ substr($schedule->check_out_start, 0, 5) }} WIB</strong> (Toleransi telat: {{ $schedule->late_tolerance }} mnt)
</div>
@endif

{{-- Status Hari Ini --}}
<div class="row g-2 mb-3" style="max-width: 520px;">
    <div class="col-6">
        <div class="card shadow-sm text-center p-2 border-start border-3 {{ $absenHariIni?->check_in_at ? 'border-success' : 'border-secondary' }}">
            <div class="small text-muted">Absen Masuk</div>
            <div class="fw-bold fs-5 {{ $absenHariIni?->check_in_at ? 'text-success' : 'text-secondary' }}">
                {{ $absenHariIni?->check_in_at?->format('H:i') ?? '—' }}
            </div>
            @if($absenHariIni?->check_in_at)
                <small class="text-muted" style="font-size: 0.72rem;">Jarak: {{ $absenHariIni->distance_m ?? '-' }}m</small>
            @endif
        </div>
    </div>
    <div class="col-6">
        <div class="card shadow-sm text-center p-2 border-start border-3 {{ $absenHariIni?->check_out_at ? 'border-success' : 'border-secondary' }}">
            <div class="small text-muted">Absen Pulang</div>
            <div class="fw-bold fs-5 {{ $absenHariIni?->check_out_at ? 'text-success' : 'text-secondary' }}">
                {{ $absenHariIni?->check_out_at?->format('H:i') ?? '—' }}
            </div>
            @if($absenHariIni?->check_out_at)
                <small class="text-muted" style="font-size: 0.72rem;">Jam Kerja: {{ $absenHariIni->total_hours ?? '-' }} jam</small>
            @endif
        </div>
    </div>
</div>

<div class="card shadow-sm mb-4" style="max-width: 520px;">
    <div class="card-body text-center p-3">
        {{-- Status GPS & Mirror Toggle --}}
        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
            <span id="gpsStatusBadge" class="badge bg-warning-subtle text-dark border border-warning-subtle small">
                <i class="bi bi-geo-alt me-1"></i> Mendeteksi Lokasi GPS...
            </span>
            <button type="button" id="btnToggleMirror" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="toggleCameraMirror()">
                <i class="bi bi-arrow-left-right me-1"></i><span id="mirrorStatusText">Normal (Gak Mirror)</span>
            </button>
        </div>

        {{-- Preview Video Kamera --}}
        <video id="video" width="100%" height="320" autoplay playsinline class="rounded mb-3 border bg-dark" style="object-fit: cover; transform: none;"></video>
        <canvas id="canvas" style="display:none"></canvas>

        <div id="alertBox" class="mb-3"></div>

        {{-- Tombol Absensi --}}
        <div class="d-grid gap-2">
            @if(!$absenHariIni?->check_in_at)
            <button id="btnMasuk" class="btn btn-success btn-lg fw-semibold" onclick="absen('in')" disabled>
                <i class="bi bi-box-arrow-in-right me-2"></i> Absen Masuk (Verifikasi Wajah & GPS)
            </button>
            @endif

            @if($absenHariIni?->check_in_at && !$absenHariIni?->check_out_at)
            <button id="btnPulang" class="btn btn-warning btn-lg fw-semibold" onclick="absen('out')" disabled>
                <i class="bi bi-box-arrow-right me-2"></i> Absen Pulang
            </button>
            @endif

            @if($absenHariIni?->check_in_at && $absenHariIni?->check_out_at)
            <div class="alert alert-success mb-0 py-2">
                <i class="bi bi-check-circle-fill me-2"></i>
                Kehadiran hari ini sudah lengkap. Terima kasih atas kontribusi Anda!
            </div>
            @endif
        </div>

        {{-- Hasil Foto Watermark jika baru selesai absen --}}
        <div id="resultPreview" class="mt-3" style="display:none;">
            <div class="small fw-semibold text-muted mb-1">Bukti Digital Ber-Watermark Tersimpan:</div>
            <img id="resultImage" src="" alt="Bukti Absen" class="img-fluid rounded border shadow-sm" style="max-height: 250px;">
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const video          = document.getElementById('video');
const canvas         = document.getElementById('canvas');
const alertBox       = document.getElementById('alertBox');
const gpsStatusBadge = document.getElementById('gpsStatusBadge');
let currentLat       = null;
let currentLng       = null;

// Pengaturan Mirror Kamera
let isMirrored = localStorage.getItem('camera_mirrored') === 'true';

function applyMirror() {
    const statusText = document.getElementById('mirrorStatusText');
    if (video) {
        video.style.transform = isMirrored ? 'scaleX(-1)' : 'none';
        video.style.webkitTransform = isMirrored ? 'scaleX(-1)' : 'none';
    }
    if (statusText) {
        statusText.textContent = isMirrored ? 'Mode: Cermin (Mirror)' : 'Mode: Normal (Gak Mirror)';
    }
}

function toggleCameraMirror() {
    isMirrored = !isMirrored;
    localStorage.setItem('camera_mirrored', isMirrored);
    applyMirror();
}

// 1. Inisialisasi Kamera
if (video) {
    applyMirror();

    navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
        .then(stream => {
            video.srcObject = stream;
            applyMirror();
        })
        .catch(() => {
            alertBox.innerHTML = '<div class="alert alert-danger py-2">Akses kamera ditolak atau tidak tersedia. Mohon izinkan kamera pada browser Anda.</div>';
        });
}

// 2. Inisialisasi Geolocation (GPS)
function initGeolocation() {
    if (!navigator.geolocation) {
        gpsStatusBadge.className = 'badge bg-danger text-white border';
        gpsStatusBadge.innerHTML = '<i class="bi bi-x-circle me-1"></i> Browser tidak mendukung GPS';
        return;
    }

    navigator.geolocation.getCurrentPosition(
        (position) => {
            currentLat = position.coords.latitude;
            currentLng = position.coords.longitude;

            gpsStatusBadge.className = 'badge bg-success-subtle text-success border border-success-subtle';
            gpsStatusBadge.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> GPS Terkunci (' + currentLat.toFixed(4) + ', ' + currentLng.toFixed(4) + ')';

            // Aktifkan tombol absen setelah GPS didapat
            const btnMasuk = document.getElementById('btnMasuk');
            const btnPulang = document.getElementById('btnPulang');
            if (btnMasuk) btnMasuk.disabled = false;
            if (btnPulang) btnPulang.disabled = false;
        },
        (error) => {
            gpsStatusBadge.className = 'badge bg-danger text-white';
            let errMsg = 'Gagal mengakses GPS';
            if (error.code === error.PERMISSION_DENIED) {
                errMsg = 'Izin lokasi (GPS) ditolak! Mohon aktifkan izin GPS browser Anda.';
            }
            gpsStatusBadge.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + errMsg;
            alertBox.innerHTML = '<div class="alert alert-danger py-2">' + errMsg + '</div>';
        },
        { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 }
    );
}

document.addEventListener('DOMContentLoaded', initGeolocation);

// 3. Proses Pengiriman Absensi
function absen(type) {
    if (!currentLat || !currentLng) {
        alertBox.innerHTML = '<div class="alert alert-warning py-2">Lokasi GPS belum terkunci. Mohon tunggu sejenak atau aktifkan GPS Anda.</div>';
        initGeolocation();
        return;
    }

    const btn = type === 'in' ? document.getElementById('btnMasuk') : document.getElementById('btnPulang');
    if (!btn) return;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memverifikasi Wajah & Lokasi...';
    alertBox.innerHTML = '';

    canvas.width  = video.videoWidth || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');

    if (isMirrored) {
        ctx.save();
        ctx.translate(canvas.width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        ctx.restore();
    } else {
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    }

    const photo = canvas.toDataURL('image/jpeg', 0.85);

    fetch('{{ route("relawan.absensi.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            type,
            photo,
            lat: currentLat,
            lng: currentLng,
            client_timestamp: new Date().toISOString(),
        }),
    })
    .then(r => r.json().then(data => ({ status: r.status, ok: r.ok, data })))
    .then(res => {
        if (res.ok && res.data.success) {
            alertBox.innerHTML = '<div class="alert alert-success"><i class="bi bi-check-circle-fill me-2"></i>' + res.data.message + '</div>';
            
            if (res.data.photo_url) {
                document.getElementById('resultImage').src = res.data.photo_url;
                document.getElementById('resultPreview').style.display = 'block';
            }

            setTimeout(() => location.reload(), 3000);
        } else {
            alertBox.innerHTML = '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>' + (res.data.message || 'Verifikasi gagal.') + '</div>';
            btn.disabled = false;
            btn.innerHTML = type === 'in'
                ? '<i class="bi bi-box-arrow-in-right me-2"></i> Absen Masuk (Verifikasi Wajah & GPS)'
                : '<i class="bi bi-box-arrow-right me-2"></i> Absen Pulang';
        }
    })
    .catch(err => {
        alertBox.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan pada server: ' + err.message + '</div>';
        btn.disabled = false;
        btn.innerHTML = type === 'in'
            ? '<i class="bi bi-box-arrow-in-right me-2"></i> Absen Masuk'
            : '<i class="bi bi-box-arrow-right me-2"></i> Absen Pulang';
    });
}
</script>
@endpush
