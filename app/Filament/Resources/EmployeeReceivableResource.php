<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeReceivableResource\Pages;
use App\Filament\Resources\EmployeeReceivableResource\RelationManagers;
use App\Models\EmployeeReceivable;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeReceivableResource extends Resource
{
    protected static ?string $model = EmployeeReceivable::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Deductions & Receivables';

    protected static ?int $navigationSort = 70;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payroll_period_id'),
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('receivable_id')
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
                Forms\Components\TextInput::make('total_paid'),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_default')
                    ->required(),
                Forms\Components\DatePicker::make('effective_date'),
                Forms\Components\DatePicker::make('received_at'),
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
                Tables\Columns\TextColumn::make('receivable_id'),
                Tables\Columns\TextColumn::make('billing_cycle'),
                Tables\Columns\TextColumn::make('amount'),
                Tables\Columns\TextColumn::make('percentage'),
                Tables\Columns\TextColumn::make('date_from'),
                Tables\Columns\TextColumn::make('date_to'),
                Tables\Columns\TextColumn::make('total_paid'),
                Tables\Columns\TextColumn::make('reason'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_default')
                    ->boolean(),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date(),
                Tables\Columns\TextColumn::make('received_at')
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
            'index' => Pages\ListEmployeeReceivables::route('/'),
            'create' => Pages\CreateEmployeeReceivable::route('/create'),
            'edit' => Pages\EditEmployeeReceivable::route('/{record}/edit'),
        ];
    }    
}
