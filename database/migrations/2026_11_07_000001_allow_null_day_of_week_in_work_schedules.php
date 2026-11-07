<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Izinkan day_of_week menjadi NULL (jadwal tunggal berlaku semua hari)
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE work_schedules MODIFY COLUMN day_of_week TINYINT NULL COMMENT '0=Minggu, 1=Senin, ..., 6=Sabtu, NULL=Semua Hari'");
        } else {
            // SQLite: rebuild kolom via Blueprint
            Schema::table('work_schedules', function (Blueprint $table) {
                $table->tinyInteger('day_of_week')->nullable()->change();
            });
        }

        // 2. Hapus unique constraint lama (division_id + day_of_week) jika ada
        // agar 1 divisi bisa punya jadwal NULL tanpa konflik
        try {
            Schema::table('work_schedules', function (Blueprint $table) {
                $table->dropUnique(['division_id', 'day_of_week']);
            });
        } catch (\Throwable $e) {
            // Constraint mungkin tidak ada, abaikan
        }

        // 3. Migrasi data lama: ambil 1 jadwal per divisi (hari Senin/day=1)
        //    lalu buat jadwal tunggal (day_of_week = NULL) dari data itu
        $divisionIds = DB::table('work_schedules')
            ->select('division_id')
            ->distinct()
            ->pluck('division_id');

        foreach ($divisionIds as $divId) {
            // Cek apakah jadwal tunggal sudah ada
            $exists = DB::table('work_schedules')
                ->where('division_id', $divId)
                ->whereNull('day_of_week')
                ->exists();

            if (!$exists) {
                // Ambil jadwal Senin (day=1) atau hari pertama yang ada
                $source = DB::table('work_schedules')
                    ->where('division_id', $divId)
                    ->orderBy('day_of_week')
                    ->first();

                if ($source) {
                    DB::table('work_schedules')->insert([
                        'division_id'     => $divId,
                        'day_of_week'     => null,
                        'check_in_start'  => $source->check_in_start,
                        'check_in_end'    => $source->check_in_end,
                        'check_out_start' => $source->check_out_start,
                        'check_out_end'   => $source->check_out_end,
                        'late_tolerance'  => $source->late_tolerance,
                        'created_at'      => now(),
                        'updated_at'      => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        // Hapus semua jadwal tunggal (NULL) saat rollback
        DB::table('work_schedules')->whereNull('day_of_week')->delete();

        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE work_schedules MODIFY COLUMN day_of_week TINYINT NOT NULL COMMENT '0=Minggu, 1=Senin, ..., 6=Sabtu'");
        }
    }
};
