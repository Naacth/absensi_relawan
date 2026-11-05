<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_schemes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nama skema gaji');
            $table->decimal('hourly_rate', 10, 2)->default(15000)->comment('Honor per jam kerja');
            $table->decimal('daily_transport', 10, 2)->default(20000)->comment('Uang transport harian per kehadiran sah');
            $table->decimal('meal_allowance', 10, 2)->default(15000)->comment('Uang makan harian (jika > 6 jam)');
            $table->decimal('overtime_hourly_rate', 10, 2)->default(20000)->comment('Honor lembur per jam');
            $table->decimal('late_deduction', 10, 2)->default(5000)->comment('Potongan per keterlambatan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_schemes');
    }
};
