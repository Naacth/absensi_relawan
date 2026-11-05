<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel attendance_logs
     * Mencatat SETIAP percobaan absensi, baik yang berhasil maupun yang gagal.
     * Tabel ini bersifat append-only (tidak boleh dihapus permanen) untuk audit trail.
     */
    public function up(): void
    {
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();

            // user_id nullable: jika wajah tidak dikenali sama sekali
            $table->foreignId('user_id')
                  ->nullable()
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Relawan yang mencoba absen; null jika wajah tidak dikenali');

            $table->enum('type', ['in', 'out'])->comment('Jenis absensi: masuk atau pulang');
            $table->enum('status', ['success', 'failed'])->comment('Hasil percobaan absensi');
            $table->string('reason')->nullable()->comment('Alasan gagal: wajah tidak cocok, liveness gagal, di luar jadwal, dsb.');
            $table->float('match_score')->nullable()->comment('Skor kecocokan wajah dari Face Recognition API');
            $table->boolean('liveness_passed')->nullable()->comment('Apakah liveness check lolos');

            // Informasi klien untuk audit keamanan
            $table->string('ip_address', 45)->nullable()->comment('IP address klien');
            $table->text('user_agent')->nullable()->comment('Browser/user agent klien');
            $table->timestamp('client_timestamp')->nullable()->comment('Waktu dari sisi klien (hanya untuk audit, bukan acuan utama)');

            // Hanya created_at, tidak ada updated_at karena log tidak boleh diubah
            $table->timestamp('created_at')->useCurrent()->comment('Waktu log dicatat (timestamp server)');

            $table->index(['user_id', 'created_at']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
    }
};
