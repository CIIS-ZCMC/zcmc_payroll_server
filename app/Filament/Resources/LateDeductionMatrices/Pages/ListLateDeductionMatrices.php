<?php

namespace App\Filament\Resources\LateDeductionMatrices\Pages;

use App\Filament\Resources\LateDeductionMatrices\LateDeductionMatrixResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLateDeductionMatrices extends ListRecords
{
    protected static string $resource = LateDeductionMatrixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
