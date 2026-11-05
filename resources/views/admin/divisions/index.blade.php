@extends('layouts.app')
@section('title', 'Divisi & Jadwal Kerja — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-diagram-3 me-2 text-primary"></i>Manajemen Divisi & Jadwal Kerja</h4>
        <small class="text-muted">Atur jam masuk, jam pulang, dan toleransi keterlambatan tiap divisi</small>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahDivisi">
        <i class="bi bi-plus-lg me-1"></i> Tambah Divisi
    </button>
</div>

<div class="row g-4">
    @forelse($divisions as $div)
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <span class="fw-bold fs-6 text-dark">{{ $div->name }}</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                    {{ $div->users_count }} Relawan
                </span>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">{{ $div->description ?: 'Tidak ada deskripsi.' }}</p>

                <h6 class="fw-bold small text-secondary mb-2"><i class="bi bi-clock me-1"></i> Jam Operasional (Senin-Sabtu)</h6>
                @php $todaySched = $div->todaySchedule(); @endphp
                @if($todaySched)
                    <div class="bg-light p-2 rounded small mb-3">
                        <div><strong>Masuk:</strong> {{ substr($todaySched->check_in_start, 0, 5) }} - {{ substr($todaySched->check_in_end, 0, 5) }} WIB</div>
                        <div><strong>Pulang:</strong> Setelah {{ substr($todaySched->check_out_start, 0, 5) }} WIB</div>
                        <div class="text-muted">Toleransi telat: {{ $todaySched->late_tolerance }} menit</div>
                    </div>
                @else
                    <div class="text-muted small mb-3">Jadwal hari ini libur.</div>
                @endif

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.divisions.edit', $div) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                        <i class="bi bi-pencil-square me-1"></i> Atur Jadwal & Edit
                    </a>
                    @if($div->users_count == 0)
                    <form method="POST" action="{{ route('admin.divisions.destroy', $div) }}" onsubmit="return confirm('Hapus divisi ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5 text-muted">Belum ada divisi terdaftar.</div>
    @endforelse
</div>

{{-- Modal Tambah Divisi --}}
<div class="modal fade" id="modalTambahDivisi" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.divisions.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Tambah Divisi Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Divisi *</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Divisi Logistik" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Uraian tugas divisi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Divisi</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

