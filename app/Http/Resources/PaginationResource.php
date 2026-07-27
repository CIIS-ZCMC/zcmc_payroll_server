<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaginationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $paginator = $this->resource;

        return [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'item_from' => $paginator->firstItem(),
            'item_to' => $paginator->lastItem()
        ];
    }
}
