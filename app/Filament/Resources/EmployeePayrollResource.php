<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeePayrollResource\Pages;
use App\Filament\Resources\EmployeePayrollResource\RelationManagers;
use App\Models\EmployeePayroll;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeePayrollResource extends Resource
{
    protected static ?string $model = EmployeePayroll::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('employee_time_record_id')
                    ->required(),
                Forms\Components\TextInput::make('payroll_period_id'),
                Forms\Components\Toggle::make('month')
                    ->required(),
                Forms\Components\TextInput::make('year')
                    ->required(),
                Forms\Components\TextInput::make('basic_pay')
                    ->required(),
                Forms\Components\TextInput::make('total_receivables')
                    ->required(),
                Forms\Components\TextInput::make('gross_pay')
                    ->required(),
                Forms\Components\TextInput::make('total_deductions')
                    ->required(),
                Forms\Components\TextInput::make('net_pay')
                    ->required(),
                Forms\Components\TextInput::make('first_half'),
                Forms\Components\TextInput::make('second_half'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('employee_time_record_id'),
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\IconColumn::make('month')
                    ->boolean(),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('basic_pay'),
                Tables\Columns\TextColumn::make('total_receivables'),
                Tables\Columns\TextColumn::make('gross_pay'),
                Tables\Columns\TextColumn::make('total_deductions'),
                Tables\Columns\TextColumn::make('net_pay'),
                Tables\Columns\TextColumn::make('first_half'),
                Tables\Columns\TextColumn::make('second_half'),
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
            'index' => Pages\ListEmployeePayrolls::route('/'),
            'create' => Pages\CreateEmployeePayroll::route('/create'),
            'edit' => Pages\EditEmployeePayroll::route('/{record}/edit'),
        ];
    }    
}
