<?php

use App\Models\Deduction;
use App\Models\DeductionGroup;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Services\BulkEmployeeDeductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function makeImportEmployee(string $number): Employee
{
    static $profileId = 0;

    return Employee::create([
        'employee_profile_id' => ++$profileId,
        'employee_number' => $number,
        'first_name' => 'Test',
        'last_name' => $number,
        'designation' => 'Nurse I',
        'hire_date' => '2020-01-01',
    ]);
}

function makeWtax(): Deduction
{
    $group = DeductionGroup::firstOrCreate(['code' => 'GOV'], ['name' => 'Government']);

    return Deduction::create([
        'deduction_group_id' => $group->id,
        'name' => 'Withholding Tax',
        'code' => 'WTAX',
        'is_active' => true,
    ]);
}

function sampleDeductionCsv(): string
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

it('resolves the deduction from the file code and parses installment rows', function () {
    makeWtax();
    makeImportEmployee('2015081702');
    makeImportEmployee('2022101725');

    $result = app(BulkEmployeeDeductionService::class)->parse(sampleDeductionCsv());

    expect($result['deduction']['code'])->toBe('WTAX');
    expect($result['period'])->toBe(['month' => 2, 'year' => 2025]);
    expect($result['rows'])->toHaveCount(2);
    expect($result['rows'][0])->toMatchArray([
        'employee_number' => '2015081702',
        'amount' => 500.0,
        'total_terms' => 4,
        'paid_terms' => 3,
    ]);
    expect($result['errors'])->toHaveCount(1);
    expect($result['errors'][0]['error'])->toBe('employee_not_found');
});

it('bulk-stores deductions with their term tracking', function () {
    $deduction = makeWtax();
    $joshua = makeImportEmployee('2015081702');
    makeImportEmployee('2022101725');

    $result = app(BulkEmployeeDeductionService::class)->import(sampleDeductionCsv());

    expect($result['stored'])->toBe(2);
    expect($result['batches'])->toBe(1);
    expect(EmployeeDeduction::count())->toBe(2);

    $row = EmployeeDeduction::with('terms')->where('employee_id', $joshua->id)->first();
    expect($row->deduction_id)->toBe($deduction->id);
    expect((float) $row->amount)->toBe(500.0);
    expect($row->is_active)->toBeTrue();
    expect($row->effective_date->toDateString())->toBe('2025-02-01');

    expect($row->terms->total_terms)->toBe(4);
    expect($row->terms->paid_terms)->toBe(3);
    expect((float) $row->terms->term_amount)->toBe(500.0);
    expect((float) $row->terms->total_amount)->toBe(2000.0);
    expect((float) $row->terms->remaining_balance)->toBe(500.0);
    expect($row->terms->completed_at)->toBeNull();
});

it('is idempotent: re-importing updates rows in place instead of duplicating', function () {
    makeWtax();
    $joshua = makeImportEmployee('2015081702');
    makeImportEmployee('2022101725');

    $service = app(BulkEmployeeDeductionService::class);
    $service->import(sampleDeductionCsv());

    // Re-import the same file with a changed amount for Joshua (500 -> 650).
    $second = str_replace('"ALVIA, JOSHUA M.",500,4,3', '"ALVIA, JOSHUA M.",650,4,3', sampleDeductionCsv());
    $result = $service->import($second);

    expect($result['stored'])->toBe(2);
    expect(EmployeeDeduction::count())->toBe(2); // no duplicates

    $row = EmployeeDeduction::with('terms')->where('employee_id', $joshua->id)->first();
    expect((float) $row->amount)->toBe(650.0);
    expect((float) $row->terms->term_amount)->toBe(650.0);
});

it('marks a term complete when no balance remains', function () {
    makeWtax();
    makeImportEmployee('2015081702');

    $csv = "Code,TAX\nDate,February,2025\nSeq. No.,Employee No.,Fullname,Amount,Term (Months),Months Paid\n1,2015081702,\"ALVIA, JOSHUA M.\",500,4,4\n";

    app(BulkEmployeeDeductionService::class)->import($csv);

    $terms = EmployeeDeduction::with('terms')->first()->terms;
    expect((float) $terms->remaining_balance)->toBe(0.0);
    expect($terms->completed_at)->not->toBeNull();
});

it('stores every row across multiple batches when the file exceeds the batch size', function () {
    makeWtax();

    $lines = [
        'Code,TAX',
        'Date,February,2025',
        'Seq. No.,Employee No.,Fullname,Amount,Term (Months),Months Paid',
    ];

    for ($i = 1; $i <= 5; $i++) {
        $number = '30000000'.$i;
        makeImportEmployee($number);
        $lines[] = "{$i},{$number},\"EMP, NUMBER {$i}\",100,3,1";
    }

    $service = app(BulkEmployeeDeductionService::class);
    $service->batchSize = 2; // force 3 batches (2 + 2 + 1)

    $result = $service->import(implode("\n", $lines)."\n");

    expect($result['stored'])->toBe(5);
    expect($result['batches'])->toBe(3);
    expect(EmployeeDeduction::count())->toBe(5);
});
