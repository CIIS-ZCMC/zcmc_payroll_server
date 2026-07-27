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
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained();

            $table->unsignedBigInteger('generated_by_id')->nullable();
            $table->string('generated_by_name')->nullable();
            
            $table->integer('version');

            $table->foreignId('is_reversed_from')->nullable()->constrained('payroll_runs')->nullOnDelete();
            $table->string('reversal_reason')->nullable();
            
            $table->enum('status', ['processing', 'completed', 'locked', 'reversed']);
           
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['payroll_period_id', 'version'], 'payroll_runs_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_runs');
    }
};