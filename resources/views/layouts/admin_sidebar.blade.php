<ul class="nav flex-column gap-1">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.relawan.*') ? 'active' : '' }}" href="{{ route('admin.relawan.index') }}">
            <i class="bi bi-people me-2"></i>Data Relawan
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.divisions.*') ? 'active' : '' }}" href="{{ route('admin.divisions.index') }}">
            <i class="bi bi-diagram-3 me-2"></i>Divisi & Jadwal
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.leave.*') ? 'active' : '' }}" href="{{ route('admin.leave.index') }}">
            <i class="bi bi-envelope-paper me-2"></i>Permohonan Izin
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.payroll.*') ? 'active' : '' }}" href="{{ route('admin.payroll.index') }}">
            <i class="bi bi-cash-stack me-2"></i>Payroll & Honor
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}" href="{{ route('admin.laporan.index') }}">
            <i class="bi bi-file-earmark-bar-graph me-2"></i>Laporan Rekap
        </a>
    </li>

    @if(auth()->user()->isSuperAdmin())
    <li class="nav-item mt-2">
        <div class="text-uppercase text-secondary small px-3 mb-1" style="font-size: 0.72rem; letter-spacing: 0.5px;">Super Admin</div>
        <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" href="{{ route('admin.settings.index') }}">
            <i class="bi bi-sliders me-2"></i>Pengaturan Sistem
        </a>
    </li>
    @endif
</ul>

