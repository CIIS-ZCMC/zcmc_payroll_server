<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGeneralPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('general_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payroll_headers_id');
            $table->foreign('payroll_headers_id')->references('id')->on('payroll_headers');
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');
            $table->text('time_records');
            $table->text("employee_receivables");
            $table->text("employee_deductions");
            $table->text("base_salary");
            $table->text("net_pay");
            $table->text("gross_pay");
            $table->text("net_salary_first_half");
            $table->text("net_salary_second_half");
            $table->text("net_total_salary");
            $table->string('month');
            $table->string('year');
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
        Schema::dropIfExists('general_payrolls');
    }
}
