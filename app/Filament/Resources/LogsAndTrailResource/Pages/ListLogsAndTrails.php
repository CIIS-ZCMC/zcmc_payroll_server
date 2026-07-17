<?php

namespace App\Filament\Resources\LogsAndTrailResource\Pages;

use App\Filament\Resources\LogsAndTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLogsAndTrails extends ListRecords
{
    protected static string $resource = LogsAndTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
