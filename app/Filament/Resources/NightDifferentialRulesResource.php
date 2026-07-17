<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NightDifferentialRulesResource\Pages;
use App\Filament\Resources\NightDifferentialRulesResource\RelationManagers;
use App\Models\NightDifferentialRules;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NightDifferentialRulesResource extends Resource
{
    protected static ?string $model = NightDifferentialRules::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Configuration';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employment_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('start_time')
                    ->required(),
                Forms\Components\TextInput::make('end_time')
                    ->required(),
                Forms\Components\TextInput::make('rate_percent')
                    ->required(),
                Forms\Components\DatePicker::make('effective_date')
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employment_type'),
                Tables\Columns\TextColumn::make('start_time'),
                Tables\Columns\TextColumn::make('end_time'),
                Tables\Columns\TextColumn::make('rate_percent'),
                Tables\Columns\TextColumn::make('effective_date')
                    ->date(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
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
            'index' => Pages\ListNightDifferentialRules::route('/'),
            'create' => Pages\CreateNightDifferentialRules::route('/create'),
            'edit' => Pages\EditNightDifferentialRules::route('/{record}/edit'),
        ];
    }    
}
