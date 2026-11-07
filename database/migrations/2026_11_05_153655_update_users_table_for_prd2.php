<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah kolom division_id dan face_id ke tabel users
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'division_id')) {
                $table->foreignId('division_id')->nullable()->after('role')->constrained('divisions')->onDelete('set null');
            }
            if (!Schema::hasColumn('users', 'face_id')) {
                $table->string('face_id')->nullable()->after('face_enrolled')->comment('ID Wajah dari ARSA API');
            }
        });

        // Ubah enum role hanya di MySQL (tidak diperlukan di SQLite karena SQLite tidak punya strict enum)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('super_admin', 'admin', 'relawan') DEFAULT 'relawan'");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'division_id')) {
                $table->dropForeign(['division_id']);
                $table->dropColumn('division_id');
            }
            if (Schema::hasColumn('users', 'face_id')) {
                $table->dropColumn('face_id');
            }
        });

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'relawan') DEFAULT 'relawan'");
        }
    }
};
