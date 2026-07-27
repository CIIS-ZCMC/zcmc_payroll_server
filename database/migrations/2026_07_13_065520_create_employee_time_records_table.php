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
        Schema::create('employee_time_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();

            $table->decimal('total_working_minutes', 10, 2)->default(0);
            $table->decimal('total_working_minutes_with_leave', 10, 2)->default(0);
            $table->decimal('total_working_hours', 10, 2)->default(0);
            $table->decimal('total_working_hours_with_leave', 10, 2)->default(0);
            $table->decimal('total_overtime_minutes', 10, 2)->default(0);
            $table->decimal('total_undertime_minutes', 10, 2)->default(0);
            $table->decimal('total_official_business_minutes', 10, 2)->default(0);
            $table->decimal('total_official_time_minutes', 10, 2)->default(0);
            $table->decimal('total_leave_minutes', 10, 2)->default(0);
            $table->decimal('total_night_duty_hours', 10, 2)->default(0);
           
            $table->decimal('no_of_present_days', 10, 2)->default(0);
            $table->decimal('no_of_present_days_with_leave', 10, 2)->default(0);
            $table->decimal('no_of_leave_wo_pay', 10, 2)->default(0);
            $table->decimal('no_of_leave_w_pay', 10, 2)->default(0);
            $table->decimal('no_of_absences', 10, 2)->default(0);
            $table->decimal('no_of_invalid_entry', 10, 2)->default(0);
            $table->decimal('no_of_day_off', 10, 2)->default(0);
            $table->decimal('no_of_schedule', 10, 2)->default(0);
           
            $table->longText('night_duties')->nullable();
            $table->longText('absent_dates')->nullable();
           
            $table->string('status')->default('draft');
            $table->boolean('is_active')->default(true);
            $table->dateTime('locked_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id'], 'employee_time_records_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_time_records');
    }
};
