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
            $table->string('employment_type')->default('Regular Employee');
            $table->string('designation')->default('All Designation');
            $table->string('assigned_area')->default('All Area');
            $table->text('condition')->nullable();
            $table->double('amount')->nullable();
            $table->double('percentage')->nullable();
            $table->string('billing_cycle')->default('Monthly');
            $table->integer('terms_to_pay')->nullable();
            $table->boolean('is_applied_to_all')->default(true);
            $table->string('apply_salarygrade_from')->nullable();
            $table->string('apply_salarygrade_to')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->string('status')->default("Active");
            $table->string('reason')->nullable();
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
        Schema::dropIfExists('deductions');
    }
}
