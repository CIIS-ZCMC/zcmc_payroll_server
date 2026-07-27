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
        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_payroll_id')->constrained();
            $table->foreignId('payroll_run_id')->constrained();
            
            $table->enum('adjustment_type', ['bonus', 'penalty', 'correction', 'reversal', 'late_pay']);
            
            $table->decimal('amount', 15, 2);
            $table->text('reason');
            
            $table->unsignedBigInteger('created_by_id')->nullable();
            $table->string('created_by_name')->nullable();
            
            $table->boolean('is_approved')->default(false);
            $table->unsignedBigInteger('approved_by_id')->nullable();
            $table->string('approved_by_name')->nullable();
            $table->dateTime('approved_at')->nullable();
            
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_payroll_id', 'payroll_run_id'], 'payroll_adjustment_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_adjustments');
    }
};