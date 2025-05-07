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
            $table->double('total_working_hours');
            $table->double('total_working_minutes');
            $table->double('total_leave_with_pay');
            $table->double('total_leave_without_pay');
            $table->double('total_days_without_pay');
            $table->double('total_present_days');
            $table->double('total_night_duty_hours');
            $table->double('total_absences');
            $table->double('total_undertime_minutes');
            $table->double('undertime_rate');
            $table->double('absent_rate');
            $table->string('month');
            $table->string('year');
            $table->string('from')->nullable()->comment('period from , ex.1-15');
            $table->string('to')->nullable()->comment('period to , ex.16-31');
            $table->double('minutes');
            $table->double('daily');
            $table->double('hourly');
            $table->boolean('is_night_shift');
            $table->boolean('is_active')->default(0);
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
