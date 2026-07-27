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
        Schema::create('logs_and_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('action_by_id')->nullable();
            $table->string('action_by_name')->nullable();
            $table->string('module');
            $table->string('action_type');
            $table->string('reference_table');
            $table->integer('reference_id');
            $table->json('changes')->nullable();
            $table->string('description')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('status')->default('success');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_and_trails');
    }
};