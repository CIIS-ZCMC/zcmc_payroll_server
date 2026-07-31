<?php

use App\Filament\Resources\EmployeeDeductions\Pages\CreateEmployeeDeduction;
use App\Filament\Resources\EmployeeDeductions\Pages\ListEmployeeDeductions;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\RelationManagers\DeductionsRelationManager;
use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\EmployeeDeductionTerm;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeEmp(string $number): Employee
{
    static $profileId = 0;

    return Employee::create([
        'employee_profile_id' => ++$profileId,
        'employee_number' => $number,
        'first_name' => 'Test',
        'last_name' => $number,
        'designation' => 'Nurse I',
        'assigned_area' => ['ward' => 'ER'],
        'hire_date' => '2020-01-01',
    ]);
}

function makeDed(string $code): Deduction
{
    $group = DeductionGroup::firstOrCreate(['code' => 'GOV'], ['name' => 'Government']);

    return Deduction::create([
        'deduction_group_id' => $group->id,
        'name' => $code,
        'code' => $code,
        'is_active' => true,
    ]);
}

function makeStanding(Employee $employee, Deduction $deduction, string $status = 'active'): EmployeeDeduction
{
    return EmployeeDeduction::create([
        'employee_id' => $employee->id,
        'deduction_id' => $deduction->id,
        'payroll_period_id' => null,
        'billing_cycle' => 'monthly',
        'amount' => 1000,
        'effective_date' => '2026-01-01',
        'status' => $status,
        'is_active' => $status === 'active',
    ]);
}

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists standing deductions', function () {
    $record = makeStanding(makeEmp('E-001'), makeDed('GSIS'));

    Livewire::test(ListEmployeeDeductions::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$record]);
});

it('creates a standing deduction through the resource', function () {
    $employee = makeEmp('E-001');
    $deduction = makeDed('GSIS');

    Livewire::test(CreateEmployeeDeduction::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'deduction_id' => $deduction->id,
            'amount' => 750,
            'billing_cycle' => 'monthly',
            'effective_date' => '2026-07-01',
            'status' => 'active',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $row = EmployeeDeduction::where('employee_id', $employee->id)->first();
    expect($row)->not->toBeNull();
    expect((float) $row->amount)->toBe(750.0);
    expect($row->deduction_id)->toBe($deduction->id);
});

it('stops an active deduction via the row action', function () {
    $record = makeStanding(makeEmp('E-001'), makeDed('GSIS'));

    Livewire::test(ListEmployeeDeductions::class)
        ->callAction(TestAction::make('stop')->table($record))
        ->assertHasNoActionErrors();

    $record->refresh();
    expect($record->status)->toBe('suspended');
    expect($record->is_active)->toBeFalse();
});

it('completes a deduction via the row action', function () {
    $record = makeStanding(makeEmp('E-001'), makeDed('GSIS'));

    Livewire::test(ListEmployeeDeductions::class)
        ->callAction(TestAction::make('complete')->table($record))
        ->assertHasNoActionErrors();

    $record->refresh();
    expect($record->status)->toBe('completed');
    expect($record->is_active)->toBeFalse();
});

it('filters the table by status', function () {
    $gsis = makeDed('GSIS');
    $active = makeStanding(makeEmp('E-001'), $gsis, 'active');
    $suspended = makeStanding(makeEmp('E-002'), $gsis, 'suspended');

    Livewire::test(ListEmployeeDeductions::class)
        ->filterTable('status', 'suspended')
        ->assertCanSeeTableRecords([$suspended])
        ->assertCanNotSeeTableRecords([$active]);
});

it('renders the deductions relation manager on an employee', function () {
    $employee = makeEmp('E-001');
    $record = makeStanding($employee, makeDed('GSIS'));

    Livewire::test(DeductionsRelationManager::class, [
        'ownerRecord' => $employee,
        'pageClass' => EditEmployee::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$record]);
});

function employeeDeductionSampleCsv(): string
{
    return <<<'CSV'
    Code,TAX,,,,
    Date,February,2025,,,
    Seq. No.,Employee No.,Fullname,Amount,Term (Months),Months Paid
    2,2015081702,"ALVIA, JOSHUA M.",500,4,3
    3,2022101725,"ALVIA, MIA MARION S.",255.67,4,3
    4,9999999999,"GHOST, NOBODY X.",100,2,1

    CSV;
}

it('shows the import action on the employee-deductions list page', function () {
    Livewire::test(ListEmployeeDeductions::class)
        ->assertActionExists('importDeductions');
});

it('imports employee deductions from an uploaded CSV', function () {
    $deduction = makeDed('WTAX');
    $joshua = makeEmp('2015081702');
    makeEmp('2022101725');

    Livewire::test(ListEmployeeDeductions::class)
        ->callAction('importDeductions', data: [
            'file' => UploadedFile::fake()->createWithContent('deductions.csv', employeeDeductionSampleCsv()),
            'deduction_id' => null,
            'payroll_period_id' => null,
        ])
        ->assertHasNoActionErrors();

    // Two known employees persisted; the ghost row is skipped.
    expect(EmployeeDeduction::count())->toBe(2);

    $row = EmployeeDeduction::with('terms')->where('employee_id', $joshua->id)->first();
    expect($row->deduction_id)->toBe($deduction->id);
    expect((float) $row->amount)->toBe(500.0);
    expect($row->effective_date->toDateString())->toBe('2025-02-01');
    expect($row->terms->total_terms)->toBe(4);
    expect($row->terms->paid_terms)->toBe(3);
    expect(EmployeeDeductionTerm::count())->toBe(2);
});
