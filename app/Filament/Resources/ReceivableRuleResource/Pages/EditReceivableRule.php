<?php

namespace App\Filament\Resources\ReceivableRuleResource\Pages;

use App\Filament\Resources\ReceivableRuleResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditReceivableRule extends EditRecord
{
    protected static string $resource = ReceivableRuleResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
