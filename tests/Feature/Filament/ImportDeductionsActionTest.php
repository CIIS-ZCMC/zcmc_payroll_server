<?php

use App\Filament\Resources\Deductions\Pages\ListDeductions;
use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;

uses(RefreshDatabase::class);

function makeDeductionImportEmployee(string $number): Employee
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

function makeImportWtax(): Deduction
{
    $group = DeductionGroup::firstOrCreate(['code' => 'GOV'], ['name' => 'Government']);

    return Deduction::create([
        'deduction_group_id' => $group->id,
        'name' => 'Withholding Tax',
        'code' => 'WTAX',
        'is_active' => true,
    ]);
}

/**
 * The canonical single-deduction template consumed by the importer: a `Code` /
 * `Date` preamble, a header row, then one row per employee (the last is an
 * unknown employee that should be skipped, not imported).
 */
function sampleImportCsv(): string
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

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

it('renders the import action on the deductions list page', function () {
    Livewire::test(ListDeductions::class)
        ->assertActionExists('importDeductions')
        ->assertActionVisible('importDeductions');
});

it('imports employee deductions end to end from an uploaded CSV', function () {
    $deduction = makeImportWtax();
    $joshua = makeDeductionImportEmployee('2015081702');
    makeDeductionImportEmployee('2022101725');

    Livewire::test(ListDeductions::class)
        ->callAction('importDeductions', data: [
            'file' => UploadedFile::fake()->createWithContent('deductions.csv', sampleImportCsv()),
            'deduction_id' => null,
            'payroll_period_id' => null,
        ])
        ->assertHasNoActionErrors();

    // Only the two known employees are persisted; the ghost row is skipped.
    expect(EmployeeDeduction::count())->toBe(2);

    $row = EmployeeDeduction::with('terms')->where('employee_id', $joshua->id)->first();
    expect($row->deduction_id)->toBe($deduction->id);
    expect((float) $row->amount)->toBe(500.0);
    expect($row->effective_date->toDateString())->toBe('2025-02-01');
    expect($row->terms->total_terms)->toBe(4);
    expect($row->terms->paid_terms)->toBe(3);
    expect((float) $row->terms->remaining_balance)->toBe(500.0);
});
