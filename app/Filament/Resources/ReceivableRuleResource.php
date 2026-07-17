<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceivableRuleResource\Pages;
use App\Filament\Resources\ReceivableRuleResource\RelationManagers;
use App\Models\ReceivableRule;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceivableRuleResource extends Resource
{
    protected static ?string $model = ReceivableRule::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Deductions & Receivables';

    protected static ?int $navigationSort = 60;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('receivable_id')
                    ->required(),
                Forms\Components\TextInput::make('min_salary'),
                Forms\Components\TextInput::make('max_salary'),
                Forms\Components\TextInput::make('apply_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('value')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('date_start'),
                Forms\Components\DatePicker::make('date_end'),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('receivable_id'),
                Tables\Columns\TextColumn::make('min_salary'),
                Tables\Columns\TextColumn::make('max_salary'),
                Tables\Columns\TextColumn::make('apply_type'),
                Tables\Columns\TextColumn::make('value'),
                Tables\Columns\TextColumn::make('date_start')
                    ->date(),
                Tables\Columns\TextColumn::make('date_end')
                    ->date(),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListReceivableRules::route('/'),
            'create' => Pages\CreateReceivableRule::route('/create'),
            'edit' => Pages\EditReceivableRule::route('/{record}/edit'),
        ];
    }    
}
