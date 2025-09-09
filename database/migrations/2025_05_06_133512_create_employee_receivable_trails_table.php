<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeReceivableTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_receivable_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_receivable_id');
            $table->foreign('employee_receivable_id')->references('id')->on('employee_receivables');
            $table->integer('total_term');
            $table->integer('total_term_received');
            $table->double('amount_received');
            $table->date('date_reeceived');
            $table->double('balance')->default(0);
            $table->string('status');
            $table->string('remarks')->nullable();
            $table->boolean('is_last_received')->default(false);
            $table->boolean('is_adjustment');
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
        Schema::dropIfExists('employee_receivable_trails');
    }
}
