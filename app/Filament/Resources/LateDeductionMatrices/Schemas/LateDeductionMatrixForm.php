<?php

namespace App\Filament\Resources\LateDeductionMatrices\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LateDeductionMatrixForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employment_type')
                    ->options(['permanent' => 'Permanent', 'contractual' => 'Contractual', 'temporary' => 'Temporary'])
                    ->required(),
                TextInput::make('from_minutes')
                    ->required()
                    ->numeric(),
                TextInput::make('to_minutes')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
            ]);
    }
}
