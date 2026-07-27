<?php

namespace App\Filament\Resources\DeductionGroups\Pages;

use App\Filament\Resources\DeductionGroups\DeductionGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDeductionGroups extends ListRecords
{
    protected static string $resource = DeductionGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
