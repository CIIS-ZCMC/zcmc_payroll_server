<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Filament\Resources\EmployeeReceivables\Actions\CompleteReceivableAction;
use App\Filament\Resources\EmployeeReceivables\Actions\StopReceivableAction;
use App\Filament\Resources\EmployeeReceivables\Schemas\EmployeeReceivableForm;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Manage a single employee's standing receivables from their profile page.
 * Reuses the shared {@see EmployeeReceivableForm} (without the employee select —
 * the owner record supplies the FK) and the shared stop/complete actions.
 */
class ReceivablesRelationManager extends RelationManager
{
    protected static string $relationship = 'receivables';

    protected static ?string $title = 'Receivables';

    public function form(Schema $schema): Schema
    {
        return EmployeeReceivableForm::configure($schema, includeEmployee: false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('#')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('receivable.name')
                    ->label('Receivable')
                    ->searchable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('billing_cycle')
                    ->badge(),
                TextColumn::make('terms.remaining_balance')
                    ->label('Remaining')
                    ->money('PHP')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('status')
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'primary',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                StopReceivableAction::make(),
                CompleteReceivableAction::make(),
            ]);
    }
}
