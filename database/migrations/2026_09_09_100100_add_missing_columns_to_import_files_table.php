<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The ImportFiles model and its table did not describe the same thing.
 *
 * The model declared file_size, file_type and imported_at as fillable; the
 * table has never had any of them, so any write through the model either threw
 * or silently dropped them. It also declared `receivables_id` and `file_path`
 * where the table has `receivable_id` and `path`.
 *
 * The two name mismatches are fixed on the model side (the table's names are
 * the ones the foreign key and existing rows use, and renaming a column needs
 * doctrine/dbal, which this project does not have). The three genuinely
 * missing columns are added here.
 */
class AddMissingColumnsToImportFilesTable extends Migration
{
    public function up()
    {
        Schema::table('import_files', function (Blueprint $table) {
            $table->unsignedBigInteger('file_size')->nullable()->after('path');
            $table->string('file_type')->nullable()->after('file_size');
            $table->dateTime('imported_at')->nullable()->after('file_type');
        });
    }

    public function down()
    {
        Schema::table('import_files', function (Blueprint $table) {
            $table->dropColumn(['file_size', 'file_type', 'imported_at']);
        });
    }
}
