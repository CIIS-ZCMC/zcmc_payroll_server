<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->uuid('deduction_uuid')->unique();
            $table->unsignedBigInteger('deduction_group_id');
            $table->foreign('deduction_group_id')->references('id')->on('deduction_groups');
            $table->string('name');
            $table->string('code');
            $table->string('type')->default('fixed')->comment('fixed / percentage / conditional');
            $table->boolean('hasDate')->default(false);
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('condition_operator')->nullable();
            $table->double('condition_value')->nullable();
            $table->double('percent_value')->nullable();
            $table->double('fixed_amount')->nullable();
            $table->string('billing_cycle')->default('monthly');
            $table->string('status')->default("active");
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
        Schema::dropIfExists('deductions');
    }
}
