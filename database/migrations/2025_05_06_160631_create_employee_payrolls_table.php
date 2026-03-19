<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_payrolls', function (Blueprint $table) {
            $table->id();

            // $table->unsignedBigInteger('employee_id');
            // $table->foreign('employee_id')->references('id')->on('employees');
            // $table->unsignedBigInteger('employee_time_record_id');
            // $table->foreign('employee_time_record_id')->references('id')->on('employee_time_records');
            // $table->unsignedBigInteger('payroll_period_id')->nullable();
            // $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->foreignId('employee_time_record_id')
                ->constrained('employee_time_records')
                ->cascadeOnDelete();

            $table->foreignId('payroll_period_id')
                ->nullable()
                ->constrained('payroll_periods')
                ->nullOnDelete();

            $table->unsignedTinyInteger('month');
            $table->unsignedSmallInteger('year');

            $table->decimal('basic_pay', 11, 2);
            $table->decimal("total_receivables", 11, 2);
            $table->decimal('gross_pay', 11, 2);
            $table->decimal("total_deductions", 11, 2);
            $table->decimal("net_pay", 11, 2);
            // $table->decimal('night_differential', 11, 2);

            $table->unique(
                ['employee_id', 'employee_time_record_id', 'payroll_period_id'],
                'emp_payroll_unique'
            );

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
        Schema::dropIfExists('employee_payrolls');
    }
}
