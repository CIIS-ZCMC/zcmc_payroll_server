<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDeductionTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_deduction_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_deduction_id');
            $table->foreign('employee_deduction_id')->references('id')->on('employee_deductions');
            $table->integer('total_term');
            $table->integer('total_term_paid');
            $table->double('amount_paid');
            $table->date('date_paid');
            $table->double('balance')->default(0);
            $table->string('status');
            $table->string('remarks')->nullable();
            $table->boolean('is_last_payment');
            $table->boolean('is_adjustment');
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
        Schema::dropIfExists('employee_deduction_trails');
    }
}
