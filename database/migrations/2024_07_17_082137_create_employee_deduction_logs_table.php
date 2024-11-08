<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDeductionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_deduction_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_deduction_id');
            $table->foreign('employee_deduction_id')->references('id')->on('employee_deductions');
            $table->unsignedBigInteger('action_by');
            $table->string('action');
            $table->string('remarks')->nullable();
            $table->text('details')->nullable();
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
        Schema::dropIfExists('employee_deduction_logs');
    }
}
