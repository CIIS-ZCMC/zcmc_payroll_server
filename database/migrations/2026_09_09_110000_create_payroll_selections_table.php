<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Step 5: who is actually in this payroll run.
 *
 * The choice was not persisted anywhere. EmployeePreviewController took a
 * `selected_employees[]` array on the request and passed it straight through,
 * so the officer's decision lived in the client and was lost on refresh — and
 * step 6 had no input set to generate from.
 *
 * Deliberately not reusing excluded_employees. That table records a system or
 * HR exclusion (resigned, a data issue) and is an *input* to the projection;
 * this records the human's final include/exclude call for one run. Conflating
 * them makes "excluded because resigned" indistinguishable from "excluded by
 * the payroll officer this cycle".
 */
class CreatePayrollSelectionsTable extends Migration
{
    public function up()
    {
        Schema::create('payroll_selections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_period_id')
                ->constrained('payroll_periods')
                ->cascadeOnDelete();

            // Regular and Job Order are separate runs over the same period.
            $table->unsignedTinyInteger('payroll_type');

            $table->foreignId('employee_id')
                ->constrained('employees')
                ->cascadeOnDelete();

            $table->boolean('is_selected')->default(true);

            // Why this employee was forced in, or left out.
            $table->string('reason')->nullable();
            $table->string('selected_by')->nullable();

            $table->timestamps();

            $table->unique(
                ['payroll_period_id', 'payroll_type', 'employee_id'],
                'payroll_selection_unique'
            );

            $table->index(['payroll_period_id', 'payroll_type', 'is_selected'], 'payroll_selection_roster');
        });
    }

    public function down()
    {
        Schema::dropIfExists('payroll_selections');
    }
}
