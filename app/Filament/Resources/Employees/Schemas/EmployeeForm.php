<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('employee_number')
                    ->required(),
                TextInput::make('first_name')
                    ->required(),
                TextInput::make('last_name')
                    ->required(),
                TextInput::make('middle_name'),
                TextInput::make('extension_name'),
                TextInput::make('designation')
                    ->required(),
                DatePicker::make('hire_date')
                    ->required(),
                Toggle::make('is_newly_hired')
                    ->required(),
            ]);
    }
}
