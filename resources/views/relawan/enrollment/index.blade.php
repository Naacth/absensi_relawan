@extends('layouts.app')
@section('title', 'Pendaftaran Wajah')
@section('sidebar')
<ul class="nav flex-column gap-1">
    <li class="nav-item"><a class="nav-link" href="{{ route('relawan.dashboard') }}"><i class="bi bi-house me-2"></i>Beranda</a></li>
    <li class="nav-item"><a class="nav-link active" href="{{ route('relawan.enrollment.index') }}"><i class="bi bi-camera me-2"></i>Daftar Wajah</a></li>
    <li class="nav-item"><a class="nav-link" href="{{ route('relawan.absensi.index') }}"><i class="bi bi-clock me-2"></i>Absensi</a></li>
</ul>
@endsection
@section('content')
<h4 class="fw-bold mb-1"><i class="bi bi-camera me-2 text-primary"></i>Pendaftaran Wajah</h4>
<p class="text-muted mb-4">Daftarkan wajah Anda untuk dapat melakukan absensi.</p>

@if($user->face_enrolled)
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>Wajah Anda sudah terdaftar. Jika ingin mendaftar ulang, hubungi admin.
</div>
@else
<div class="alert alert-info">
    <strong>Panduan:</strong> Pastikan wajah Anda terlihat jelas, pencahayaan cukup, dan tidak memakai masker atau topi.
</div>
<div class="card border-0 shadow-sm" style="max-width:500px">
    <div class="card-body text-center">
        <div class="d-flex justify-content-between align-items-center mb-2 px-1">
            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="bi bi-camera-video me-1"></i>Kamera Aktif</span>
            <button type="button" id="btnToggleMirror" class="btn btn-sm btn-outline-secondary" onclick="toggleCameraMirror()">
                <i class="bi bi-arrow-left-right me-1"></i><span id="mirrorStatusText">Normal (Gak Mirror)</span>
            </button>
        </div>
        <video id="video" width="100%" height="300" autoplay playsinline class="rounded mb-3 border bg-dark" style="object-fit: cover; transform: none;"></video>
        <canvas id="canvas" style="display:none"></canvas>
        <div id="alertBox"></div>
        <button id="captureBtn" class="btn btn-primary btn-lg w-100">
            <i class="bi bi-camera me-2"></i>Ambil Foto & Daftar Wajah
        </button>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const video   = document.getElementById('video');
const canvas  = document.getElementById('canvas');
const alertBox= document.getElementById('alertBox');
const btn     = document.getElementById('captureBtn');

// Status mirror: default false (gak mirror)
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

if (btn) {
    applyMirror();

    // Minta akses kamera
    navigator.mediaDevices.getUserMedia({ video: { width: { ideal: 1280 }, height: { ideal: 720 } } })
        .then(stream => { 
            video.srcObject = stream; 
            applyMirror();
        })
        .catch(() => {
            alertBox.innerHTML = '<div class="alert alert-danger">Akses kamera ditolak atau tidak tersedia.</div>';
            btn.disabled = true;
        });

    btn.addEventListener('click', () => {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

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

        const photo = canvas.toDataURL('image/jpeg');

        fetch('{{ route("relawan.enrollment.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ photo }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alertBox.innerHTML = '<div class="alert alert-success">' + data.message + '</div>';
                setTimeout(() => location.href = '{{ route("relawan.dashboard") }}', 2000);
            } else {
                alertBox.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-camera me-2"></i>Ambil Foto & Daftar Wajah';
            }
        })
        .catch(err => {
            alertBox.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan pada server.</div>';
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-camera me-2"></i>Ambil Foto & Daftar Wajah';
        });
    });
}
</script>
@endpush

