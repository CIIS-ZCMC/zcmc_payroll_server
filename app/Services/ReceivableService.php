<?php

namespace App\Services;

use App\Contract\ReceivableInterface;
use App\Data\ReceivableData;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReceivableService
{
    public function __construct(private ReceivableInterface $interface)
    {
        //Nothing
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

    public function create(ReceivableData $data): Receivable
    {
        return $this->interface->create([
            'receivable_uuid' => $data->receivable_uuid,
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

    public function update(int $id, ReceivableData $data): Receivable
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

    public function delete($id): bool
    {
        return $this->interface->delete($id);
    }
}
