<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueConstraintToEmployeeDeductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->unique(['payroll_period_id', 'employee_id', 'deduction_id'], 'employee_deduction_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->dropUnique('employee_deduction_unique');
        });
    }
}
