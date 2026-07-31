<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoginTrailResource\Pages;
use App\Filament\Resources\LoginTrailResource\RelationManagers;
use App\Models\LoginTrail;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LoginTrailResource extends Resource
{
    protected static ?string $model = LoginTrail::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs and Trails';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('action_by')
                    ->required(),
                Forms\Components\TextInput::make('module_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('methods')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('description')
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
                Tables\Columns\TextColumn::make('module_name'),
                Tables\Columns\TextColumn::make('methods'),
                Tables\Columns\TextColumn::make('description'),
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
            'index' => Pages\ListLoginTrails::route('/'),
            'create' => Pages\CreateLoginTrail::route('/create'),
            'edit' => Pages\EditLoginTrail::route('/{record}/edit'),
        ];
    }    
}
