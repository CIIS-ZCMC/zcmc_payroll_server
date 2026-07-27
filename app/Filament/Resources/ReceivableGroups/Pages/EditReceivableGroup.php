<?php

namespace App\Filament\Resources\ReceivableGroups\Pages;

use App\Filament\Resources\ReceivableGroups\ReceivableGroupResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReceivableGroup extends EditRecord
{
    protected static string $resource = ReceivableGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
