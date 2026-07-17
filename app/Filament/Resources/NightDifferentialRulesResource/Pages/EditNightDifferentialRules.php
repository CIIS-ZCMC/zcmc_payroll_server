<?php

namespace App\Filament\Resources\NightDifferentialRulesResource\Pages;

use App\Filament\Resources\NightDifferentialRulesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditNightDifferentialRules extends EditRecord
{
    protected static string $resource = NightDifferentialRulesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
