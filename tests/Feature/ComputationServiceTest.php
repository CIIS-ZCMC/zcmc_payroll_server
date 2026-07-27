<?php

use App\Models\Receivable;
use App\Models\ReceivableGroup;
use App\Services\Helper\ComputationService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->service = new ComputationService();
});

it('prorates initial salary over required duty days plus allowances', function () {
    // 22000 / 22 = 1000/day; 22 days present + 2000 allowance
    expect($this->service->initialSalary(22000, 22, 2000))->toBe(24000.0);
    // partial month, no allowance
    expect($this->service->initialSalary(22000, 11))->toBe(11000.0);
});

it('computes daily, hourly and minute rates', function () {
    expect($this->service->dailyRate(22000))->toBe(1000.0);
    expect($this->service->hourlyRate(22000))->toBe(125.0);
    expect($this->service->minuteRate(22000))->toBe(round(125 / 60, 4));
});

it('computes absent and undertime deductions', function () {
    expect($this->service->absentDeduction(22000, 2))->toBe(2000.0);
    expect($this->service->undertimeDeduction(22000, 60))->toBe(round($this->service->minuteRate(22000) * 60, 2));
});

it('computes hazard pay by grade and zeroes it past the absence threshold', function () {
    // grade 15 -> 25%
    expect($this->service->computeHazard(15, 22000))->toBe(5500.0);
    // grade 20 -> 15%
    expect($this->service->computeHazard(20, 22000))->toBe(3300.0);
    // 11+ absent or leave days -> not entitled
    expect($this->service->computeHazard(15, 22000, absentDays: 11))->toBe(0.0);
    expect($this->service->computeHazard(15, 22000, leaveDays: 11))->toBe(0.0);
});

it('resolves PERA from the seeded receivable and prorates for absences', function () {
    $group = ReceivableGroup::create(['name' => 'Allowances', 'code' => 'ALLOWANCE']);
    Receivable::create([
        'receivable_group_id' => $group->id,
        'name' => 'PERA',
        'code' => Receivable::CODE_PERA,
        'fixed_amount' => 2000,
        'is_active' => true,
    ]);

    // full PERA when no absences
    expect($this->service->computePera(22, 0))->toBe(2000.0);
    // 2000 - (round(2000/22,2)=90.91 * 2) = 1818.18
    expect($this->service->computePera(22, 2))->toBe(1818.18);
    // no present days -> no PERA
    expect($this->service->computePera(0, 0))->toBe(0.0);
});
