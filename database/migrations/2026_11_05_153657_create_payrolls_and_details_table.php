<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('title')->comment('Judul Payroll, contoh: Payroll November 2026');
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['draft', 'approved', 'paid'])->default('draft');
            $table->decimal('total_disbursed', 14, 2)->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_id')->constrained('payrolls')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->integer('total_days_attended')->default(0)->comment('Total hari hadir sah');
            $table->integer('total_days_leave')->default(0)->comment('Total hari izin resmi');
            $table->decimal('total_hours', 7, 2)->default(0)->comment('Total akumulasi jam kerja');
            $table->decimal('base_salary', 12, 2)->default(0)->comment('Honor jam kerja');
            $table->decimal('transport_allowance', 12, 2)->default(0)->comment('Uang transport');
            $table->decimal('meal_allowance', 12, 2)->default(0)->comment('Uang makan');
            $table->decimal('overtime_pay', 12, 2)->default(0)->comment('Honor lembur');
            $table->decimal('deductions', 12, 2)->default(0)->comment('Potongan');
            $table->decimal('net_salary', 12, 2)->default(0)->comment('Total honor bersih yang diterima');
            $table->enum('payment_status', ['unpaid', 'paid'])->default('unpaid');
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->unique(['payroll_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_details');
        Schema::dropIfExists('payrolls');
    }
};
