<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_id');
            $table->foreign('deduction_id')->references('id')->on('deductions');
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
        Schema::dropIfExists('deduction_rules');
    }
}
