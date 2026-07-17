<?php

namespace App\Filament\Resources\LogsAndTrailResource\Pages;

use App\Filament\Resources\LogsAndTrailResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLogsAndTrail extends EditRecord
{
    protected static string $resource = LogsAndTrailResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
