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
            $table->text("action_by")->comment("Employee Details is from UMIS");
            $table->unsignedBigInteger('employee_deduction_id');
            $table->foreign('employee_deduction_id')->references('id')->on('employee_deductions');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('deduction_id');
            $table->foreign('deduction_id')->references('id')->on('deductions');
            $table->string('month');
            $table->string('year');
            $table->string('amount')->comment("Amount of the employee paid");
            $table->string('amount_to_pay')->comment("Expected Amount to pay, data is from Employee Deductions [Amount]");
            $table->string('amount_balance');
            $table->string('reason');
            $table->boolean('will_deduct')->default(false);
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
        Schema::dropIfExists('employee_deduction_adjustments');
    }
}
