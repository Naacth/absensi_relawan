<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel attendances
     * Menyimpan satu record per relawan per hari.
     * Berisi waktu masuk, waktu pulang, skor verifikasi, dan total jam kerja.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Relawan yang absen');
            $table->date('date')->comment('Tanggal absensi');

            // --- Absen Masuk ---
            $table->timestamp('check_in_at')->nullable()->comment('Waktu masuk (timestamp server)');
            $table->float('check_in_score')->nullable()->comment('Skor kecocokan wajah saat masuk (0-1)');
            $table->boolean('check_in_liveness')->nullable()->comment('Status liveness saat masuk');
            $table->string('check_in_snapshot')->nullable()->comment('Path snapshot masuk (sementara)');

            // --- Absen Pulang ---
            $table->timestamp('check_out_at')->nullable()->comment('Waktu pulang (timestamp server)');
            $table->float('check_out_score')->nullable()->comment('Skor kecocokan wajah saat pulang (0-1)');
            $table->boolean('check_out_liveness')->nullable()->comment('Status liveness saat pulang');
            $table->string('check_out_snapshot')->nullable()->comment('Path snapshot pulang (sementara)');

            // --- Rekapitulasi ---
            $table->decimal('total_hours', 5, 2)->nullable()->comment('Total jam kerja = check_out - check_in');

            $table->timestamps();

            // Satu relawan hanya punya satu record per hari (mencegah duplikasi)
            $table->unique(['user_id', 'date']);
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
