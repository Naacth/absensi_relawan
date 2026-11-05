<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Kirim pesan teks via Fonnte WA Gateway API
     *
     * @param string $phone
     * @param string $message
     * @return bool
     */
    public static function sendMessage(string $phone, string $message): bool
    {
        // Normalisasi nomor telepon ke format 62xxx atau 08xxx
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '08')) {
            $phone = '628' . substr($phone, 2);
        }

        $mode = env('FONNTE_API_MODE', 'dummy');

        if ($mode === 'dummy') {
            Log::info("[FONNTE WA DUMMY] To: {$phone} | Msg: {$message}");
            return true;
        }

        $token = Setting::get('fonnte_api_token', env('FONNTE_API_TOKEN'));
        if (!$token) {
            Log::warning('[FONNTE WA] Token API belum dikonfigurasi.');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target'  => $phone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("[FONNTE WA SUCCESS] To: {$phone}");
                return true;
            }

            Log::error("[FONNTE WA FAILED] To: {$phone} | Response: " . $response->body());
            return false;
        } catch (\Throwable $e) {
            Log::error('[FONNTE WA EXCEPTION] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Kirim notifikasi slip gaji telah diterbitkan
     */
    public static function notifyPayrollApproved(User $user, float $netSalary, string $period, string $slipUrl): void
    {
        if (!$user->phone) return;

        $nominalStr = 'Rp ' . number_format($netSalary, 0, ',', '.');
        $msg  = "Halo *{$user->name}*,\n\n";
        $msg .= "Slip honorarium Anda untuk periode *{$period}* telah disetujui oleh pengurus SPPG Curug Cukanggalih 02.\n";
        $msg .= "Total Bersih: *{$nominalStr}*\n\n";
        $msg .= "Rincian dan slip gaji resmi dapat Anda lihat melalui tautan berikut:\n";
        $msg .= "{$slipUrl}\n\n";
        $msg .= "_Pesan otomatis oleh Sistem Absensi SPPG Curug Cukanggalih 02_";

        static::sendMessage($user->phone, $msg);
    }

    /**
     * Kirim alert kecurangan / anomali absensi ke Admin/Super Admin
     */
    public static function alertAdminAnomaly(string $message): void
    {
        // Cari admin/super_admin dengan nomor telepon aktif
        $admins = User::whereIn('role', ['super_admin', 'admin'])
            ->whereNotNull('phone')
            ->where('is_active', true)
            ->get();

        foreach ($admins as $admin) {
            $text  = "🚨 *ALERT DETEKSI ANOMALI SPPG* 🚨\n\n";
            $text .= "{$message}\n\n";
            $text .= "Waktu Server: " . now()->format('d/m/Y H:i:s') . " WIB\n";
            $text .= "Mohon periksa Dashboard Log Audit Sistem.";

            static::sendMessage($admin->phone, $text);
        }
    }
}

