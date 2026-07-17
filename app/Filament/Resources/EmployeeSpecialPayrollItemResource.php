<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeSpecialPayrollItemResource\Pages;
use App\Filament\Resources\EmployeeSpecialPayrollItemResource\RelationManagers;
use App\Models\EmployeeSpecialPayrollItem;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeSpecialPayrollItemResource extends Resource
{
    protected static ?string $model = EmployeeSpecialPayrollItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('payroll_period_id')
                    ->required(),
                Forms\Components\TextInput::make('special_payroll_id')
                    ->required(),
                Forms\Components\TextInput::make('special_payroll_component_id')
                    ->required(),
                Forms\Components\TextInput::make('component_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('special_payroll_id'),
                Tables\Columns\TextColumn::make('special_payroll_component_id'),
                Tables\Columns\TextColumn::make('component_type'),
                Tables\Columns\TextColumn::make('label'),
                Tables\Columns\TextColumn::make('amount'),
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
            'index' => Pages\ListEmployeeSpecialPayrollItems::route('/'),
            'create' => Pages\CreateEmployeeSpecialPayrollItem::route('/create'),
            'edit' => Pages\EditEmployeeSpecialPayrollItem::route('/{record}/edit'),
        ];
    }    
}
