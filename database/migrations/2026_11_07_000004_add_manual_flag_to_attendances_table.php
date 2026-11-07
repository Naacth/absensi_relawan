<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->boolean('is_manual')->default(false)->after('total_hours')
                ->comment('true jika diinput manual oleh admin');
            $table->foreignId('manual_by')->nullable()->after('is_manual')
                ->constrained('users')->onDelete('set null')
                ->comment('Admin yang melakukan input manual');
            $table->text('manual_note')->nullable()->after('manual_by')
                ->comment('Catatan alasan input manual');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['manual_by']);
            $table->dropColumn(['is_manual', 'manual_by', 'manual_note']);
        });
    }
};
