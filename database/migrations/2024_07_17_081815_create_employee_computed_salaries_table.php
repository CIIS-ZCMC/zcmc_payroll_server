<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeComputedSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_computed_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('time_record_id');
            $table->foreign('time_record_id')->references('id')->on('time_records');
            $table->text('computed_salary')->comment("Without night differential computation and deductions");
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
        Schema::dropIfExists('employee_computed_salaries');
    }
}
