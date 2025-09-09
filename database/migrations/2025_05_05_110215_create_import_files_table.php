<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('import_files', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_id')->nullable();
            $table->foreign('deduction_id')->references('id')->on('deductions');
            $table->unsignedBigInteger('receivable_id')->nullable();
            $table->foreign('receivable_id')->references('id')->on('receivables');
            $table->string('file_name');
            $table->string('path');
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
        Schema::dropIfExists('import_files');
    }
}
