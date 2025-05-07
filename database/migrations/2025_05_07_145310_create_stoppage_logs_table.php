<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoppageLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stoppage_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('action_by')->comment('ID of the user who performed the action');
            $table->unsignedBigInteger('employee_deduction_id')->nullable();
            $table->foreign('employee_deduction_id')->references('id')->on('employee_deductions')->nullable();
            $table->unsignedBigInteger('employee_receivable_id')->nullable();
            $table->foreign('employee_receivable_id')->references('id')->on('employee_receivables')->nullable();
            $table->string('date_from')->nullable();
            $table->string('date_to')->nullable();
            $table->string('status');
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('stoppage_logs');
    }
}
