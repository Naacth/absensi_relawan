<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\DivisionController;
use App\Http\Controllers\Admin\LeaveRequestController as AdminLeave;
use App\Http\Controllers\Admin\PayrollController as AdminPayroll;
use App\Http\Controllers\Admin\RelawanController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Relawan\DashboardController as RelawanDashboard;
use App\Http\Controllers\Relawan\AbsensiController;
use App\Http\Controllers\Relawan\EnrollmentController;
use App\Http\Controllers\Relawan\LeaveRequestController as RelawanLeave;
use App\Http\Controllers\Relawan\PayrollController as RelawanPayroll;
use Illuminate\Support\Facades\Route;

// ─── Redirect root ───────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));

// ─── Auth ─────────────────────────────────────────────────────
Route::get('/login',   [AuthController::class, 'showLogin'])->name('login');
Route::post('/login',  [AuthController::class, 'login'])->name('login.post')->middleware('throttle:5,1');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ─── Admin & Super Admin ──────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Pengaturan Sistem & Skema Gaji (Khusus Super Admin)
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/pengaturan',  [SettingController::class, 'index'])->name('settings.index');
        Route::post('/pengaturan', [SettingController::class, 'update'])->name('settings.update');
    });

    // Manajemen Divisi & Jadwal Kerja
    Route::resource('divisions', DivisionController::class)->except(['show', 'create']);

    // Manajemen User / Relawan
    Route::get('/relawan',                          [RelawanController::class, 'index'])->name('relawan.index');
    Route::get('/relawan/tambah',                   [RelawanController::class, 'create'])->name('relawan.create');
    Route::post('/relawan',                         [RelawanController::class, 'store'])->name('relawan.store');
    Route::get('/relawan/{relawan}/edit',           [RelawanController::class, 'edit'])->name('relawan.edit');
    Route::put('/relawan/{relawan}',                [RelawanController::class, 'update'])->name('relawan.update');
    Route::patch('/relawan/{relawan}/toggle-active',[RelawanController::class, 'toggleActive'])->name('relawan.toggleActive');
    Route::delete('/relawan/{relawan}/reset-wajah', [RelawanController::class, 'resetWajah'])->name('relawan.resetWajah');

    // Manajemen Izin & Sakit (Leave Management)
    Route::get('/izin',                       [AdminLeave::class, 'index'])->name('leave.index');
    Route::patch('/izin/{leave}/approve',     [AdminLeave::class, 'approve'])->name('leave.approve');
    Route::patch('/izin/{leave}/reject',      [AdminLeave::class, 'reject'])->name('leave.reject');

    // Penggajian & Payroll Otomatis
    Route::get('/payroll',                        [AdminPayroll::class, 'index'])->name('payroll.index');
    Route::get('/payroll/generate',               [AdminPayroll::class, 'create'])->name('payroll.create');
    Route::post('/payroll/generate',              [AdminPayroll::class, 'generate'])->name('payroll.generate');
    Route::get('/payroll/{payroll}',              [AdminPayroll::class, 'show'])->name('payroll.show');
    Route::patch('/payroll/{payroll}/approve',    [AdminPayroll::class, 'approve'])->name('payroll.approve');
    Route::patch('/payroll/{payroll}/mark-paid',  [AdminPayroll::class, 'markAsPaid'])->name('payroll.markPaid');
    Route::get('/payroll/{payroll}/export-csv',   [AdminPayroll::class, 'exportCsv'])->name('payroll.exportCsv');
    Route::get('/payroll/slip/{detail}/pdf',      [AdminPayroll::class, 'slipPdf'])->name('payroll.slipPdf');

    // Laporan Kehadiran Bulanan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.exportPdf');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.exportExcel');

    // Absensi Manual oleh Admin
    Route::get('/absensi-manual',              [\App\Http\Controllers\Admin\AbsensiManualController::class, 'index'])->name('absensi-manual.index');
    Route::post('/absensi-manual',             [\App\Http\Controllers\Admin\AbsensiManualController::class, 'store'])->name('absensi-manual.store');
    Route::delete('/absensi-manual/{attendance}', [\App\Http\Controllers\Admin\AbsensiManualController::class, 'destroy'])->name('absensi-manual.destroy');
});

// ─── Relawan ──────────────────────────────────────────────────
Route::prefix('relawan')->name('relawan.')->middleware(['auth', 'role:relawan'])->group(function () {
    Route::get('/dashboard',  [RelawanDashboard::class, 'index'])->name('dashboard');

    // Enrollment wajah
    Route::get('/enrollment',  [EnrollmentController::class, 'index'])->name('enrollment.index');
    Route::post('/enrollment', [EnrollmentController::class, 'store'])->name('enrollment.store');

    // Absensi (Wajah + GPS Geofencing + Watermark)
    Route::get('/absensi',  [AbsensiController::class, 'index'])->name('absensi.index');
    Route::post('/absensi', [AbsensiController::class, 'store'])->name('absensi.store')->middleware('throttle:10,1');

    // Pengajuan Izin / Sakit
    Route::get('/izin',        [RelawanLeave::class, 'index'])->name('leave.index');
    Route::get('/izin/ajukan', [RelawanLeave::class, 'create'])->name('leave.create');
    Route::post('/izin',       [RelawanLeave::class, 'store'])->name('leave.store');

    // Riwayat Slip Honor Pribadi
    Route::get('/payroll',            [RelawanPayroll::class, 'index'])->name('payroll.index');
    Route::get('/payroll/slip/{detail}', [RelawanPayroll::class, 'slipPdf'])->name('payroll.slip');
});
