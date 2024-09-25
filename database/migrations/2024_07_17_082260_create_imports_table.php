<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_id')->nullable(); // Make deduction_id nullable
            $table->unsignedBigInteger('receivables_id')->nullable(); // Make receivables_id nullable
            $table->string('file_name');
            $table->string('employment_type');
            $table->date("payroll_date"); // day 15 for 1-15 || day 16 for 16-31
            $table->timestamps();
            $table->foreign('deduction_id')->references('id')->on('deductions')->onDelete('set null');
            $table->foreign('receivables_id')->references('id')->on('receivables')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imports', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['deduction_id']);

            // Drop the column
            $table->dropColumn('deduction_id');
        });
    }
}
