<?php

namespace App\Services;

use App\Contract\ReceivableGroupInterface;
use App\Contract\ReceivableInterface;
use App\Data\ReceivableData;
use App\Models\Receivable;
use App\Models\ReceivableGroup;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReceivableService
{
    public function __construct(private ReceivableInterface $interface) {}

    public function index(bool $isPaginate = false, int $perPage = 15, int $page = 1): Collection|LengthAwarePaginator
    {
        if ($isPaginate) {
            return $this->paginate($perPage, $page);
        }

        $total =  $this->getAll()->count();
        return $this->paginate($total, $page);
    }

    public function getAll(): Collection
    {
        return $this->interface->getAll();
    }

    public function find(int $id): ?Receivable
    {
        return $this->interface->find($id);
    }

    public function paginate(int $perPage, int $page): LengthAwarePaginator
    {
        return $this->interface->paginate($perPage, $page);
    }

    public function create(ReceivableData $data): Receivable
    {
        return $this->interface->create([$data]);
    }

    public function update(int $id, ReceivableData $data): Receivable
    {
        return $this->interface->update($id, [$data]);
    }

    public function delete(int $id): bool
    {
        return $this->interface->delete($id);
    }
}
