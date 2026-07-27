<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employee_deduction_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_deduction_id')->constrained();
            $table->foreignId('payroll_run_id')->constrained();
            
            $table->decimal('amount', 10, 2);
            $table->integer('term_no');

            $table->dateTime('deducted_at');
           
            $table->softDeletes();
            $table->timestamps();
           
            $table->unique(['employee_deduction_id', 'payroll_run_id'], 'deduction_payment_id_payroll_run_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_deduction_payments');
    }
};
