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
        Schema::create('employee_computed_salaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_run_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();
            $table->foreignId('employee_time_record_id')->constrained();
            
            $table->decimal('basic_pay', 15, 4)->comment('night diff not included');
            $table->decimal('minutes_rate', 15, 4);
            $table->decimal('daily_rate', 15, 4);
            $table->decimal('hourly_rate', 15, 4);
            $table->decimal('absent_rate', 15, 4)->default(0);
            $table->decimal('undertime_rate', 15, 4)->default(0);

            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['employee_id', 'payroll_run_id', 'employee_time_record_id'], 'employee_computed_salaries_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_computed_salaries');
    }
};