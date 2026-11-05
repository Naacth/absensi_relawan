<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceRecognitionService
{
    /**
     * Mendaftarkan wajah baru ke ARSA Face API (atau dummy) dan mengembalikan face_id unik.
     *
     * @param string $photoBase64
     * @return array [ 'success' => bool, 'face_id' => string, 'message' => string ]
     */
    public function enrollFace(string $photoBase64): array
    {
        $mode = env('FACE_API_MODE', 'dummy');

        if ($mode === 'dummy') {
            return [
                'success' => true,
                'face_id' => 'arsa_face_' . uniqid(),
                'message' => 'Pendaftaran wajah berhasil (Simulasi Dummy Mode)',
            ];
        }

        $apiKey = Setting::get('arsa_api_key', env('ARSA_API_KEY'));
        $apiUrl = env('ARSA_API_URL', 'https://api.arsa.ai/v1');

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept'        => 'application/json',
            ])->post("{$apiUrl}/faces/enroll", [
                'image' => $photoBase64,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'success' => true,
                    'face_id' => $data['face_id'] ?? ('arsa_' . uniqid()),
                    'message' => 'Wajah berhasil didaftarkan ke ARSA API.',
                ];
            }

            Log::error('ARSA Face Enroll Error: ' . $response->body());
            return [
                'success' => false,
                'face_id' => null,
                'message' => 'Gagal mendaftarkan wajah ke ARSA API: ' . ($response->json()['message'] ?? 'Respons tidak valid'),
            ];
        } catch (\Throwable $e) {
            Log::error('ARSA Face Enroll Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'face_id' => null,
                'message' => 'Terjadi kesalahan koneksi ke ARSA API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Memverifikasi wajah relawan saat absensi.
     * Menguji kecocokan dengan face_id yang terdaftar dan memeriksa liveness.
     *
     * @param User $user
     * @param string $photoBase64
     * @return array [ 'success' => bool, 'match_score' => float, 'liveness' => bool, 'message' => string ]
     */
    public function verifyFace(User $user, string $photoBase64): array
    {
        $mode = env('FACE_API_MODE', 'dummy');

        if ($mode === 'dummy') {
            // Nilai simulasi acak tinggi (0.92 - 0.98) dan liveness lolos
            $simulatedScore = round(0.92 + (mt_rand(0, 60) / 1000), 4);
            return [
                'success'     => true,
                'match_score' => $simulatedScore,
                'liveness'    => true,
                'message'     => 'Verifikasi wajah lolos (Simulasi ARSA Dummy Mode)',
            ];
        }

        $apiKey = Setting::get('arsa_api_key', env('ARSA_API_KEY'));
        $apiUrl = env('ARSA_API_URL', 'https://api.arsa.ai/v1');

        try {
            $response = Http::timeout(10)->withHeaders([
                'Authorization' => "Bearer {$apiKey}",
                'Accept'        => 'application/json',
            ])->post("{$apiUrl}/faces/verify", [
                'face_id' => $user->face_id,
                'image'   => $photoBase64,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $score    = (float) ($data['match_score'] ?? 0);
                $liveness = (bool)  ($data['liveness'] ?? false);

                return [
                    'success'     => ($score >= 0.85 && $liveness),
                    'match_score' => $score,
                    'liveness'    => $liveness,
                    'message'     => 'Verifikasi wajah diproses oleh ARSA API',
                ];
            }

            return [
                'success'     => false,
                'match_score' => 0,
                'liveness'    => false,
                'message'     => 'ARSA API mengembalikan penolakan verifikasi.',
            ];
        } catch (\Throwable $e) {
            Log::error('ARSA Face Verify Exception: ' . $e->getMessage());
            return [
                'success'     => false,
                'match_score' => 0,
                'liveness'    => false,
                'message'     => 'Koneksi ke ARSA API bermasalah: ' . $e->getMessage(),
            ];
        }
    }
}

