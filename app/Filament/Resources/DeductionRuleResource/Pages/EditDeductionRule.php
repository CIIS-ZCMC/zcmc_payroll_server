<?php

namespace App\Filament\Resources\DeductionRuleResource\Pages;

use App\Filament\Resources\DeductionRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeductionRule extends EditRecord
{
    protected static string $resource = DeductionRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
