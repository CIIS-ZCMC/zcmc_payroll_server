<?php

namespace App\Filament\Resources\StoppageLogResource\Pages;

use App\Filament\Resources\StoppageLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStoppageLog extends EditRecord
{
    protected static string $resource = StoppageLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
