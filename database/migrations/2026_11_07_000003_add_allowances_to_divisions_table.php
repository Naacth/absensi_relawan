<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->decimal('daily_transport', 12, 2)->default(0)->after('daily_rate')
                ->comment('Uang transport per hari hadir');
            $table->decimal('meal_allowance', 12, 2)->default(0)->after('daily_transport')
                ->comment('Uang makan per hari (jika >= 6 jam kerja)');
            $table->decimal('overtime_hourly_rate', 12, 2)->default(0)->after('meal_allowance')
                ->comment('Tarif lembur per jam (jam > 8/hari)');
            $table->decimal('late_deduction', 12, 2)->default(0)->after('overtime_hourly_rate')
                ->comment('Potongan per keterlambatan');
        });

        // Isi nilai default dari SalaryScheme global yang sudah ada
        $scheme = DB::table('salary_schemes')->where('is_active', true)->first();
        if ($scheme) {
            DB::table('divisions')->update([
                'daily_transport'      => $scheme->daily_transport,
                'meal_allowance'       => $scheme->meal_allowance,
                'overtime_hourly_rate' => $scheme->overtime_hourly_rate,
                'late_deduction'       => $scheme->late_deduction,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn(['daily_transport', 'meal_allowance', 'overtime_hourly_rate', 'late_deduction']);
        });
    }
};
