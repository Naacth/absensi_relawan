<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WatermarkService
{
    /**
     * Memproses gambar snapshot kamera, membubuhkan watermark server-side yang permanen,
     * dan menyimpannya ke storage publik.
     *
     * @param string $photoBase64 Gambar base64 dari kamera browser
     * @param User $user Relawan yang bersangkutan
     * @param string $type Jenis absensi ('in' atau 'out')
     * @param float|null $lat Latitude GPS
     * @param float|null $lng Longitude GPS
     * @param float|null $distance Jarak ke SPPG (meter)
     * @return string Path relatif file yang disimpan di storage publik
     */
    public static function processAndSave(
        string $photoBase64,
        User $user,
        string $type,
        ?float $lat = null,
        ?float $lng = null,
        ?float $distance = null
    ): string {
        // Bersihkan prefix data:image/...;base64,
        if (str_contains($photoBase64, ',')) {
            $photoBase64 = explode(',', $photoBase64)[1];
        }
        $rawImage = base64_decode($photoBase64);

        if (!$rawImage) {
            throw new \Exception('Gambar tidak valid.');
        }

        $image = @imagecreatefromstring($rawImage);
        if (!$image) {
            throw new \Exception('Gagal membaca resource gambar GD.');
        }

        $width  = imagesx($image);
        $height = imagesy($image);

        // Siapkan warna
        $blackOverlay = imagecolorallocatealpha($image, 10, 25, 47, 40); // latar belakang semi-transparan
        $whiteText    = imagecolorallocate($image, 255, 255, 255);
        $yellowText   = imagecolorallocate($image, 255, 215, 0);
        $greenText    = imagecolorallocate($image, 50, 205, 50);

        // Buat banner watermark di bagian bawah (tinggi 90px)
        $bannerHeight = 90;
        $bannerY = max(0, $height - $bannerHeight);
        imagefilledrectangle($image, 0, $bannerY, $width, $height, $blackOverlay);

        // Header watermark
        $jenisAbsen = $type === 'in' ? 'ABSEN MASUK' : 'ABSEN PULANG';
        $waktuServer = now()->timezone('Asia/Jakarta')->format('d M Y - H:i:s') . ' WIB';
        $divisi = $user->division ? $user->division->name : 'Umum';

        $line1 = "SPPG CURUG CUKANGGALIH 02 | {$jenisAbsen}";
        $line2 = "Relawan: {$user->name} | NIK: " . ($user->nik ?? '-') . " | Div: {$divisi}";
        $line3 = "Waktu Server: {$waktuServer}";
        
        $latStr = $lat ? number_format($lat, 6) : '-';
        $lngStr = $lng ? number_format($lng, 6) : '-';
        $distStr = $distance !== null ? number_format($distance, 1) . 'm dari SPPG' : '-';
        $line4 = "GPS: {$latStr}, {$lngStr} ({$distStr}) [TERVERIFIKASI]";

        // Tulis teks menggunakan imagestring (font 1 - 5 bawaan PHP GD, portable tanpa perlu font .ttf eksternal)
        imagestring($image, 3, 15, $bannerY + 8,  $line1, $yellowText);
        imagestring($image, 2, 15, $bannerY + 28, $line2, $whiteText);
        imagestring($image, 2, 15, $bannerY + 46, $line3, $whiteText);
        imagestring($image, 2, 15, $bannerY + 64, $line4, $greenText);

        // Buat folder bulanan
        $folder = 'absensi/' . now()->format('Y/m');
        $filename = 'absen_' . $user->id . '_' . $type . '_' . time() . '_' . Str::random(6) . '.jpg';
        $relativePath = $folder . '/' . $filename;

        // Simpan ke storage publik dengan kualitas 80% (ringan dan hemat memori Shared Hosting)
        ob_start();
        imagejpeg($image, null, 80);
        $compressedData = ob_get_clean();
        imagedestroy($image);

        Storage::disk('public')->put($relativePath, $compressedData);

        return $relativePath;
    }
}

