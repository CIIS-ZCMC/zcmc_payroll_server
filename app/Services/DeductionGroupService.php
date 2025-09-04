<?php

namespace App\Services;

use App\Contract\DeductionGroupInterface;
use App\Data\DeductionGroupData;
use App\Models\DeductionGroup;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DeductionGroupService
{
    public function __construct(private DeductionGroupInterface $interface)
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

    public function create(DeductionGroupData $data): DeductionGroup
    {
        return $this->interface->create([
            'deduction_group_uuid' => $data->deduction_group_uuid,
            'name' => $data->name,
            'code' => $data->code
        ]);
    }

    public function update(int $id, DeductionGroupData $data): DeductionGroup
    {
        return $this->interface->update($id, [
            'name' => $data->name,
            'code' => $data->code
        ]);
    }

    public function find(int $id): ?DeductionGroup
    {
        return $this->interface->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}