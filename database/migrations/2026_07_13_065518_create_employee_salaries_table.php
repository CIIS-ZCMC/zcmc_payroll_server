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
        Schema::create('employee_salaries', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();
           
            $table->enum('employment_type', ['permanent', 'contractual', 'temporary']);
           
            $table->decimal('base_salary', 15, 2);
            $table->integer('salary_grade');
            $table->integer('salary_step');

            $table->boolean('is_active')->default(true);
            
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'payroll_period_id'], 'employee_salaries_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_salaries');
    }
};