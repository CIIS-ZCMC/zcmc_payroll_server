<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeReceivableTrailResource\Pages;
use App\Filament\Resources\EmployeeReceivableTrailResource\RelationManagers;
use App\Models\EmployeeReceivableTrail;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeReceivableTrailResource extends Resource
{
    protected static ?string $model = EmployeeReceivableTrail::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Logs and Trails';

    protected static ?int $navigationSort = 90;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_receivable_id')
                    ->required(),
                Forms\Components\TextInput::make('total_term')
                    ->required(),
                Forms\Components\TextInput::make('total_term_received')
                    ->required(),
                Forms\Components\TextInput::make('amount_received')
                    ->required(),
                Forms\Components\TextInput::make('balance')
                    ->required(),
                Forms\Components\DatePicker::make('date_received')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('remarks')
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_last_received')
                    ->required(),
                Forms\Components\Toggle::make('is_adjustment')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_receivable_id'),
                Tables\Columns\TextColumn::make('total_term'),
                Tables\Columns\TextColumn::make('total_term_received'),
                Tables\Columns\TextColumn::make('amount_received'),
                Tables\Columns\TextColumn::make('balance'),
                Tables\Columns\TextColumn::make('date_received')
                    ->date(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('remarks'),
                Tables\Columns\IconColumn::make('is_last_received')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_adjustment')
                    ->boolean(),
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
            'index' => Pages\ListEmployeeReceivableTrails::route('/'),
            'create' => Pages\CreateEmployeeReceivableTrail::route('/create'),
            'edit' => Pages\EditEmployeeReceivableTrail::route('/{record}/edit'),
        ];
    }    
}
