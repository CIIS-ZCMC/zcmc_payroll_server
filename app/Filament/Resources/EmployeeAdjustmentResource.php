<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeAdjustmentResource\Pages;
use App\Filament\Resources\EmployeeAdjustmentResource\RelationManagers;
use App\Models\EmployeeAdjustment;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeAdjustmentResource extends Resource
{
    protected static ?string $model = EmployeeAdjustment::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Employees';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payroll_period_id')
                    ->required(),
                Forms\Components\TextInput::make('employee_deduction_id'),
                Forms\Components\TextInput::make('employee_receivable_id'),
                Forms\Components\Textarea::make('action_by')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount_to_pay')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount_balance')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reason')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('employee_deduction_id'),
                Tables\Columns\TextColumn::make('employee_receivable_id'),
                Tables\Columns\TextColumn::make('action_by'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('amount_to_pay'),
                Tables\Columns\TextColumn::make('amount_balance'),
                Tables\Columns\TextColumn::make('reason'),
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
            'index' => Pages\ListEmployeeAdjustments::route('/'),
            'create' => Pages\CreateEmployeeAdjustment::route('/create'),
            'edit' => Pages\EditEmployeeAdjustment::route('/{record}/edit'),
        ];
    }    
}
