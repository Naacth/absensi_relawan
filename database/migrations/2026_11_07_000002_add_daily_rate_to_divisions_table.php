<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            // Gaji harian per divisi (0 = ikuti skema global)
            $table->decimal('daily_rate', 12, 2)->default(0)
                ->after('description')
                ->comment('Honor per hari kehadiran. 0 = pakai skema gaji global.');
        });
    }

    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            $table->dropColumn('daily_rate');
        });
    }
};
