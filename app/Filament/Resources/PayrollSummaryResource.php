<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollSummaryResource\Pages;
use App\Filament\Resources\PayrollSummaryResource\RelationManagers;
use App\Models\PayrollSummary;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PayrollSummaryResource extends Resource
{
    protected static ?string $model = PayrollSummary::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('payroll_period_id'),
                Forms\Components\TextInput::make('generated_by_id')
                    ->required(),
                Forms\Components\TextInput::make('generated_by_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('total_employees')
                    ->required(),
                Forms\Components\TextInput::make('total_deductions')
                    ->required(),
                Forms\Components\TextInput::make('total_receivables')
                    ->required(),
                Forms\Components\TextInput::make('total_gross')
                    ->required(),
                Forms\Components\TextInput::make('total_net')
                    ->required(),
                Forms\Components\TextInput::make('total_night_differential')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('generated_by_id'),
                Tables\Columns\TextColumn::make('generated_by_name'),
                Tables\Columns\TextColumn::make('total_employees'),
                Tables\Columns\TextColumn::make('total_deductions'),
                Tables\Columns\TextColumn::make('total_receivables'),
                Tables\Columns\TextColumn::make('total_gross'),
                Tables\Columns\TextColumn::make('total_net'),
                Tables\Columns\TextColumn::make('total_night_differential'),
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
            'index' => Pages\ListPayrollSummaries::route('/'),
            'create' => Pages\CreatePayrollSummary::route('/create'),
            'edit' => Pages\EditPayrollSummary::route('/{record}/edit'),
        ];
    }    
}
