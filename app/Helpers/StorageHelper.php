<?php

if (!function_exists('storage_url')) {
    /**
     * Generate URL untuk file di storage publik.
     * - Di local/development: pakai asset('storage/...') — butuh symlink
     * - Di production/shared hosting: pakai route storage-file untuk serve via PHP
     *
     * @param string|null $path Path relatif dari storage/app/public/
     * @return string|null
     */
    function storage_url(?string $path): ?string
    {
        if (!$path) return null;

        // Jika APP_ENV=production atau STORAGE_SERVE_VIA_PHP=true, pakai route controller
        if (config('app.env') === 'production' || env('STORAGE_SERVE_VIA_PHP', false)) {
            return route('storage.file', ['path' => $path]);
        }

        // Local: pakai symlink biasa
        return asset('storage/' . $path);
    }
}
