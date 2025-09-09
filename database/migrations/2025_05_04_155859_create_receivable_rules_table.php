<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateReceivableRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('receivable_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('receivable_id');
            $table->foreign('receivable_id')->references('id')->on('receivables');
            $table->decimal('min_salary')->nullable();
            $table->decimal('max_salary')->nullable();
            $table->string('apply_type')->comment('Type of deduction: fixed or percentage');
            $table->string('value')->comment('	If fixed, this is the fixed amount; if percentage, its the rate (e.g. 5 for 5%)');
            $table->date('effective_date')->nullable()->comment('Date from which the rule is effective');
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('receivable_rules');
    }
}
