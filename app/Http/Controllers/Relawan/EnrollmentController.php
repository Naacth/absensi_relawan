<?php

namespace App\Http\Controllers\Relawan;

use App\Http\Controllers\Controller;
use App\Models\FaceEmbedding;
use App\Services\FaceRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    protected FaceRecognitionService $faceService;

    public function __construct(FaceRecognitionService $faceService)
    {
        $this->faceService = $faceService;
    }

    public function index()
    {
        $user = Auth::user();
        return view('relawan.enrollment.index', compact('user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|string', // base64
        ]);

        $user = Auth::user();

        // Daftarkan wajah via ARSA Face API (atau dummy mode)
        $enrollResult = $this->faceService->enrollFace($request->photo);

        if (!$enrollResult['success']) {
            return response()->json([
                'success' => false,
                'message' => $enrollResult['message'],
            ], 422);
        }

        $faceId = $enrollResult['face_id'];

        // Simpan face_id dan placeholder embedding terenkripsi
        $user->update([
            'face_enrolled' => true,
            'face_id'       => $faceId,
        ]);

        FaceEmbedding::updateOrCreate(
            ['user_id' => $user->id],
            ['embedding' => encrypt(json_encode(['face_id' => $faceId, 'enrolled_at' => now()->toIso8601String()]))]
        );

        return response()->json([
            'success' => true,
            'message' => 'Wajah berhasil didaftarkan! Anda sekarang siap melakukan absensi.',
        ]);
    }
}
