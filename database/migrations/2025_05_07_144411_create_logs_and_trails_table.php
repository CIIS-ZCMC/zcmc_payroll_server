<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsAndTrailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('logs_and_trails', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('action_by')->comment('ID of the user who performed the action');
            $table->string('module')->comment('Module where the action was performed (e.g., payroll, attendance)');
            $table->string('action_type')->comment('Type of action performed (e.g., create, update, delete)');
            $table->string('reference_table')->comment('Name of the reference table related to the action');
            $table->unsignedBigInteger('reference_id')->comment('ID of the record in the reference table');
            $table->json('changes')->comment('Details of the changes made during the action');
            $table->string('description')->comment('Optional text for more context');
            $table->string('ip_address')->comment('IP address of the user who performed the action');
            $table->string('status')->comment('Status of the action (e.g., success, failed)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('logs_and_trails');
    }
}
