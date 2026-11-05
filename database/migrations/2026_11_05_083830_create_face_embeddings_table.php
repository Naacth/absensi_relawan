<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel face_embeddings
     * Menyimpan vektor wajah (face embedding) relawan yang terenkripsi.
     * Setiap relawan hanya memiliki satu embedding aktif.
     */
    public function up(): void
    {
        Schema::create('face_embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Relawan pemilik embedding');
            $table->longText('embedding')->comment('Vektor wajah terenkripsi (AES-256)');
            $table->string('photo_path')->nullable()->comment('Path foto referensi (opsional, disimpan sementara)');
            $table->timestamps();

            // Pastikan satu relawan hanya punya satu embedding aktif
            $table->unique('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('face_embeddings');
    }
};
