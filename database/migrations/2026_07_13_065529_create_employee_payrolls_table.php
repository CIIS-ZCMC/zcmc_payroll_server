<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('employee_time_record_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();
            $table->foreignId('payroll_run_id')->constrained();

            $table->decimal('basic_pay', 15, 2);
            $table->decimal('total_receivables', 15, 2)->default(0);
            $table->decimal('gross_pay', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('total_adjustments', 15, 2)->default(0);
            $table->decimal('net_pay', 15, 2);
            $table->decimal('first_half', 15, 2)->default(0);
            $table->decimal('second_half', 15, 2)->default(0);

            $table->dateTime('locked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['employee_time_record_id', 'payroll_run_id'], 'employee_payroll_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payrolls');
    }
};