<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class StorageController extends Controller
{
    /**
     * Serve foto absensi yang diproteksi — hanya user yang login bisa akses.
     * Solusi untuk Shared Hosting yang tidak mendukung symlink storage.
     *
     * URL: /storage-file/absensi/2026/11/absen_27_in_xxx.jpg
     */
    public function serve(Request $request, string $path): Response
    {
        // Wajib login
        if (!Auth::check()) {
            abort(403, 'Akses ditolak.');
        }

        // Sanitasi path — cegah directory traversal
        $path = ltrim($path, '/');
        $path = str_replace(['..', '\\'], '', $path);

        // Hanya izinkan akses ke folder absensi dan leave
        if (!str_starts_with($path, 'absensi/') && !str_starts_with($path, 'leave/')) {
            abort(403, 'Akses ditolak.');
        }

        // Cek file ada
        if (!Storage::disk('public')->exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $file     = Storage::disk('public')->get($path);
        $mimeType = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';
        $size     = Storage::disk('public')->size($path);

        return response($file, 200, [
            'Content-Type'        => $mimeType,
            'Content-Length'      => $size,
            'Content-Disposition' => 'inline',
            'Cache-Control'       => 'private, max-age=3600',
        ]);
    }
}
