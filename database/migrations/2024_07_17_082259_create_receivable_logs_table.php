<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivableLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivable_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receivable_id');
            $table->foreign('receivable_id')->references('id')->on('receivables');
            $table->unsignedBigInteger('action_by');
            $table->string('action');
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
        Schema::dropIfExists('receivable_logs');
    }
}
