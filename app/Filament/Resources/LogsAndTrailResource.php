<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LogsAndTrailResource\Pages;
use App\Filament\Resources\LogsAndTrailResource\RelationManagers;
use App\Models\LogsAndTrail;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogsAndTrailResource extends Resource
{
    protected static ?string $model = LogsAndTrail::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs & Trails';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action_by')
                    ->required(),
                Forms\Components\TextInput::make('module')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('action_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_table')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reference_id')
                    ->required(),
                Forms\Components\TextInput::make('changes')
                    ->required(),
                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('ip_address')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('action_by'),
                Tables\Columns\TextColumn::make('module'),
                Tables\Columns\TextColumn::make('action_type'),
                Tables\Columns\TextColumn::make('reference_table'),
                Tables\Columns\TextColumn::make('reference_id'),
                Tables\Columns\TextColumn::make('changes'),
                Tables\Columns\TextColumn::make('description'),
                Tables\Columns\TextColumn::make('ip_address'),
                Tables\Columns\TextColumn::make('status'),
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
            'index' => Pages\ListLogsAndTrails::route('/'),
            'create' => Pages\CreateLogsAndTrail::route('/create'),
            'edit' => Pages\EditLogsAndTrail::route('/{record}/edit'),
        ];
    }    
}
