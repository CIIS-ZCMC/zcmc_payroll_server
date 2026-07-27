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
        Schema::create('employee_receivable_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_receivable_id')->constrained();
            $table->foreignId('payroll_run_id')->constrained();
            
            $table->decimal('amount', 10, 2);
            $table->integer('term_no');

            $table->dateTime('received_at');
            
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_receivable_id', 'payroll_run_id'], 'receivable_payment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_receivable_payments');
    }
};
