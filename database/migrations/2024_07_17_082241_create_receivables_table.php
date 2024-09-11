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
            $table->string('employment_type');
            $table->string('charge_basis');
            $table->double('charge_value')->nullable();
            $table->string('billing_cycle');
            $table->integer('terms_to_pay')->nullable();
            $table->boolean('is_applied_to_all');
            $table->string('apply_salarygrade_from')->nullable();
            $table->string('apply_salarygrade_to')->nullable();
            $table->boolean('is_mandatory');
            $table->string('status')->default("Active");
            $table->string('reason');
            $table->timestamp('stopped_at')->nullable();
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
