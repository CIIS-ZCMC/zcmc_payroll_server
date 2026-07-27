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
        Schema::create('employee_deduction_terms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_deduction_id')->constrained()->unique();

            $table->integer('total_terms');
            $table->integer('paid_terms')->default(0);

            $table->decimal('term_amount', 10, 2);
            $table->decimal('total_amount', 10, 2);

            $table->decimal('remaining_balance', 10, 2);

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
        Schema::dropIfExists('employee_deduction_terms');
    }
};