<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollProcessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_processes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');
            $table->string('payroll_type');
            $table->integer('current_step');
            $table->string('status');
            $table->string('started_by');
            $table->dateTime('started_at');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'payroll_type'], 'payroll_process_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payroll_processes');
    }
}
