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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->integer('employee_profile_id');
            $table->string('employee_number');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->string('extension_name')->nullable();
            $table->string('designation');
            $table->json('assigned_area');
            $table->date('hire_date');
            $table->boolean('is_newly_hired')->default(false);
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['employee_profile_id', 'employee_number'], 'employees_employee_profile_id_employee_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
