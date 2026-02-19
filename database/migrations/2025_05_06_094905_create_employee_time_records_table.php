<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeTimeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_time_records', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->unsignedBigInteger('payroll_period_id');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            // $table->text('minutes'); //Rate
            // $table->text('daily'); //Rate
            // $table->text('hourly'); //Rate
            // $table->text('absent_rate');
            // $table->text('undertime_rate');
            // $table->text('base_salary');
            // $table->text('basic_pay')->comment('basic_pay of employee, receivables is not included');

            $table->decimal('total_working_minutes', 10, 2);
            $table->decimal('total_working_minutes_with_leave', 10, 2);
            $table->decimal('total_working_hours', 10, 2);
            $table->decimal('total_working_hours_with_leave', 10, 2);
            $table->decimal('total_overtime_minutes', 10, 2);
            $table->decimal('total_undertime_minutes', 10, 2);
            $table->decimal('total_official_business_minutes', 10, 2);
            $table->decimal('total_official_time_minutes', 10, 2);
            $table->decimal('total_leave_minutes', 10, 2);
            $table->decimal('total_night_duty_hours', 10, 2);

            $table->decimal('no_of_present_days', 10, 2);
            $table->decimal('no_of_present_days_with_leave', 10, 2);
            $table->decimal('no_of_leave_wo_pay', 10, 2);
            $table->decimal('no_of_leave_w_pay', 10, 2);
            $table->decimal('no_of_absences', 10, 2);
            $table->decimal('no_of_invalid_entry', 10, 2);
            $table->decimal('no_of_day_off', 10, 2);
            $table->decimal('no_of_schedule', 10, 2);

            $table->longText('night_duties');
            $table->longText('absent_dates');

            // $table->longText('schedules');
            $table->string('month');
            $table->string('year');
            $table->string('from');
            $table->string('to');

            $table->string('status');
            $table->boolean('is_active')->default(true);

            $table->timestamp('locked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id'], 'employee_period_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_time_records');
    }
}
