<?php

namespace App\Filament\Resources\NightDifferentialRules\Pages;

use App\Filament\Resources\NightDifferentialRules\NightDifferentialRuleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditNightDifferentialRule extends EditRecord
{
    protected static string $resource = NightDifferentialRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
