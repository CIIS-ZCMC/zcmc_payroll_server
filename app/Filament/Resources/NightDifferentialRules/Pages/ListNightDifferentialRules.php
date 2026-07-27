<?php

namespace App\Filament\Resources\NightDifferentialRules\Pages;

use App\Filament\Resources\NightDifferentialRules\NightDifferentialRuleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListNightDifferentialRules extends ListRecords
{
    protected static string $resource = NightDifferentialRuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
