<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_payrolls', function (Blueprint $table) {
            $table->id();
            $table->string('generated_by_id');
            $table->string('generated_by_name');
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');
            $table->double('total_employees');
            $table->double('total_deductions');
            $table->double('total_receivables');
            $table->double('total_gross');
            $table->double('total_net');
            $table->string('month');
            $table->string('year');
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
        Schema::dropIfExists('general_payrolls');
    }
}
