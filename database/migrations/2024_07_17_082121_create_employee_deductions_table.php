<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');
            $table->unsignedBigInteger('deduction_id');
            $table->foreign('deduction_id')->references('id')->on('deductions');
            $table->double('amount')->nullable();
            $table->double('percentage')->nullable();
            $table->string('frequency');
            $table->string('status')->nullable();
            $table->string('date_from')->nullable();
            $table->string('date_to')->nullable();
            $table->string('stopped_at')->nullable();
            $table->string('completed_at')->nullable();
            $table->string('reason')->nullable();
            $table->boolean('with_terms');
            $table->integer('total_term')->nullable();
            $table->integer('total_paid')->nullable();
            $table->boolean('is_default');
            $table->longText('isDifferential')->nullable();
            $table->date('willDeduct')->nullable();
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
        Schema::dropIfExists('employee_deductions');
    }
}
