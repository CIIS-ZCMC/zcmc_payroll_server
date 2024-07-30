<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('time_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');
            $table->double('total_working_hours');
            $table->double('total_working_minutes');
            $table->double('total_leave_with_pay');
            $table->double('total_leave_without_pay');
            $table->double('total_without_pay_days');
            $table->double('total_present_days');
            $table->double('total_night_duty_days');
            $table->double('total_night_duty_hours');
            $table->double('total_day_shift_hours');
            $table->double('total_day_shift_days');
            $table->double('total_absences');
            $table->double('undertime_minutes');
            $table->double('absent_rate');
            $table->double('undertime_rate');
            $table->string('month');
            $table->string('year');
            $table->double('minutes');
            $table->double('daily');
            $table->double('hourly');
            $table->integer('is_active')->default(0);
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
        Schema::dropIfExists('time_records');
    }
}
