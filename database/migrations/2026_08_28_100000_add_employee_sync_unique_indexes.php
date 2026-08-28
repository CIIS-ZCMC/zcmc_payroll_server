<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bulk upserts only behave as upserts when the conflict target is backed by a
 * unique index. EmployeeSyncService keys `employees` by employee_profile_id and
 * `excluded_employees` by (employee_id, payroll_period_id); neither had one.
 */
class AddEmployeeSyncUniqueIndexes extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->unique('employee_profile_id', 'employees_employee_profile_id_unique');
        });

        Schema::table('excluded_employees', function (Blueprint $table) {
            $table->unique(['employee_id', 'payroll_period_id'], 'excluded_employee_period_unique');
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropUnique('employees_employee_profile_id_unique');
        });

        Schema::table('excluded_employees', function (Blueprint $table) {
            $table->dropUnique('excluded_employee_period_unique');
        });
    }
}
