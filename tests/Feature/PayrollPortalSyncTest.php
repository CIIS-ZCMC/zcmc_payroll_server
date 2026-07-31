<?php

use App\Contract\PortalCacheReaderInterface;
use App\Exceptions\PortalCacheMissing;
use App\Models\Employee;
use App\Models\EmployeeComputedSalary;
use App\Models\EmployeeExclusion;
use App\Models\EmployeeSalary;
use App\Models\EmployeeTimeRecord;
use App\Models\PayrollPeriod;
use App\Models\PayrollRun;
use App\Services\Fetch\PayrollPortalSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * Binds a fake portal reader that returns a canned aggregate payload for the
 * requested period and null for anything else — no live Redis needed.
 *
 * @param  array<string, mixed>|null  $payload
 */
function fakePortalReader(?array $payload): void
{
    $reader = new class($payload) implements PortalCacheReaderInterface
    {
        public function __construct(private ?array $payload) {}

        public function read(string $year, string $month, string $employmentType, string $periodType): ?array
        {
            return $this->payload;
        }
    };

    app()->instance(PortalCacheReaderInterface::class, $reader);
}

/**
 * @return array<string, mixed>
 */
function samplePortalPayload(): array
{
    return [
        'period' => [
            'payroll_type' => 'monthly',
            'period_start' => 1,
            'period_end' => 15,
            'status' => 'draft',
        ],
        'employees' => [
            [
                'employee_profile_id' => 101,
                'employee_number' => '2015081702',
                'first_name' => 'Joshua',
                'last_name' => 'Alvia',
                'designation' => 'Nurse I',
                'assigned_area' => ['ward' => 'ER'],
                'hire_date' => '2020-01-01',
                'salary' => ['employment_type' => 'permanent', 'base_salary' => 22000, 'salary_grade' => 15, 'salary_step' => 1],
                'time_record' => ['no_of_present_days' => 22, 'total_undertime_minutes' => 0, 'night_duties' => ['2026-03-05'], 'absent_dates' => []],
                'computed_salary' => ['basic_pay' => 22000, 'daily_rate' => 1000, 'hourly_rate' => 125],
            ],
            [
                'employee_profile_id' => 102,
                'employee_number' => '2022101725',
                'first_name' => 'Mia',
                'last_name' => 'Alvia',
                'designation' => 'Nurse II',
                'assigned_area' => ['ward' => 'OR'],
                'hire_date' => '2021-06-01',
                'salary' => ['employment_type' => 'contractual', 'base_salary' => 18000, 'salary_grade' => 11, 'salary_step' => 2],
                'time_record' => ['no_of_present_days' => 10, 'no_of_absences' => 12],
                'computed_salary' => ['basic_pay' => 18000],
                'exclusion' => ['reason' => 'On leave without pay'],
            ],
        ],
    ];
}

it('hydrates all six tables plus a payroll run from the portal payload', function () {
    fakePortalReader(samplePortalPayload());

    $counts = app(PayrollPortalSyncService::class)->sync('2026', '3', 'regular', 'first_half');

    // PayrollPeriod (keyed on the requested tuple + payroll_type from the payload)
    $period = PayrollPeriod::first();
    expect($period)->not->toBeNull();
    expect($period->month)->toBe(3);
    expect($period->year)->toBe(2026);
    expect($period->employment_type)->toBe('regular');
    expect($period->payroll_type)->toBe('monthly');
    expect($period->period_type)->toBe('first_half');

    // A payroll run was created for computed salaries to attach to.
    expect(PayrollRun::count())->toBe(1);

    // Employees
    expect(Employee::count())->toBe(2);
    $joshua = Employee::where('employee_number', '2015081702')->first();
    expect($joshua->assigned_area)->toBe(['ward' => 'ER']);

    // Salary
    expect(EmployeeSalary::count())->toBe(2);
    $salary = EmployeeSalary::where('employee_id', $joshua->id)->first();
    expect((float) $salary->base_salary)->toBe(22000.0);
    expect($salary->employment_type)->toBe('permanent');

    // Time record (longText night_duties stored as JSON string)
    expect(EmployeeTimeRecord::count())->toBe(2);
    $timeRecord = EmployeeTimeRecord::where('employee_id', $joshua->id)->first();
    expect((float) $timeRecord->no_of_present_days)->toBe(22.0);
    expect($timeRecord->night_duties)->toBe(json_encode(['2026-03-05']));

    // Computed salary
    expect(EmployeeComputedSalary::count())->toBe(2);
    $computed = EmployeeComputedSalary::where('employee_id', $joshua->id)->first();
    expect((float) $computed->basic_pay)->toBe(22000.0);
    expect($computed->employee_time_record_id)->toBe($timeRecord->id);

    // Exclusion (only the second employee)
    expect(EmployeeExclusion::count())->toBe(1);
    expect(EmployeeExclusion::first()->reason)->toBe('On leave without pay');

    expect($counts)->toMatchArray([
        'employees' => 2,
        'time_records' => 2,
        'salaries' => 2,
        'computed_salaries' => 2,
        'exclusions' => 1,
    ]);
});

it('is idempotent: re-fetching updates rows in place instead of duplicating', function () {
    fakePortalReader(samplePortalPayload());

    $service = app(PayrollPortalSyncService::class);
    $service->sync('2026', '3', 'regular', 'first_half');
    $service->sync('2026', '3', 'regular', 'first_half');

    expect(PayrollPeriod::count())->toBe(1);
    expect(Employee::count())->toBe(2);
    expect(EmployeeSalary::count())->toBe(2);
    expect(EmployeeTimeRecord::count())->toBe(2);
    expect(EmployeeComputedSalary::count())->toBe(2);
    expect(PayrollRun::count())->toBe(1);
});

it('throws PortalCacheMissing when the key is absent', function () {
    fakePortalReader(null);

    app(PayrollPortalSyncService::class)->sync('2026', '3', 'regular', 'first_half');
})->throws(PortalCacheMissing::class);

it('404s over HTTP when the cache key is absent', function () {
    fakePortalReader(null);

    $this->postJson('/api/v1/payroll-periods/fetch-from-portal', [
        'year' => 2026,
        'month' => 3,
        'employment_type' => 'regular',
        'period_type' => 'first_half',
    ])->assertNotFound();
});

it('fetches over HTTP when the payload is present', function () {
    fakePortalReader(samplePortalPayload());

    $this->postJson('/api/v1/payroll-periods/fetch-from-portal', [
        'year' => 2026,
        'month' => 3,
        'employment_type' => 'regular',
        'period_type' => 'first_half',
    ])
        ->assertOk()
        ->assertJsonPath('data.employees', 2);

    expect(Employee::count())->toBe(2);
});

it('validates the HTTP request', function () {
    fakePortalReader(samplePortalPayload());

    $this->postJson('/api/v1/payroll-periods/fetch-from-portal', [
        'year' => 2026,
        'month' => 13,
        'employment_type' => 'invalid',
        'period_type' => 'first_half',
    ])->assertStatus(422);
});

it('runs the payroll:fetch command', function () {
    fakePortalReader(samplePortalPayload());

    $this->artisan('payroll:fetch', [
        'year' => '2026',
        'month' => '3',
        'employmentType' => 'regular',
        'periodType' => 'first_half',
    ])->assertExitCode(0);

    expect(EmployeeComputedSalary::count())->toBe(2);
});
