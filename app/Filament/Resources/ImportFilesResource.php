<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportFilesResource\Pages;
use App\Filament\Resources\ImportFilesResource\RelationManagers;
use App\Models\ImportFiles;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImportFilesResource extends Resource
{
    protected static ?string $model = ImportFiles::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 20;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('deduction_id'),
                Forms\Components\TextInput::make('receivable_id'),
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('path')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deduction_id'),
                Tables\Columns\TextColumn::make('receivable_id'),
                Tables\Columns\TextColumn::make('file_name'),
                Tables\Columns\TextColumn::make('path'),
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
            'index' => Pages\ListImportFiles::route('/'),
            'create' => Pages\CreateImportFiles::route('/create'),
            'edit' => Pages\EditImportFiles::route('/{record}/edit'),
        ];
    }    
}
