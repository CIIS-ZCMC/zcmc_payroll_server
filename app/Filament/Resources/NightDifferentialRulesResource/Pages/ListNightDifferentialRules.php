<?php

namespace App\Filament\Resources\NightDifferentialRulesResource\Pages;

use App\Filament\Resources\NightDifferentialRulesResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListNightDifferentialRules extends ListRecords
{
    protected static string $resource = NightDifferentialRulesResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
