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
        Schema::create('night_differential_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained();

            $table->unsignedBigInteger('computed_by_id')->nullable();
            $table->string('computed_by_name')->nullable();
            $table->dateTime('computed_at')->nullable();
            
            $table->integer('total_employees')->default(0);
            $table->decimal('total_hours', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);

            $table->integer('version');
            $table->enum('status', ['draft', 'computed', 'posted', 'locked']);
            
            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['payroll_period_id', 'version'], 'ndr_payroll_version_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('night_differential_runs');
    }
};