<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeNightDiffComputationResource\Pages;
use App\Filament\Resources\EmployeeNightDiffComputationResource\RelationManagers;
use App\Models\EmployeeNightDiffComputation;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeNightDiffComputationResource extends Resource
{
    protected static ?string $model = EmployeeNightDiffComputation::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 60;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('payroll_period_id')
                    ->required(),
                Forms\Components\TextInput::make('total_night_hours')
                    ->required(),
                Forms\Components\TextInput::make('total_night_amount')
                    ->required(),
                Forms\Components\TextInput::make('hourly_rate')
                    ->required(),
                Forms\Components\TextInput::make('rate_percent')
                    ->required(),
                Forms\Components\Toggle::make('is_finalized')
                    ->required(),
                Forms\Components\DateTimePicker::make('computed_at')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('total_night_hours'),
                Tables\Columns\TextColumn::make('total_night_amount'),
                Tables\Columns\TextColumn::make('hourly_rate'),
                Tables\Columns\TextColumn::make('rate_percent'),
                Tables\Columns\IconColumn::make('is_finalized')
                    ->boolean(),
                Tables\Columns\TextColumn::make('computed_at')
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
            'index' => Pages\ListEmployeeNightDiffComputations::route('/'),
            'create' => Pages\CreateEmployeeNightDiffComputation::route('/create'),
            'edit' => Pages\EditEmployeeNightDiffComputation::route('/{record}/edit'),
        ];
    }    
}
