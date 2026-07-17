<?php

namespace App\Filament\Resources\DeductionGroupResource\Pages;

use App\Filament\Resources\DeductionGroupResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeductionGroup extends EditRecord
{
    protected static string $resource = DeductionGroupResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
