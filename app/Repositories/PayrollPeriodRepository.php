<?php

namespace App\Repositories;

use App\Contract\PayrollPeriodInterface;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;

class PayrollPeriodRepository implements PayrollPeriodInterface
{
    public function __construct(private PayrollPeriod $model) {}

    /**
     * Maps the integer payroll type used by callers to the enum stored in the database.
     *
     * @var array<int, string>
     */
    public const PAYROLL_TYPES = [
        1 => 'monthly',
        2 => 'weekly',
    ];

    public function getAll(): Collection
    {
        return $this->model->latest('id')->get();
    }

    public function getActive(): ?PayrollPeriod
    {
        return $this->model->where('is_active', true)->latest('id')->first();
    }

    public function setActive(int $id): PayrollPeriod
    {
        $period = $this->model->findOrFail($id);
        $period->update(['is_active' => true]);

        return $period;
    }

    public function deactivateOthers(int $id): bool
    {
        return $this->model->whereKeyNot($id)->update(['is_active' => false]) >= 0;
    }

    public function create(array $data): PayrollPeriod
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): PayrollPeriod
    {
        $period = $this->model->findOrFail($id);
        $period->update($data);

        return $period;
    }

    public function lock(int $id): PayrollPeriod
    {
        $period = $this->model->findOrFail($id);
        $period->update(['status' => 'locked', 'locked_at' => now()]);

        return $period;
    }

    public function updateOrCreate(array $data): PayrollPeriod
    {
        return $this->model->updateOrCreate(
            [
                'employment_type' => $data['employment_type'],
                'month' => $data['month'],
                'year' => $data['year'],
                'payroll_type' => $data['payroll_type'],
            ],
            $data
        );
    }

    public function findPeriod(int $year, int $month, string $periodType, string $employmentType): ?PayrollPeriod
    {
        return $this->model->where('year', $year)
            ->where('month', $month)
            ->where('period_type', $periodType)
            ->where('employment_type', $employmentType)
            ->latest('id')
            ->first();
    }

    public function isLocked(int $id): bool
    {
        return $this->model->whereKey($id)
            ->where(fn($query) => $query->where('status', 'locked')->orWhereNotNull('locked_at'))
            ->exists();
    }

    public function previous(int $periodId): ?PayrollPeriod
    {
        $period = $this->model->findOrFail($periodId);

        return $this->model
            ->where('id', '<', $period->id)
            ->where('employment_type', $period->employment_type)
            ->where('payroll_type', $period->payroll_type)
            ->orderByDesc('id')
            ->first();
    }

    public function upsert(array $data): int
    {
        return $this->model->upsert(
            $data,
            ['employment_type', 'month', 'year', 'payroll_type'],
            ['period_type', 'period_start', 'period_end', 'status']
        );
    }
}
