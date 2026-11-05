<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('check_in_lat', 10, 7)->nullable()->after('check_in_liveness')->comment('GPS Latitude Masuk');
            $table->decimal('check_in_lng', 10, 7)->nullable()->after('check_in_lat')->comment('GPS Longitude Masuk');
            $table->decimal('distance_m', 8, 2)->nullable()->after('check_in_lng')->comment('Jarak ke SPPG saat masuk (meter)');
            $table->string('photo_check_in_path')->nullable()->after('distance_m')->comment('Foto ber-watermark saat masuk');

            $table->decimal('check_out_lat', 10, 7)->nullable()->after('check_out_liveness')->comment('GPS Latitude Pulang');
            $table->decimal('check_out_lng', 10, 7)->nullable()->after('check_out_lat')->comment('GPS Longitude Pulang');
            $table->decimal('distance_out_m', 8, 2)->nullable()->after('check_out_lng')->comment('Jarak ke SPPG saat pulang (meter)');
            $table->string('photo_check_out_path')->nullable()->after('distance_out_m')->comment('Foto ber-watermark saat pulang');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_lat', 'check_in_lng', 'distance_m', 'photo_check_in_path',
                'check_out_lat', 'check_out_lng', 'distance_out_m', 'photo_check_out_path'
            ]);
        });
    }
};
