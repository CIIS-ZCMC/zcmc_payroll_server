<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->text('module')->nullable();
            $table->string('action');
            $table->text('status');
            $table->string('ip_address');
            $table->string('remarks')->nullable();
            $table->text('affected_entity')->nullable()->comment("Ex. modified data IDs,uploaded documents. in JSON FORMAT");
            $table->unsignedBigInteger('employee_profile_id');
            $table->text('employee_number')->nullable();
            $table->string('name');
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
        Schema::dropIfExists('transaction_logs');
    }
}
