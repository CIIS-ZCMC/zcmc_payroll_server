<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExcludedEmployeesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('excluded_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');
            $table->string("month");
            $table->string("year");
            $table->string('period_start');
            $table->string('period_end');
            $table->string('reason');
            $table->boolean("is_removed")->comment("true if it is removed from list. for the genpayrol month and year")->default(false);
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
        Schema::dropIfExists('excluded_employees');
    }
}