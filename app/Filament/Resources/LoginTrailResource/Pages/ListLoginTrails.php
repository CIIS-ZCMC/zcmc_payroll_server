<?php

namespace App\Filament\Resources\LoginTrailResource\Pages;

use App\Filament\Resources\LoginTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLoginTrails extends ListRecords
{
    protected static string $resource = LoginTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
