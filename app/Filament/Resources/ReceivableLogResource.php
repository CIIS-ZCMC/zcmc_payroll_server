<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReceivableLogResource\Pages;
use App\Filament\Resources\ReceivableLogResource\RelationManagers;
use App\Models\ReceivableLog;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ReceivableLogResource extends Resource
{
    protected static ?string $model = ReceivableLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs and Trails';

    protected static ?int $navigationSort = 50;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListReceivableLogs::route('/'),
            'create' => Pages\CreateReceivableLog::route('/create'),
            'edit' => Pages\EditReceivableLog::route('/{record}/edit'),
        ];
    }    
}
