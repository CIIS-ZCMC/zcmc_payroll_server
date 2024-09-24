<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDeductionAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_deduction_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_deduction_id');
            $table->foreign('employee_deduction_id')->references('id')->on('employee_deductions');

            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');

            $table->unsignedBigInteger('deduction_id');
            $table->foreign('deduction_id')->references('id')->on('deductions');

            $table->string('month');
            $table->string('year');
            $table->string('amount');
            $table->string('reason');

            $table->text("action_by")->comment("Employee Details is from UMIS");
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
        Schema::dropIfExists('employee_deduction_adjustments');
    }
}
