<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeComputedSalaryResource\Pages;
use App\Filament\Resources\EmployeeComputedSalaryResource\RelationManagers;
use App\Models\EmployeeComputedSalary;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeComputedSalaryResource extends Resource
{
    protected static ?string $model = EmployeeComputedSalary::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Time Records';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('payroll_period_id')
                    ->required(),
                Forms\Components\TextInput::make('employee_time_record_id')
                    ->required(),
                Forms\Components\TextInput::make('basic_pay')
                    ->required(),
                Forms\Components\TextInput::make('minutes_rate')
                    ->required(),
                Forms\Components\TextInput::make('daily_rate')
                    ->required(),
                Forms\Components\TextInput::make('hourly_rate')
                    ->required(),
                Forms\Components\TextInput::make('absent_rate')
                    ->required(),
                Forms\Components\TextInput::make('undertime_rate')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('employee_time_record_id'),
                Tables\Columns\TextColumn::make('basic_pay'),
                Tables\Columns\TextColumn::make('minutes_rate'),
                Tables\Columns\TextColumn::make('daily_rate'),
                Tables\Columns\TextColumn::make('hourly_rate'),
                Tables\Columns\TextColumn::make('absent_rate'),
                Tables\Columns\TextColumn::make('undertime_rate'),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployeeComputedSalaries::route('/'),
            'create' => Pages\CreateEmployeeComputedSalary::route('/create'),
            'edit' => Pages\EditEmployeeComputedSalary::route('/{record}/edit'),
        ];
    }    
}
