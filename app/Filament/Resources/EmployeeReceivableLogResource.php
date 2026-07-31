<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeReceivableLogResource\Pages;
use App\Filament\Resources\EmployeeReceivableLogResource\RelationManagers;
use App\Models\EmployeeReceivableLog;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeReceivableLogResource extends Resource
{
    protected static ?string $model = EmployeeReceivableLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs and Trails';

    protected static ?int $navigationSort = 80;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_receivable_id')
                    ->required(),
                Forms\Components\TextInput::make('action_by')
                    ->required(),
                Forms\Components\TextInput::make('action')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\Textarea::make('details')
                    ->maxLength(65535),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_receivable_id'),
                Tables\Columns\TextColumn::make('action_by'),
                Tables\Columns\TextColumn::make('action'),
                Tables\Columns\TextColumn::make('remarks'),
                Tables\Columns\TextColumn::make('details'),
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
            'index' => Pages\ListEmployeeReceivableLogs::route('/'),
            'create' => Pages\CreateEmployeeReceivableLog::route('/create'),
            'edit' => Pages\EditEmployeeReceivableLog::route('/{record}/edit'),
        ];
    }    
}
