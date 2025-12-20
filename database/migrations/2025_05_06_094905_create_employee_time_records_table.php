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

            $table->text('minutes'); //Rate
            $table->text('daily'); //Rate
            $table->text('hourly'); //Rate
            $table->text('absent_rate');
            $table->text('undertime_rate');
            $table->text('base_salary');
            $table->text('basic_pay')->comment('basic_pay of employee, receivables is not included');
            // $table->text('net_pay')->comment('basic_pay of employee, receivables is not included');

            $table->double('total_working_minutes');
            $table->double('total_working_minutes_with_leave');
            $table->double('total_working_hours');
            $table->double('total_working_hours_with_leave');
            $table->double('total_overtime_minutes');
            $table->double('total_undertime_minutes');
            $table->double('total_official_business_minutes');
            $table->double('total_official_time_minutes');
            $table->double('total_leave_minutes');
            $table->double('total_night_duty_hours');
            $table->double('no_of_present_days');
            $table->double('no_of_present_days_with_leave');
            $table->double('no_of_leave_wo_pay');
            $table->double('no_of_leave_w_pay');
            $table->double('no_of_absences');
            $table->double('no_of_invalid_entry');
            $table->double('no_of_day_off');
            $table->double('no_of_schedule');
            $table->longText('night_differentials');
            $table->longText('absent_dates');
            // $table->longText('schedules');
            $table->string('month');
            $table->string('year');
            $table->string('from');
            $table->string('to');
            // $table->boolean('is_night_shift');
            $table->boolean('is_active')->default(true);
            $table->string('status');
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
