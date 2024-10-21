<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNightDifferentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('night_differentials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_list_id');
            $table->foreign('employee_list_id')->references('id')->on('employee_lists');
            $table->string("month");
            $table->string("year");
            $table->double("accumulated_hours");
            $table->double("computed_pay");
            $table->string('fromPeriod')->nullable()->comment('period from , ex.1-15');
            $table->string('toPeriod')->nullable()->comment('period to , ex.16-31');
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
        Schema::dropIfExists('night_differentials');
    }
}
