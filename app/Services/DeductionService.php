<?php

namespace App\Services;

use App\Contract\DeductionInterface;
use App\Data\DeductionData;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeductionService
{
    public function __construct(private DeductionInterface $interface)
    {
        //nothing
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function index(Request $request): Collection|LengthAwarePaginator
    {
        $mode = $request->mode;
        $perPage = $request->per_page ?? 15;
        $page = $request->page ?? 1;

        if ($mode === 'paginate') {
            return $this->paginate($perPage, $page);
        }

        return $this->getAll();
    }

    public function create(DeductionData $data): Deduction
    {
        return $this->interface->create([
            'deduction_uuid' => $data->deduction_uuid,
            'deduction_group_id' => $data->deduction_group_id,
            'name' => $data->name,
            'code' => $data->code,
            'type' => $data->type,
            'hasDate' => $data->hasDate,
            'date_start' => $data->date_start,
            'date_end' => $data->date_end,
            'condition_operator' => $data->condition_operator,
            'condition_value' => $data->condition_value,
            'percent_value' => $data->percent_value,
            'fixed_amount' => $data->fixed_amount,
            'billing_cycle' => $data->billing_cycle,
            'status' => $data->status,
        ]);
    }

    public function update(int $id, DeductionData $data): Deduction
    {
        return $this->interface->update($id, [
            'name' => $data->name,
            'code' => $data->code,
            'type' => $data->type,
            'hasDate' => $data->hasDate,
            'date_start' => $data->date_start,
            'date_end' => $data->date_end,
            'condition_operator' => $data->condition_operator,
            'condition_value' => $data->condition_value,
            'percent_value' => $data->percent_value,
            'fixed_amount' => $data->fixed_amount,
            'billing_cycle' => $data->billing_cycle,
            'status' => $data->status,
        ]);
    }

    public function find(int $id): ?Deduction
    {
        return $this->interface->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}