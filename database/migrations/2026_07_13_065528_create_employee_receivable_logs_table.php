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
        Schema::create('employee_receivable_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_receivable_id')->constrained();
            $table->unsignedBigInteger('action_by_id')->nullable();
            $table->string('action');
            $table->string('remarks')->nullable();
            $table->text('details')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_receivable_logs');
    }
};