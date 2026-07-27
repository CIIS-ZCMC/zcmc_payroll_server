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
        Schema::create('payroll_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_run_id')->constrained()->unique();
            
            $table->integer('total_employees');
            $table->decimal('total_gross', 15, 2);
            $table->decimal('total_net', 15, 2);
            $table->decimal('total_deductions', 15, 2);
            $table->decimal('total_receivables', 15, 2);
            $table->decimal('total_overtime_pay', 15, 2)->default(0);
            $table->decimal('total_night_diff_pay', 15, 2)->default(0);
            $table->decimal('total_absent_deduction', 15, 2)->default(0);
            $table->decimal('total_undertime_deduction', 15, 2)->default(0);
            $table->decimal('total_late_deduction', 15, 2)->default(0);
            $table->decimal('total_adjustments', 15, 2)->default(0);
            $table->integer('total_absences')->default(0);
            $table->integer('total_undertime_minutes')->default(0);
            $table->integer('total_overtime_minutes')->default(0);
            
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_summaries');
    }
};