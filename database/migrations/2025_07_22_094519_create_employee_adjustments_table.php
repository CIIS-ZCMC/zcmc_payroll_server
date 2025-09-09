<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_adjustments', function (Blueprint $table) {
            $table->id();
            $table->text("action_by")->comment("Employee Details is from UMIS");

            $table->unsignedBigInteger('payroll_period_id');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            $table->unsignedBigInteger('employee_deduction_id')->nullable();
            $table->foreign('employee_deduction_id')->references('id')->on('employee_deductions');

            $table->unsignedBigInteger('employee_receivable_id')->nullable();
            $table->foreign('employee_receivable_id')->references('id')->on('employee_receivables');

            $table->string('amount')->comment("Amount of the employee paid");
            $table->string('amount_to_pay')->comment("Expected Amount to pay, data is from Employee Deductions [Amount]");
            $table->string('amount_balance');
            $table->string('reason');
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
        Schema::dropIfExists('employee_adjustments');
    }
}
