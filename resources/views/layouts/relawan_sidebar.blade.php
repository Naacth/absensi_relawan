<ul class="nav flex-column gap-1">
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('relawan.dashboard') ? 'active' : '' }}" href="{{ route('relawan.dashboard') }}">
            <i class="bi bi-house me-2"></i>Beranda
        </a>
    </li>
    @if(!auth()->user()->face_enrolled)
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('relawan.enrollment.*') ? 'active' : '' }}" href="{{ route('relawan.enrollment.index') }}">
            <i class="bi bi-camera me-2"></i>Daftar Wajah
        </a>
    </li>
    @endif
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('relawan.absensi.*') ? 'active' : '' }}" href="{{ route('relawan.absensi.index') }}">
            <i class="bi bi-geo-alt me-2"></i>Absensi (Wajah & GPS)
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('relawan.leave.*') ? 'active' : '' }}" href="{{ route('relawan.leave.index') }}">
            <i class="bi bi-envelope-paper me-2"></i>Pengajuan Izin / Cuti
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('relawan.payroll.*') ? 'active' : '' }}" href="{{ route('relawan.payroll.index') }}">
            <i class="bi bi-receipt me-2"></i>Slip Honor Saya
        </a>
    </li>
</ul>

