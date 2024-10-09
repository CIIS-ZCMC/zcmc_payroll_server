<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirstPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('first_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('general_payrolls_id');
            $table->foreign('general_payrolls_id')->references('id')->on('general_payrolls');
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');          
            $table->text("net_total_salary");
            $table->dateTime("locked_at")->nullable();
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
        Schema::dropIfExists('first_payrolls');
    }
}
