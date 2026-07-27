<?php

namespace App\Filament\Resources\NightDifferentialRules\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class NightDifferentialRuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employment_type')
                    ->options(['permanent' => 'Permanent', 'contractual' => 'Contractual', 'temporary' => 'Temporary'])
                    ->required(),
                TimePicker::make('start_time')
                    ->required(),
                TimePicker::make('end_time')
                    ->required(),
                Select::make('rate_type')
                    ->options([
                        'percentage' => 'Percentage',
                        'fixed_per_minute' => 'Fixed per minute',
                        'fixed_per_hour' => 'Fixed per hour',
                    ])
                    ->required(),
                TextInput::make('rate')
                    ->required()
                    ->numeric(),
                DatePicker::make('effective_date')
                    ->required(),
                DatePicker::make('end_date'),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
