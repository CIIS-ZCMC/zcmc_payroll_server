<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use App\Filament\Resources\EmployeeDeductions\Actions\CompleteDeductionAction;
use App\Filament\Resources\EmployeeDeductions\Actions\StopDeductionAction;
use App\Filament\Resources\EmployeeDeductions\Schemas\EmployeeDeductionForm;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/**
 * Manage a single employee's standing deductions from their profile page.
 * Reuses the shared {@see EmployeeDeductionForm} (without the employee select —
 * the owner record supplies the FK) and the shared stop/complete actions.
 */
class DeductionsRelationManager extends RelationManager
{
    protected static string $relationship = 'deductions';

    protected static ?string $title = 'Deductions';

    public function form(Schema $schema): Schema
    {
        return EmployeeDeductionForm::configure($schema, includeEmployee: false);
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
                TextColumn::make('deduction.name')
                    ->label('Deduction')
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
                StopDeductionAction::make(),
                CompleteDeductionAction::make(),
            ]);
    }
}
