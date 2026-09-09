<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * A payroll run has to know whether the figures on screen still reflect the
 * data behind them.
 *
 * Steps 1 to 5 all change the inputs to the computation — an imported
 * deduction, a stopped receivable, an adjustment, a change to who is selected.
 * Without a flag, a run edited after step 6 shows the previous recompute's
 * numbers in the preview and posts them, and nothing anywhere says so.
 */
class AddStepTrackingToPayrollProcessesTable extends Migration
{
    public function up()
    {
        Schema::table('payroll_processes', function (Blueprint $table) {
            // A run that has never been recomputed is dirty by definition.
            $table->boolean('is_dirty')->default(true)->after('status');
            $table->dateTime('recomputed_at')->nullable()->after('is_dirty');
        });
    }

    public function down()
    {
        Schema::table('payroll_processes', function (Blueprint $table) {
            $table->dropColumn(['is_dirty', 'recomputed_at']);
        });
    }
}
