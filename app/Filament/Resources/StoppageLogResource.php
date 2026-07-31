<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoppageLogResource\Pages;
use App\Filament\Resources\StoppageLogResource\RelationManagers;
use App\Models\StoppageLog;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoppageLogResource extends Resource
{
    protected static ?string $model = StoppageLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs and Trails';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_deduction_id'),
                Forms\Components\TextInput::make('employee_receivable_id'),
                Forms\Components\TextInput::make('action_by')
                    ->required(),
                Forms\Components\TextInput::make('date_from')
                    ->maxLength(255),
                Forms\Components\TextInput::make('date_to')
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('reason')
                    ->maxLength(255),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_deduction_id'),
                Tables\Columns\TextColumn::make('employee_receivable_id'),
                Tables\Columns\TextColumn::make('action_by'),
                Tables\Columns\TextColumn::make('date_from'),
                Tables\Columns\TextColumn::make('date_to'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('reason'),
                Tables\Columns\TextColumn::make('remarks'),
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
            'index' => Pages\ListStoppageLogs::route('/'),
            'create' => Pages\CreateStoppageLog::route('/create'),
            'edit' => Pages\EditStoppageLog::route('/{record}/edit'),
        ];
    }    
}
