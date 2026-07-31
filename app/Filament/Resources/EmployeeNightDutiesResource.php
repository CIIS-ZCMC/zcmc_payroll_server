<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeNightDutiesResource\Pages;
use App\Filament\Resources\EmployeeNightDutiesResource\RelationManagers;
use App\Models\EmployeeNightDuties;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeNightDutiesResource extends Resource
{
    protected static ?string $model = EmployeeNightDuties::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Time Records';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('payroll_period_id')
                    ->required(),
                Forms\Components\DatePicker::make('duty_date')
                    ->required(),
                Forms\Components\DateTimePicker::make('time_in')
                    ->required(),
                Forms\Components\DateTimePicker::make('time_out')
                    ->required(),
                Forms\Components\TextInput::make('night_minutes')
                    ->required(),
                Forms\Components\TextInput::make('night_hours')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('duty_date')
                    ->date(),
                Tables\Columns\TextColumn::make('time_in')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('time_out')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('night_minutes'),
                Tables\Columns\TextColumn::make('night_hours'),
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
            'index' => Pages\ListEmployeeNightDuties::route('/'),
            'create' => Pages\CreateEmployeeNightDuties::route('/create'),
            'edit' => Pages\EditEmployeeNightDuties::route('/{record}/edit'),
        ];
    }    
}
