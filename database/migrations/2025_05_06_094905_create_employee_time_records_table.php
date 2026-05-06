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
            $table->double('minutes')->comment('rate');
            $table->double('daily')->comment('rate');
            $table->double('hourly')->comment('rate');
            $table->double('absent_rate');
            $table->double('undertime_rate');
            $table->double('base_salary');
            $table->double('initial_net_pay')->comment('holiday not include');
            $table->double('net_pay')->comment('holiday is include');
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
            $table->string('month');
            $table->string('year');
            $table->string('from')->nullable()->comment('period from , ex.1-15');
            $table->string('to')->nullable()->comment('period to , ex.16-31');
            $table->boolean('is_night_shift');
            $table->boolean('is_active')->default(0);
            $table->string('status')->nullable()->comment('first_half or second_half');
            $table->timestamp('locked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
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
