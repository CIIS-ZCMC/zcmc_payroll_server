<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeReceivableLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_receivable_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_receivable_id');
            $table->foreign('employee_receivable_id')->references('id')->on('employee_receivables');
            $table->unsignedBigInteger('action_by');
            $table->string('action');
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('employee_receivable_logs');
    }
}
