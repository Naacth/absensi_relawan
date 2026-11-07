@extends('layouts.app')
@section('title', 'Absensi Manual — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i>Absensi Manual</h4>
        <small class="text-muted">Admin dapat menghadirkan atau menghapus kehadiran relawan secara manual</small>
    </div>
    <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2">
        <i class="bi bi-exclamation-triangle me-1"></i> Hanya untuk koreksi data kehadiran
    </span>
</div>

{{-- Filter Tanggal & Divisi --}}
<div class="card shadow-sm mb-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('admin.absensi-manual.index') }}" class="row g-2 align-items-center">
            <div class="col-auto">
                <label class="col-form-label fw-semibold small">Tanggal:</label>
            </div>
            <div class="col-auto">
                <input type="date" name="tanggal" class="form-control form-control-sm"
                       value="{{ $tanggal }}" max="{{ today()->toDateString() }}">
            </div>
            <div class="col-auto">
                <label class="col-form-label fw-semibold small">Divisi:</label>
            </div>
            <div class="col-auto">
                <select name="divisi_id" class="form-select form-select-sm" style="min-width:160px;">
                    <option value="">-- Semua Divisi --</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ $divisiId == $div->id ? 'selected' : '' }}>
                        {{ $div->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="bi bi-funnel me-1"></i>Tampilkan
                </button>
            </div>
        </form>
    </div>
</div>

<form method="POST" action="{{ route('admin.absensi-manual.store') }}" id="formAbsensiManual">
    @csrf
    <input type="hidden" name="tanggal" value="{{ $tanggal }}">
    <input type="hidden" name="divisi_id" value="{{ $divisiId }}">

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <span class="fw-bold">
                <i class="bi bi-calendar-check me-1 text-primary"></i>
                Daftar Relawan — {{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l, d F Y') }}
            </span>
            <div class="d-flex align-items-center gap-3">
                {{-- Jam Masuk & Pulang --}}
                <div class="d-flex align-items-center gap-2 small">
                    <label class="fw-semibold mb-0">Jam Masuk:</label>
                    <input type="time" name="jam_masuk" class="form-control form-control-sm" value="07:00" style="width:110px;">
                    <label class="fw-semibold mb-0">Jam Pulang:</label>
                    <input type="time" name="jam_pulang" class="form-control form-control-sm" value="15:00" style="width:110px;">
                </div>
                {{-- Checklist semua --}}
                <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="checkAll">
                    <label class="form-check-label small fw-semibold" for="checkAll">Pilih Semua</label>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th width="50" class="text-center">Hadir</th>
                            <th>Nama Relawan</th>
                            <th>Divisi</th>
                            <th class="text-center">Status Hari Ini</th>
                            <th class="text-center">Jam Masuk</th>
                            <th class="text-center">Jam Pulang</th>
                            <th class="text-center">Total Jam</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($relawanList as $r)
                    @php
                        $absensi = $r->absensi_hari_ini;
                        $sudahHadir = $absensi && $absensi->check_in_at;
                        $isManual   = $absensi?->is_manual ?? false;
                    @endphp
                    <tr class="{{ $sudahHadir ? ($isManual ? 'table-warning' : 'table-success') : '' }}">
                        <td class="text-center">
                            <input class="form-check-input check-relawan" type="checkbox"
                                   name="hadir[]" value="{{ $r->id }}"
                                   {{ $sudahHadir ? 'checked' : '' }}>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $r->name }}</div>
                            <small class="text-muted">{{ $r->nik ?? '-' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary-subtle text-dark border">
                                {{ $r->division?->name ?? 'Umum' }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if($sudahHadir && $isManual)
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-pencil me-1"></i>Manual
                                </span>
                            @elseif($sudahHadir)
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i>Hadir (Wajah)
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-muted border">
                                    <i class="bi bi-dash me-1"></i>Belum Hadir
                                </span>
                            @endif
                        </td>
                        <td class="text-center small">
                            {{ $absensi?->check_in_at?->format('H:i') ?? '—' }}
                        </td>
                        <td class="text-center small">
                            {{ $absensi?->check_out_at?->format('H:i') ?? '—' }}
                        </td>
                        <td class="text-center small fw-semibold">
                            {{ $absensi?->total_hours ? number_format($absensi->total_hours, 1) . ' jam' : '—' }}
                        </td>
                        <td class="text-center">
                            @if($absensi && $isManual)
                            <form method="POST"
                                  action="{{ route('admin.absensi-manual.destroy', $absensi) }}"
                                  onsubmit="return confirm('Hapus absensi manual {{ $r->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2"
                                        title="Hapus absensi manual ini">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @elseif($absensi && !$isManual)
                                <span class="text-muted small" title="Absensi asli tidak bisa dihapus dari sini">
                                    <i class="bi bi-lock text-muted"></i>
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-4 d-block mb-2"></i>
                            Tidak ada relawan aktif untuk filter ini.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($relawanList->count() > 0)
        <div class="card-footer bg-white py-3">
            <div class="row align-items-center g-2">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small mb-1">
                        <i class="bi bi-chat-left-text me-1"></i>Catatan / Alasan Input Manual:
                    </label>
                    <input type="text" name="manual_note" class="form-control form-control-sm"
                           placeholder="Contoh: Relawan hadir tapi HP rusak, server error, dll."
                           maxlength="255">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2 justify-content-md-end">
                    <div class="small text-muted me-2">
                        <span id="countChecked" class="fw-bold text-primary">0</span>
                        dari {{ $relawanList->count() }} relawan dipilih hadir
                    </div>
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-save me-1"></i>Simpan Absensi Manual
                    </button>
                </div>
            </div>
        </div>
        @endif
    </div>
</form>

{{-- Keterangan warna --}}
<div class="mt-3 d-flex gap-3 small text-muted">
    <span><span class="badge bg-success me-1">●</span> Hadir via sistem (wajah + GPS)</span>
    <span><span class="badge bg-warning text-dark me-1">●</span> Hadir via input manual admin</span>
    <span><span class="badge bg-secondary-subtle text-muted border me-1">●</span> Belum hadir</span>
    <span><i class="bi bi-lock text-muted me-1"></i> Absensi asli tidak bisa dihapus dari sini</span>
</div>
@endsection

@push('scripts')
<script>
// Checklist semua
document.getElementById('checkAll').addEventListener('change', function () {
    document.querySelectorAll('.check-relawan').forEach(cb => cb.checked = this.checked);
    updateCount();
});

// Hitung yang dicentang
function updateCount() {
    const total = document.querySelectorAll('.check-relawan:checked').length;
    document.getElementById('countChecked').textContent = total;
}

document.querySelectorAll('.check-relawan').forEach(cb => {
    cb.addEventListener('change', updateCount);
});

// Init count
updateCount();
</script>
@endpush
