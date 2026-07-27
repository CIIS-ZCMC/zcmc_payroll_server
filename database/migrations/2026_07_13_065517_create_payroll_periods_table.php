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
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->integer('month');
            $table->integer('year');
            $table->enum('employment_type', ['regular', 'job-order']);
            $table->enum('payroll_type', ['monthly', 'weekly']);
            $table->enum('period_type', ['first_half', 'second_half']);
            $table->integer('period_start');
            $table->integer('period_end');
            $table->enum('status', ['draft', 'posted', 'locked', 'released']);
            $table->boolean('is_active')->default(false);
            $table->dateTime('posted_at')->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['month', 'year', 'employment_type', 'payroll_type'], 'payroll_periods_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
