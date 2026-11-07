<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('division_id')->constrained('divisions')->onDelete('cascade');
            $table->tinyInteger('day_of_week')->nullable()->comment('NULL = berlaku semua hari, 0=Minggu, 1=Senin, ..., 6=Sabtu');
            $table->time('check_in_start')->default('06:00:00');
            $table->time('check_in_end')->default('08:30:00');
            $table->time('check_out_start')->default('14:00:00');
            $table->time('check_out_end')->default('18:00:00');
            $table->integer('late_tolerance')->default(15)->comment('Toleransi keterlambatan (menit)');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
