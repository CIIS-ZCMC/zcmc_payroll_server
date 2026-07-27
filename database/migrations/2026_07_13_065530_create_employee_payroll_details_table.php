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
        Schema::create('employee_payroll_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->constrained()->unique();
           
            $table->decimal('basic_pay', 15, 2);
            $table->decimal('absent_deduction', 15, 2)->default(0);
            $table->decimal('undertime_deduction', 15, 2)->default(0);
            $table->decimal('late_deduction', 15, 2)->default(0);
            $table->decimal('overtime_pay', 15, 2)->default(0);
            $table->decimal('night_differential_pay', 15, 2)->default(0);
            $table->decimal('total_deductions', 15, 2)->default(0);
            $table->decimal('total_receivables', 15, 2)->default(0);
           
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_payroll_details');
    }
};