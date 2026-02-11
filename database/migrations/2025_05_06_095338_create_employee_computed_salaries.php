<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeComputedSalaries extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_computed_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->unsignedBigInteger('payroll_period_id');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            $table->unsignedBigInteger('employee_time_record_id');
            $table->foreign('employee_time_record_id')->references('id')->on('employee_time_records');

            $table->decimal('basic_pay')->comment("Without night differential computation and deductions");
            $table->decimal('minutes_rate');
            $table->decimal('daily_rate');
            $table->decimal('hourly_rate');
            $table->decimal('absent_rate');
            $table->decimal('undertime_rate');

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id', 'employee_time_record_id'], 'employee_period_time_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_computed_salaries');
    }
}
