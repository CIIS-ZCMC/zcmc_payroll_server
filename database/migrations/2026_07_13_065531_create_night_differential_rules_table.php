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
        Schema::create('night_differential_rules', function (Blueprint $table) {
            $table->id();
            $table->enum('employment_type', ['permanent', 'contractual', 'temporary', 'job-order']);
            
            $table->time('start_time');
            $table->time('end_time');
            
            $table->enum('rate_type', ['percentage', 'fixed_per_minute', 'fixed_per_hour']);
            $table->decimal('rate', 10, 2);
            
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            
            $table->boolean('is_active')->default(true);
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['employment_type', 'rate_type', 'effective_date', 'end_date'], 'ndr_emp_rate_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('night_differential_rules');
    }
};
