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
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');
            $table->unsignedBigInteger('payroll_headers_id')->nullable();
            $table->foreign('payroll_headers_id')->references('id')->on('payroll_headers');
            $table->string('reason');
            $table->string("year");
            $table->string("month");
            $table->boolean("is_removed")->comment("true if it is removed from list. for the genpayrol month and year")->default(false);
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
