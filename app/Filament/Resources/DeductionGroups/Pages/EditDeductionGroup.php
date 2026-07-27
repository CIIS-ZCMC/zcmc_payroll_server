<?php

namespace App\Filament\Resources\DeductionGroups\Pages;

use App\Filament\Resources\DeductionGroups\DeductionGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDeductionGroup extends EditRecord
{
    protected static string $resource = DeductionGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
