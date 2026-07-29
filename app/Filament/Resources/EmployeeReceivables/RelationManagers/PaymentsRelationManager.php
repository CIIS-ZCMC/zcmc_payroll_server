<?php

namespace App\Filament\Resources\EmployeeReceivables\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Read-only history of receivables collected per payroll run.
 */
class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';

    protected static ?string $title = 'Payment history';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('received_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('term_no')
                    ->label('Term #')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('payroll_run_id')
                    ->label('Payroll run')
                    ->sortable(),
            ])
            ->defaultSort('received_at', 'desc')
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([]);
    }
}
