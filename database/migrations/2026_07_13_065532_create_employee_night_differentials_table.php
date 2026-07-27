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
        Schema::create('employee_night_differentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('night_differential_run_id')->constrained();
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();
            $table->foreignId('night_duty_id')->constrained('employee_night_duties');
            $table->foreignId('rule_id')->constrained('night_differential_rules');
           
            $table->integer('total_minutes');
            $table->decimal('total_hours', 10, 2);
            $table->decimal('amount', 10, 2);

            $table->softDeletes();
            $table->timestamps();
            
            $table->unique(['night_duty_id', 'night_differential_run_id'], 'nd_duty_id_nd_run_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_night_differentials');
    }
};
