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
            $table->uuid('receivable_uuid')->unique();
            $table->string('name');
            $table->string('code');
            $table->string('type');
            $table->boolean('hasDate')->default(false);
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('condition_operator')->nullable();
            $table->double('condition_value')->nullable();
            $table->double('percent_value')->nullable();
            $table->double('fixed_amount')->nullable();
            $table->string('billing_cycle')->default('Monthly');
            $table->string('status')->default("Active");
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
        Schema::dropIfExists('receivables');
    }
}
