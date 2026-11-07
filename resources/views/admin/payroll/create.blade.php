@extends('layouts.app')
@section('title', 'Generate Payroll Baru — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i></a>
    <div>
        <h4 class="fw-bold mb-0">Generate Payroll Baru</h4>
        <small class="text-muted">Sistem akan menghitung honor seluruh relawan aktif secara otomatis berdasarkan data absensi</small>
    </div>
</div>

<div class="row g-4">
    {{-- Form Generate --}}
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-gear-wide-connected me-1 text-primary"></i> Parameter Payroll
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('admin.payroll.generate') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Judul Payroll <span class="text-muted fw-normal">(Opsional)</span></label>
                        <input type="text" name="title" class="form-control"
                               placeholder="Contoh: Payroll Relawan Periode November 2026">
                        <small class="text-muted">Biarkan kosong untuk judul otomatis berdasarkan rentang tanggal.</small>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Mulai <span class="text-danger">*</span></label>
                            <input type="date" name="period_start" class="form-control"
                                   value="{{ now()->startOfMonth()->toDateString() }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Selesai <span class="text-danger">*</span></label>
                            <input type="date" name="period_end" class="form-control"
                                   value="{{ now()->endOfMonth()->toDateString() }}" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold">Catatan Tambahan</label>
                        <textarea name="notes" class="form-control" rows="2"
                                  placeholder="Catatan internal pengurus/bendahara..."></textarea>
                    </div>

                    <div class="alert alert-info border-info small mb-4">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        <strong>Proses Otomatis:</strong> Honor dihitung dari <strong>tarif harian per divisi × hari hadir</strong>.
                        Jika divisi belum diatur tarifnya, sistem fallback ke skema global (tarif/jam × jam kerja).
                        Tunjangan transport, uang makan, dan lembur dihitung dari skema global.
                        Payroll berstatus <strong>Draft</strong> dan dapat ditinjau sebelum di-approve.
                    </div>

                    <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold w-100">
                        <i class="bi bi-gear-wide-connected me-1"></i> Kalkulasi & Generate Payroll
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Preview Tarif Per Divisi --}}
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-cash-stack me-1 text-success"></i> Tarif Honor per Divisi
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light">
                        <tr>
                            <th>Divisi</th>
                            <th class="text-center">Tarif / Hari</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($divisions as $div)
                    <tr>
                        <td class="fw-semibold">{{ $div->name }}</td>
                        <td class="text-center fw-bold text-success">
                            Rp {{ number_format($div->daily_rate, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success border border-success">
                                <i class="bi bi-check-circle me-1"></i>Aktif
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center text-muted py-3">
                            <i class="bi bi-exclamation-triangle text-warning me-1"></i>
                            Belum ada divisi dengan tarif harian.
                        </td>
                    </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top small text-muted py-2">
                <i class="bi bi-gear me-1"></i>
                Atur tarif di <a href="{{ route('admin.divisions.index') }}" class="text-primary fw-semibold">Manajemen Divisi</a>
            </div>
        </div>

        {{-- Info Skema Global --}}
        <div class="card shadow-sm mt-3">
            <div class="card-header bg-white py-3 fw-bold">
                <i class="bi bi-sliders me-1 text-secondary"></i> Tunjangan (Skema Global)
            </div>
            <div class="card-body small">
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Transport / Hari Hadir</span>
                    <span class="fw-semibold">Rp {{ number_format(\App\Models\SalaryScheme::where('is_active',true)->value('daily_transport') ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1 border-bottom">
                    <span class="text-muted">Uang Makan / Hari (≥ 6 jam)</span>
                    <span class="fw-semibold">Rp {{ number_format(\App\Models\SalaryScheme::where('is_active',true)->value('meal_allowance') ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="d-flex justify-content-between py-1">
                    <span class="text-muted">Lembur / Jam (> 8 jam)</span>
                    <span class="fw-semibold">Rp {{ number_format(\App\Models\SalaryScheme::where('is_active',true)->value('overtime_hourly_rate') ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
