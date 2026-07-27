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
        Schema::create('late_deduction_matrix', function (Blueprint $table) {
            $table->id();
            $table->enum('employment_type', ['permanent', 'contractual', 'temporary']);
            $table->integer('from_minutes');
            $table->integer('to_minutes');
            $table->decimal('amount', 10, 2);
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['employment_type', 'from_minutes', 'to_minutes'], 'ldm_emp_minutes_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('late_deduction_matrix');
    }
};
