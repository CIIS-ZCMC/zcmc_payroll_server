<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionLogResource\Pages;
use App\Filament\Resources\TransactionLogResource\RelationManagers;
use App\Models\TransactionLog;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TransactionLogResource extends Resource
{
    protected static ?string $model = TransactionLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs and Trails';

    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Textarea::make('module')
                    ->maxLength(65535),
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('status')
                    ->required()
                    ->maxLength(65535),
                Forms\Components\TextInput::make('ip_address')
                    ->maxLength(255),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\TextInput::make('serverResponse')
                    ->maxLength(255),
                Forms\Components\Textarea::make('affected_entity')
                    ->maxLength(65535),
                Forms\Components\TextInput::make('employee_profile_id'),
                Forms\Components\TextInput::make('employee_number')
                    ->maxLength(255),
                Forms\Components\TextInput::make('name')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('module'),
                Tables\Columns\TextColumn::make('action'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('ip_address'),
                Tables\Columns\TextColumn::make('remarks'),
                Tables\Columns\TextColumn::make('serverResponse'),
                Tables\Columns\TextColumn::make('affected_entity'),
                Tables\Columns\TextColumn::make('employee_profile_id'),
                Tables\Columns\TextColumn::make('employee_number'),
                Tables\Columns\TextColumn::make('name'),
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
            'index' => Pages\ListTransactionLogs::route('/'),
            'create' => Pages\CreateTransactionLog::route('/create'),
            'edit' => Pages\EditTransactionLog::route('/{record}/edit'),
        ];
    }    
}
