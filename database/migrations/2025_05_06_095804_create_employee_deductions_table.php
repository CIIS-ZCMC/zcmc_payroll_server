<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('payroll_period_id')->nullable();
            $table->foreign('payroll_period_id')->references('id')->on('payroll_periods');

            $table->unsignedBigInteger('employee_id');
            $table->foreign('employee_id')->references('id')->on('employees');

            $table->unsignedBigInteger('deduction_id');
            $table->foreign('deduction_id')->references('id')->on('deductions');

            $table->string('billing_cycle');
            $table->decimal('amount')->nullable();
            $table->decimal('percentage')->nullable();

            $table->string('date_from')->nullable();
            $table->string('date_to')->nullable();

            $table->boolean('with_terms');
            $table->integer('total_term')->nullable();
            $table->integer('total_paid')->nullable();

            $table->string('reason')->nullable();
            $table->string('status')->nullable();

            $table->longText('isDifferential')->nullable();
            $table->boolean('is_default');

            $table->date('effective_date')->nullable();
            $table->date('deduct_at')->nullable();

            $table->dateTime('stopped_at')->nullable();
            $table->dateTime('completed_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->unique(
                ['employee_id', 'deduction_id', 'payroll_period_id'],
                'employee_deduction_unique'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('employee_deductions');
    }
}
