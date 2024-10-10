<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollHeadersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_headers', function (Blueprint $table) {
            $table->id();
            $table->string("month");
            $table->string("year");
            $table->string("employment_type");
            $table->string('fromPeriod')->nullable();
            $table->string('toPeriod')->nullable();
            $table->string("days_of_duty")->nullable();
            $table->text("created_by")->comment("Saved Logged Employee data - Json Format");
            $table->dateTime("posted_at")->nullable();
            $table->dateTime("last_generated_at")->nullable();
            $table->boolean("is_special")->default(false);
            $table->dateTime("locked_at")->nullable();
            $table->dateTime("first_payroll_locked_at")->nullable();
            $table->dateTime("second_payroll_locked_at")->nullable();
            $table->dateTime("deleted_at")->nullable();
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
        Schema::dropIfExists('payroll_headers');
    }
}
