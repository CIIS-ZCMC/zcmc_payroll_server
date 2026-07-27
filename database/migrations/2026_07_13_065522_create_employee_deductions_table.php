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
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('deduction_id')->constrained();
            $table->foreignId('payroll_period_id')->nullable()->constrained()->nullOnDelete();

            $table->string('billing_cycle');
            $table->decimal('amount', 10, 2);
            $table->integer('percentage')->nullable();

            $table->date('effective_date');
            $table->date('end_date')->nullable();

            $table->enum('status', ['active', 'inactive', 'completed', 'suspended'])->default('active');

            $table->boolean('is_active')->default(true);
            $table->boolean('is_default')->default(false);

            $table->string('remarks')->nullable();

            $table->dateTime('stopped_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'deduction_id', 'payroll_period_id'], 'employee_deduction_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_deductions');
    }
};
