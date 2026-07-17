<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollPeriodResource\Pages;
use App\Filament\Resources\PayrollPeriodResource\RelationManagers;
use App\Models\PayrollPeriod;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollPeriodResource extends Resource
{
    protected static ?string $model = PayrollPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('month')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('employment_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('payroll_type')
                    ->required(),
                Forms\Components\TextInput::make('special_payroll_id'),
                Forms\Components\TextInput::make('source_payroll_period_id'),
                Forms\Components\TextInput::make('period_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('period_start')
                    ->required(),
                Forms\Components\TextInput::make('period_end')
                    ->required(),
                Forms\Components\TextInput::make('days_of_duty')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\DateTimePicker::make('posted_at'),
                Forms\Components\DateTimePicker::make('locked_at'),
                Forms\Components\DateTimePicker::make('last_generated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('employment_type'),
                Tables\Columns\TextColumn::make('payroll_type'),
                Tables\Columns\TextColumn::make('special_payroll_id'),
                Tables\Columns\TextColumn::make('source_payroll_period_id'),
                Tables\Columns\TextColumn::make('period_type'),
                Tables\Columns\TextColumn::make('period_start'),
                Tables\Columns\TextColumn::make('period_end'),
                Tables\Columns\TextColumn::make('days_of_duty'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('posted_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('locked_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('last_generated_at')
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
            'index' => Pages\ListPayrollPeriods::route('/'),
            'create' => Pages\CreatePayrollPeriod::route('/create'),
            'edit' => Pages\EditPayrollPeriod::route('/{record}/edit'),
        ];
    }    
}
