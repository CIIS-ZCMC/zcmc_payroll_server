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
            $table->unsignedBigInteger('deduction_group_id');
            $table->foreign('deduction_group_id')->references('id')->on('deduction_groups');
            $table->string('name');
            $table->string('code');
            $table->string('employment_type');
            $table->string('designation');
            $table->string('assigned_area');
            $table->string('charge_basis');
            $table->double('charge_value')->nullable();
            $table->string('billing_cycle');
            $table->integer('terms_to_pay')->nullable();
            $table->boolean('is_applied_to_all');
            $table->string('apply_salarygrade_from')->nullable();
            $table->string('apply_salarygrade_to')->nullable();
            $table->boolean('is_mandatory');
            $table->boolean('is_active');
            $table->string('reason');
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
