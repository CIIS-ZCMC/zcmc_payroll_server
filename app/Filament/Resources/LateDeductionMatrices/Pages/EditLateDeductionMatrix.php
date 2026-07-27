<?php

namespace App\Filament\Resources\LateDeductionMatrices\Pages;

use App\Filament\Resources\LateDeductionMatrices\LateDeductionMatrixResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLateDeductionMatrix extends EditRecord
{
    protected static string $resource = LateDeductionMatrixResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
