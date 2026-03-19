<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNightDifferentialRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('night_differential_rules', function (Blueprint $table) {
            $table->id();
            $table->string('employment_type');

            $table->time('start_time');
            $table->time('end_time');

            $table->decimal('rate_percent', 11, 2);

            $table->date('effective_date');

            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('night_differential_rules');
    }
}
