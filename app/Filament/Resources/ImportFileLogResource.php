<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ImportFileLogResource\Pages;
use App\Filament\Resources\ImportFileLogResource\RelationManagers;
use App\Models\ImportFileLog;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ImportFileLogResource extends Resource
{
    protected static ?string $model = ImportFileLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Imports';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('deduction_id'),
                Forms\Components\TextInput::make('receivable_id'),
                Forms\Components\TextInput::make('file_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('employment_type')
                    ->required()
                    ->maxLength(255),
                Forms\Components\DatePicker::make('payroll_date')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deduction_id'),
                Tables\Columns\TextColumn::make('receivable_id'),
                Tables\Columns\TextColumn::make('file_name'),
                Tables\Columns\TextColumn::make('employment_type'),
                Tables\Columns\TextColumn::make('payroll_date')
                    ->date(),
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
            'index' => Pages\ListImportFileLogs::route('/'),
            'create' => Pages\CreateImportFileLog::route('/create'),
            'edit' => Pages\EditImportFileLog::route('/{record}/edit'),
        ];
    }    
}
