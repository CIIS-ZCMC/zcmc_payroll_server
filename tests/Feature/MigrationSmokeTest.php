<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_payroll_schema_migrates()
    {
        foreach ([
            'payroll_periods',
            'employees',
            'employee_salaries',
            'employee_time_records',
            'employee_computed_salaries',
            'employee_deductions',
            'employee_receivables',
            'employee_payrolls',
            'excluded_employees',
            'deductions',
            'deduction_groups',
            'receivables',
        ] as $table) {
            $this->assertTrue(Schema::hasTable($table), "Missing table: {$table}");
        }
    }
}
