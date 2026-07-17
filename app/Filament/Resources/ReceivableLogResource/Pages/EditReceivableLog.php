<?php

namespace App\Filament\Resources\ReceivableLogResource\Pages;

use App\Filament\Resources\ReceivableLogResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceivableLog extends EditRecord
{
    protected static string $resource = ReceivableLogResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
