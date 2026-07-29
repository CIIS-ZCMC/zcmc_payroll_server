<?php

namespace App\Filament\Resources\EmployeeReceivables\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Read-only audit trail of actions taken on the receivable.
 */
class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected static ?string $title = 'Activity log';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('action')
                    ->badge(),
                TextColumn::make('remarks')
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('action_by_id')
                    ->label('By')
                    ->placeholder('System'),
            ])
            ->defaultSort('created_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
