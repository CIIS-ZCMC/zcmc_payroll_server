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
            $table->unsignedBigInteger('generated_by');
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');
            $table->text('total_employees');
            $table->text('total_deductions');
            $table->text('total_receivables');
            $table->text('total_gross');
            $table->text('total_net');
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
