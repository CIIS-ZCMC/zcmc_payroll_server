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
        Schema::create('employee_night_duties', function (Blueprint $table) {
            $table->id();            
            $table->foreignId('employee_id')->constrained();
            $table->foreignId('payroll_period_id')->constrained();

            $table->date('duty_date');
            $table->dateTime('time_in');
            $table->dateTime('time_out');
            $table->integer('total_minutes');
            $table->decimal('total_hours', 10, 2);

            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_id', 'duty_date'], 'employee_night_duty_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_night_duties');
    }
};