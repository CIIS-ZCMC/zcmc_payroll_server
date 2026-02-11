<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeNightDiffComputationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_night_diff_computations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->unsignedBigInteger('payroll_period_id');
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            $table->decimal('total_night_hours', 10, 2);
            $table->decimal('total_night_amount', 15, 2);

            $table->decimal('hourly_rate');
            $table->decimal('rate_percent');

            $table->boolean('is_finalized');
            $table->dateTime('computed_at');

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
        Schema::dropIfExists('employee_night_diff_computations');
    }
}
