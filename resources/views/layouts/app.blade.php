<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Absensi & Payroll SPPG Curug Cukanggalih 02')</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
        .sidebar { min-height: 100vh; background: #0f172a; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .sidebar .nav-link { color: #94a3b8; font-size: 0.92rem; padding: 0.65rem 1rem; border-radius: 8px; transition: all 0.2s; }
        .sidebar .nav-link:hover { color: #f8fafc; background: rgba(255,255,255,0.08); }
        .sidebar .nav-link.active { color: #ffffff; background: #2563eb; font-weight: 600; }
        .sidebar-brand { color: #ffffff; font-weight: 700; font-size: 1.15rem; letter-spacing: -0.5px; }
        .card { border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.06); border-radius: 12px; }
    </style>
    @stack('styles')
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar py-3 px-3">
            <div class="sidebar-brand mb-4 ps-2 d-flex align-items-center">
                <i class="bi bi-shield-check text-primary fs-3 me-2"></i>
                <div>
                    <div>SPPG Absensi</div>
                    <small class="text-secondary fw-normal fs-6">Curug Cukanggalih 02</small>
                </div>
            </div>

            <div class="mb-3 px-2 py-2 bg-dark rounded border border-secondary border-opacity-25 text-white-50 small">
                <div class="text-white fw-bold text-truncate">{{ auth()->user()->name }}</div>
                <div class="badge bg-{{ auth()->user()->isSuperAdmin() ? 'danger' : (auth()->user()->isAdmin() ? 'primary' : 'success') }} mt-1">
                    {{ strtoupper(str_replace('_', ' ', auth()->user()->role)) }}
                </div>
                @if(auth()->user()->division)
                    <div class="small mt-1 text-info">{{ auth()->user()->division->name }}</div>
                @endif
            </div>

            @yield('sidebar')

            <hr class="border-secondary border-opacity-25 my-3">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-sm btn-outline-danger w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </nav>

        <!-- Main content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @yield('content')
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
