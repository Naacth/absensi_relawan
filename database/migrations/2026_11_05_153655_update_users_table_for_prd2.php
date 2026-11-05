<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Ubah enum role menggunakan raw statement agar kompatibel dengan MySQL
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'relawan') DEFAULT 'relawan'");

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('division_id')->nullable()->after('role')->constrained('divisions')->onDelete('set null');
            $table->string('face_id')->nullable()->after('face_enrolled')->comment('ID Wajah dari ARSA API');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['division_id']);
            $table->dropColumn(['division_id', 'face_id']);
        });

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'relawan') DEFAULT 'relawan'");
    }
};
