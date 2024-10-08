<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_receivables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');
            $table->unsignedBigInteger('receivable_id');
            $table->foreign('receivable_id')->references('id')->on('receivables');
            $table->double('amount')->nullable();
            $table->double('percentage')->nullable();
            $table->string('status')->nullable();
            $table->string('date_from')->nullable();
            $table->string('date_to')->nullable();
            $table->string('stopped_at')->nullable();
            $table->string('completed_at')->nullable();
            $table->integer('total_paid')->nullable();
            $table->string('reason')->nullable();
            $table->string('frequency');
            $table->boolean('is_default');
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
        Schema::dropIfExists('employee_receivables');
    }
}
