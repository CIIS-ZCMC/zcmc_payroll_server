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
        Schema::create('payroll_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained()->unique();

            $table->integer('current_step');
            $table->string('status');

            $table->unsignedBigInteger('started_by_id');
            $table->string('started_by_name');
            $table->dateTime('started_at');

            $table->unsignedBigInteger('locked_by_id')->nullable();
            $table->dateTime('locked_at')->nullable();
            $table->dateTime('lock_expires_at')->nullable();
            
            $table->dateTime('completed_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_processes');
    }
};