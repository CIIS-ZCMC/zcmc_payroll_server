<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeReceivableAdjustmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_receivable_adjustments', function (Blueprint $table) {
            $table->id();
            $table->text("action_by")->comment("Employee Details is from UMIS");
            $table->unsignedBigInteger('employee_receivable_id');
            $table->foreign('employee_receivable_id')->references('id')->on('employee_receivables');
            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->unsignedBigInteger('receivable_id');
            $table->foreign('receivable_id')->references('id')->on('receivables');
            $table->string('month');
            $table->string('year');
            $table->string('amount')->comment("Amount of the employee received");
            $table->string('amount_to_received')->comment("Expected Amount to receive, data is from Employee Receivables [Amount]");
            $table->string('amount_balance')->nullable();
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
        Schema::dropIfExists('employee_receivable_adjustments');
    }
}
