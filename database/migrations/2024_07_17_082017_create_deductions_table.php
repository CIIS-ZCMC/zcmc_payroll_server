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
            $table->double('amount');
            $table->double('percentage')->nullable();
            $table->date('date_from');
            $table->date('date_to');
            $table->string('emmployment_type');
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
        Schema::dropIfExists('deductions');
    }
}
