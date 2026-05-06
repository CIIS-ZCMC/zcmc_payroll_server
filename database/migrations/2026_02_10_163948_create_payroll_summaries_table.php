<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollSummariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_summaries', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            $table->integer('generated_by_id');
            $table->string('generated_by_name');

            $table->integer('total_employees');

            $table->decimal('total_deductions', 15, 2);
            $table->decimal('total_receivables', 15, 2);
            $table->decimal('total_gross', 15, 2);
            $table->decimal('total_net', 15, 2);
            $table->decimal('total_night_differential', 15, 2);

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
        Schema::dropIfExists('payroll_summaries');
    }
}
