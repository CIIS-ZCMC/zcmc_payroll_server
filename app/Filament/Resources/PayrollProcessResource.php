<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollProcessResource\Pages;
use App\Filament\Resources\PayrollProcessResource\RelationManagers;
use App\Models\PayrollProcess;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollProcessResource extends Resource
{
    protected static ?string $model = PayrollProcess::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payroll_period_id'),
                Forms\Components\TextInput::make('payroll_type')
                    ->required(),
                Forms\Components\TextInput::make('current_step')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('started_by')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DateTimePicker::make('started_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('payroll_type'),
                Tables\Columns\TextColumn::make('current_step'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('started_by'),
                Tables\Columns\TextColumn::make('started_at')
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
            'index' => Pages\ListPayrollProcesses::route('/'),
            'create' => Pages\CreatePayrollProcess::route('/create'),
            'edit' => Pages\EditPayrollProcess::route('/{record}/edit'),
        ];
    }    
}
