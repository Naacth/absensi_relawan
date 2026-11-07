@extends('layouts.app')
@section('title', 'Skema Honor Relawan — SPPG')

@section('sidebar')
    @include('layouts.admin_sidebar')
@endsection

@section('content')
<div class="d-flex align-items-center mb-4 gap-2">
    <a href="{{ route('admin.payroll.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="fw-bold mb-0"><i class="bi bi-cash-coin me-2 text-success"></i>Pengaturan Skema Honor Relawan</h4>
        <small class="text-muted">Atur honor harian per divisi, tunjangan transport, uang makan, dan tarif lembur</small>
    </div>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show shadow-sm">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0 small">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.payroll.skema.update') }}">
    @csrf

    <div class="row g-4">
        {{-- Honor Harian Per Divisi --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold"><i class="bi bi-diagram-3 me-1 text-primary"></i> Honor Harian per Divisi</span>
                    <div class="small text-muted mt-1">
                        Dasar perhitungan: <strong>Honor Pokok = Tarif/Hari × Hari Hadir</strong>
                    </div>
                </div>
                <div class="card-body">
                    @forelse($divisions as $div)
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            {{ $div->name }}
                            <span class="badge bg-secondary-subtle text-muted border fw-normal ms-1 small">
                                {{ $div->users->count() ?? 0 }} relawan
                            </span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">Rp</span>
                            <input type="number" name="division_rates[{{ $div->id }}]"
                                   class="form-control"
                                   value="{{ old('division_rates.'.$div->id, (int)$div->daily_rate) }}"
                                   min="0" step="1000"
                                   placeholder="0">
                            <span class="input-group-text text-muted">/ hari hadir</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-muted small text-center py-3">
                        <i class="bi bi-inbox me-1"></i> Belum ada divisi terdaftar.
                        <a href="{{ route('admin.divisions.index') }}">Tambah divisi</a>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Tunjangan & Potongan Global --}}
        <div class="col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white py-3">
                    <span class="fw-bold"><i class="bi bi-sliders me-1 text-warning"></i> Tunjangan & Potongan (Berlaku Semua Divisi)</span>
                    <div class="small text-muted mt-1">Komponen ini ditambahkan di atas honor harian divisi</div>
                </div>
                <div class="card-body">

                    {{-- Transport --}}
                    <div class="mb-4 p-3 bg-primary-subtle rounded border border-primary-subtle">
                        <div class="fw-semibold text-primary mb-2">
                            <i class="bi bi-bus-front me-1"></i> Uang Transport
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-primary-subtle text-primary fw-bold">Rp</span>
                            <input type="number" name="daily_transport"
                                   class="form-control @error('daily_transport') is-invalid @enderror"
                                   value="{{ old('daily_transport', (int)$scheme->daily_transport) }}"
                                   min="0" step="1000" required>
                            <span class="input-group-text text-muted">/ hari hadir</span>
                        </div>
                        <small class="text-muted">Diberikan untuk setiap hari kehadiran sah yang tercatat di sistem.</small>
                    </div>

                    {{-- Uang Makan --}}
                    <div class="mb-4 p-3 bg-success-subtle rounded border border-success-subtle">
                        <div class="fw-semibold text-success mb-2">
                            <i class="bi bi-egg-fried me-1"></i> Uang Makan
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-success-subtle text-success fw-bold">Rp</span>
                            <input type="number" name="meal_allowance"
                                   class="form-control @error('meal_allowance') is-invalid @enderror"
                                   value="{{ old('meal_allowance', (int)$scheme->meal_allowance) }}"
                                   min="0" step="1000" required>
                            <span class="input-group-text text-muted">/ hari</span>
                        </div>
                        <small class="text-muted">Diberikan untuk hari dengan jam kerja <strong>≥ 6 jam</strong>.</small>
                    </div>

                    {{-- Lembur --}}
                    <div class="mb-4 p-3 bg-warning-subtle rounded border border-warning-subtle">
                        <div class="fw-semibold text-warning-emphasis mb-2">
                            <i class="bi bi-clock-history me-1"></i> Tarif Lembur
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-warning-subtle text-warning-emphasis fw-bold">Rp</span>
                            <input type="number" name="overtime_hourly_rate"
                                   class="form-control @error('overtime_hourly_rate') is-invalid @enderror"
                                   value="{{ old('overtime_hourly_rate', (int)$scheme->overtime_hourly_rate) }}"
                                   min="0" step="1000" required>
                            <span class="input-group-text text-muted">/ jam lembur</span>
                        </div>
                        <small class="text-muted">Diberikan untuk kelebihan jam kerja <strong>&gt; 8 jam/hari</strong>.</small>
                    </div>

                    {{-- Potongan --}}
                    <div class="p-3 bg-danger-subtle rounded border border-danger-subtle">
                        <div class="fw-semibold text-danger mb-2">
                            <i class="bi bi-dash-circle me-1"></i> Potongan Keterlambatan
                        </div>
                        <div class="input-group">
                            <span class="input-group-text bg-danger-subtle text-danger fw-bold">Rp</span>
                            <input type="number" name="late_deduction"
                                   class="form-control @error('late_deduction') is-invalid @enderror"
                                   value="{{ old('late_deduction', (int)$scheme->late_deduction) }}"
                                   min="0" step="1000" required>
                            <span class="input-group-text text-muted">/ kejadian telat</span>
                        </div>
                        <small class="text-muted">Dipotong jika relawan absen melebihi toleransi keterlambatan yang ditentukan.</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- Preview Simulasi --}}
        <div class="col-12">
            <div class="card shadow-sm border-info">
                <div class="card-header bg-info-subtle py-3">
                    <span class="fw-bold text-info"><i class="bi bi-calculator me-1"></i> Simulasi Perhitungan Honor</span>
                    <small class="text-muted ms-2">(update otomatis)</small>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Divisi</label>
                            <select id="simDiv" class="form-select form-select-sm">
                                @foreach($divisions as $div)
                                <option value="{{ $div->daily_rate }}">{{ $div->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Hari Hadir</label>
                            <input type="number" id="simDays" class="form-control form-control-sm" value="22" min="0" max="31">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Hari ≥ 6 jam</label>
                            <input type="number" id="simMealDays" class="form-control form-control-sm" value="22" min="0" max="31">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small fw-semibold">Jam Lembur</label>
                            <input type="number" id="simOvertime" class="form-control form-control-sm" value="0" min="0" step="0.5">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-semibold">Estimasi Honor Bersih</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-success text-white fw-bold">Rp</span>
                                <input type="text" id="simResult" class="form-control fw-bold text-success fs-6" readonly>
                            </div>
                            <small id="simDetail" class="text-muted"></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mt-4">
        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
            <i class="bi bi-save me-1"></i> Simpan Skema Honor
        </button>
        <a href="{{ route('admin.payroll.create') }}" class="btn btn-outline-success px-4 py-2">
            <i class="bi bi-gear-wide-connected me-1"></i> Generate Payroll
        </a>
    </div>
</form>
@endsection

@push('scripts')
<script>
function formatRp(n) {
    return new Intl.NumberFormat('id-ID').format(Math.round(n));
}

function simulate() {
    const dailyRate   = parseFloat(document.getElementById('simDiv').value)     || 0;
    const days        = parseFloat(document.getElementById('simDays').value)     || 0;
    const mealDays    = parseFloat(document.getElementById('simMealDays').value) || 0;
    const overtime    = parseFloat(document.getElementById('simOvertime').value) || 0;

    const transport   = parseFloat(document.querySelector('[name=daily_transport]').value)      || 0;
    const meal        = parseFloat(document.querySelector('[name=meal_allowance]').value)        || 0;
    const lemburRate  = parseFloat(document.querySelector('[name=overtime_hourly_rate]').value)  || 0;

    const pokok    = dailyRate * days;
    const transp   = transport * days;
    const makan    = meal * mealDays;
    const lembur   = lemburRate * overtime;
    const total    = pokok + transp + makan + lembur;

    document.getElementById('simResult').value = formatRp(total);
    document.getElementById('simDetail').innerHTML =
        `Pokok: Rp ${formatRp(pokok)} + Transport: Rp ${formatRp(transp)} + Makan: Rp ${formatRp(makan)} + Lembur: Rp ${formatRp(lembur)}`;
}

// Bind semua input ke fungsi simulasi
document.addEventListener('DOMContentLoaded', function () {
    const inputs = ['simDiv','simDays','simMealDays','simOvertime'];
    inputs.forEach(id => document.getElementById(id)?.addEventListener('input', simulate));

    const fields = ['daily_transport','meal_allowance','overtime_hourly_rate'];
    fields.forEach(name => document.querySelector(`[name=${name}]`)?.addEventListener('input', simulate));

    // Update simDiv options saat division_rates berubah
    document.querySelectorAll('[name^="division_rates"]').forEach(inp => {
        inp.addEventListener('input', function() {
            const idx = this.name.match(/\[(\d+)\]/)?.[1];
            const opt = document.querySelector(`#simDiv option[data-id="${idx}"]`);
            if (opt) opt.value = this.value || 0;
            simulate();
        });
    });

    simulate();
});
</script>
@endpush
