<?php

namespace App\Filament\Resources\DeductionGroupResource\Pages;

use App\Filament\Resources\DeductionGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeductionGroups extends ListRecords
{
    protected static string $resource = DeductionGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
