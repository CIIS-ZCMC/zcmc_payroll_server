<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeDeductionResource\Pages;
use App\Filament\Resources\EmployeeDeductionResource\RelationManagers;
use App\Models\EmployeeDeduction;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeDeductionResource extends Resource
{
    protected static ?string $model = EmployeeDeduction::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Manages';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payroll_period_id'),
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('deduction_id')
                    ->required(),
                Forms\Components\TextInput::make('billing_cycle')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('amount'),
                Forms\Components\TextInput::make('percentage'),
                Forms\Components\TextInput::make('date_from')
                    ->maxLength(255),
                Forms\Components\TextInput::make('date_to')
                    ->maxLength(255),
                Forms\Components\Toggle::make('with_terms')
                    ->required(),
                Forms\Components\TextInput::make('total_term'),
                Forms\Components\TextInput::make('total_paid'),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\Textarea::make('isDifferential'),
                Forms\Components\Toggle::make('is_default')
                    ->required(),
                Forms\Components\DatePicker::make('effective_date'),
                Forms\Components\DatePicker::make('deduct_at'),
                Forms\Components\DateTimePicker::make('stopped_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('deduction_id'),
                Tables\Columns\TextColumn::make('billing_cycle'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('percentage'),
                Tables\Columns\TextColumn::make('date_from'),
                Tables\Columns\TextColumn::make('date_to'),
                Tables\Columns\IconColumn::make('with_terms')
                    ->boolean(),
                Tables\Columns\TextColumn::make('total_term'),
                Tables\Columns\TextColumn::make('total_paid'),
                Tables\Columns\TextColumn::make('reason'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('isDifferential'),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date(),
                Tables\Columns\TextColumn::make('deduct_at')
                    ->date(),
                Tables\Columns\TextColumn::make('stopped_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('completed_at')
                    ->dateTime(),
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
            'index' => Pages\ListEmployeeDeductions::route('/'),
            'create' => Pages\CreateEmployeeDeduction::route('/create'),
            'edit' => Pages\EditEmployeeDeduction::route('/{record}/edit'),
        ];
    }    
}
