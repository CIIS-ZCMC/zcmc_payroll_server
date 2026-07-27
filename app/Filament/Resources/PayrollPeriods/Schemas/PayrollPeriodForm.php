<?php

namespace App\Filament\Resources\PayrollPeriods\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PayrollPeriodForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('employment_type')
                    ->options(['permanent' => 'Permanent', 'contractual' => 'Contractual', 'temporary' => 'Temporary'])
                    ->required(),
                TextInput::make('month')
                    ->required()
                    ->numeric(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                Select::make('payroll_type')
                    ->options([
                        'monthly' => 'Monthly',
                        'semi-monthly' => 'Semi monthly',
                        'bi-weekly' => 'Bi weekly',
                        'weekly' => 'Weekly',
                    ])
                    ->required(),
                Select::make('period_type')
                    ->options(['regular' => 'Regular', 'special' => 'Special', '13th month' => '13th month'])
                    ->required(),
                DatePicker::make('period_start')
                    ->required(),
                DatePicker::make('period_end')
                    ->required(),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'posted' => 'Posted', 'locked' => 'Locked', 'released' => 'Released'])
                    ->required(),
                Toggle::make('is_active')
                    ->required(),
                DateTimePicker::make('posted_at'),
                DateTimePicker::make('locked_at'),
            ]);
    }
}
