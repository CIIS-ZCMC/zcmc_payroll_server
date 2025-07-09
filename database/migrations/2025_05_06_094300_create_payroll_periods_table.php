<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string("month");
            $table->string("year");
            $table->string("employment_type");
            $table->string("period_type")->comment("first_half, second_half, full_month");
            $table->string('period_start');
            $table->string('period_end');
            $table->integer("days_of_duty");
            $table->boolean("is_special")->default(false);
            $table->dateTime("posted_at")->nullable();
            $table->dateTime("last_generated_at")->nullable();
            $table->dateTime("locked_at")->nullable();
            $table->boolean("is_active")->default(false);
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
        Schema::dropIfExists('payroll_periods');
    }
}
