<?php

use App\Filament\Resources\EmployeeReceivables\Pages\CreateEmployeeReceivable;
use App\Filament\Resources\EmployeeReceivables\Pages\ListEmployeeReceivables;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\RelationManagers\ReceivablesRelationManager;
use App\Models\Employee;
use App\Models\EmployeeReceivable;
use App\Models\EmployeeReceivableTerm;
use App\Models\Receivable;
use App\Models\ReceivableGroup;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeRcvEmployee(string $number): Employee
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

function makeReceivable(string $code): Receivable
{
    $group = ReceivableGroup::firstOrCreate(['code' => 'ALLOWANCE'], ['name' => 'Allowances']);

    return Receivable::create([
        'receivable_group_id' => $group->id,
        'name' => $code,
        'code' => $code,
        'is_active' => true,
    ]);
}

function makeStandingReceivable(Employee $employee, Receivable $receivable, string $status = 'active'): EmployeeReceivable
{
    return EmployeeReceivable::create([
        'employee_id' => $employee->id,
        'receivable_id' => $receivable->id,
        'payroll_period_id' => null,
        'billing_cycle' => 'monthly',
        'amount' => 1000,
        'effective_date' => '2026-01-01',
        'status' => $status,
        'is_active' => $status === 'active',
    ]);
}

function sampleReceivableCsv(): string
{
    return <<<'CSV'
    Code,PERA,,,,
    Date,February,2025,,,
    Seq. No.,Employee No.,Fullname,Amount,Term (Months),Months Paid
    2,2015081702,"ALVIA, JOSHUA M.",500,4,3
    3,2022101725,"ALVIA, MIA MARION S.",255.67,4,3
    4,9999999999,"GHOST, NOBODY X.",100,2,1

    CSV;
}

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('lists standing receivables', function () {
    $record = makeStandingReceivable(makeRcvEmployee('E-001'), makeReceivable('PERA'));

    Livewire::test(ListEmployeeReceivables::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$record]);
});

it('creates a standing receivable through the resource', function () {
    $employee = makeRcvEmployee('E-001');
    $receivable = makeReceivable('PERA');

    Livewire::test(CreateEmployeeReceivable::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'receivable_id' => $receivable->id,
            'amount' => 750,
            'billing_cycle' => 'monthly',
            'effective_date' => '2026-07-01',
            'status' => 'active',
            'is_active' => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $row = EmployeeReceivable::where('employee_id', $employee->id)->first();
    expect($row)->not->toBeNull();
    expect((float) $row->amount)->toBe(750.0);
    expect($row->receivable_id)->toBe($receivable->id);
});

it('stops an active receivable via the row action', function () {
    $record = makeStandingReceivable(makeRcvEmployee('E-001'), makeReceivable('PERA'));

    Livewire::test(ListEmployeeReceivables::class)
        ->callAction(TestAction::make('stop')->table($record))
        ->assertHasNoActionErrors();

    $record->refresh();
    expect($record->status)->toBe('suspended');
    expect($record->is_active)->toBeFalse();
});

it('completes a receivable via the row action', function () {
    $record = makeStandingReceivable(makeRcvEmployee('E-001'), makeReceivable('PERA'));

    Livewire::test(ListEmployeeReceivables::class)
        ->callAction(TestAction::make('complete')->table($record))
        ->assertHasNoActionErrors();

    $record->refresh();
    expect($record->status)->toBe('completed');
    expect($record->is_active)->toBeFalse();
});

it('filters the table by status', function () {
    $pera = makeReceivable('PERA');
    $active = makeStandingReceivable(makeRcvEmployee('E-001'), $pera, 'active');
    $suspended = makeStandingReceivable(makeRcvEmployee('E-002'), $pera, 'suspended');

    Livewire::test(ListEmployeeReceivables::class)
        ->filterTable('status', 'suspended')
        ->assertCanSeeTableRecords([$suspended])
        ->assertCanNotSeeTableRecords([$active]);
});

it('renders the receivables relation manager on an employee', function () {
    $employee = makeRcvEmployee('E-001');
    $record = makeStandingReceivable($employee, makeReceivable('PERA'));

    Livewire::test(ReceivablesRelationManager::class, [
        'ownerRecord' => $employee,
        'pageClass' => EditEmployee::class,
    ])
        ->assertOk()
        ->assertCanSeeTableRecords([$record]);
});

it('shows the import action on the employee-receivables list page', function () {
    Livewire::test(ListEmployeeReceivables::class)
        ->assertActionExists('importReceivables');
});

it('imports employee receivables from an uploaded CSV', function () {
    $receivable = makeReceivable('PERA');
    $joshua = makeRcvEmployee('2015081702');
    makeRcvEmployee('2022101725');

    Livewire::test(ListEmployeeReceivables::class)
        ->callAction('importReceivables', data: [
            'file' => UploadedFile::fake()->createWithContent('receivables.csv', sampleReceivableCsv()),
            'receivable_id' => null,
            'payroll_period_id' => null,
        ])
        ->assertHasNoActionErrors();

    // Two known employees persisted; the ghost row is skipped.
    expect(EmployeeReceivable::count())->toBe(2);

    $row = EmployeeReceivable::with('terms')->where('employee_id', $joshua->id)->first();
    expect($row->receivable_id)->toBe($receivable->id);
    expect((float) $row->amount)->toBe(500.0);
    expect($row->effective_date->toDateString())->toBe('2025-02-01');
    expect($row->terms->total_terms)->toBe(4);
    expect($row->terms->paid_terms)->toBe(3);
    expect(EmployeeReceivableTerm::count())->toBe(2);
});
