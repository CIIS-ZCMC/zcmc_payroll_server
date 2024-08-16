<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code');
            $table->string('emmployment_type');
            $table->string('charge_basis')->nullable();
            $table->double('charge')->nullable();
            $table->string('billing_cycle')->nullable();
            $table->integer('terms_to_pay')->nullable();
            $table->boolean('is_mandatory');
            $table->boolean('is_active');
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
        Schema::dropIfExists('receivables');
    }
}
