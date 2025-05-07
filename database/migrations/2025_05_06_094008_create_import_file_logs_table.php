<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportFileLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_file_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_id')->nullable();
            $table->foreign('deduction_id')->references('id')->on('deductions');
            $table->unsignedBigInteger('receivable_id')->nullable();
            $table->foreign('receivable_id')->references('id')->on('receivables');
            $table->string('file_name');
            $table->string('employment_type');
            $table->date("payroll_date")->comment('day 15 for 1-15 || day 16 for 16-31');
            $table->softDeletes();
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
        Schema::dropIfExists('import_file_logs');
    }
}
