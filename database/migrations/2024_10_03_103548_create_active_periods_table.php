<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivePeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('active_periods', function (Blueprint $table) {
            $table->id();
            $table->string("month");
            $table->string("year");
            $table->string("fromPeriod");
            $table->string("toPeriod");
            $table->string("employmentType");
            $table->integer("is_active");
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
        Schema::dropIfExists('active_periods');
    }
}
