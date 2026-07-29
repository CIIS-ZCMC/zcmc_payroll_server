<?php

namespace App\Filament\Resources\EmployeeDeductions\Tables;

use App\Filament\Resources\EmployeeDeductions\Actions\CompleteDeductionAction;
use App\Filament\Resources\EmployeeDeductions\Actions\StopDeductionAction;
use App\Models\Deduction;
use App\Models\PayrollPeriod;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmployeeDeductionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.employee_number')
                    ->label('Emp. No.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.last_name')
                    ->label('Employee')
                    ->formatStateUsing(fn ($state, $record): string => trim("{$record->employee?->last_name}, {$record->employee?->first_name}"))
                    ->searchable(),
                TextColumn::make('deduction.name')
                    ->label('Deduction')
                    ->searchable(),
                TextColumn::make('deduction.code')
                    ->label('Code')
                    ->toggleable(),
                TextColumn::make('amount')
                    ->money('PHP')
                    ->sortable(),
                TextColumn::make('billing_cycle')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'completed' => 'primary',
                        'suspended' => 'danger',
                        default => 'gray',
                    }),
                IconColumn::make('is_active')
                    ->boolean(),
                TextColumn::make('effective_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('terms.remaining_balance')
                    ->label('Remaining')
                    ->money('PHP')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'completed' => 'Completed',
                        'suspended' => 'Suspended',
                    ]),
                SelectFilter::make('deduction_id')
                    ->label('Deduction')
                    ->options(fn (): array => Deduction::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable(),
                SelectFilter::make('payroll_period_id')
                    ->label('Payroll period')
                    ->options(fn (): array => PayrollPeriod::query()
                        ->orderByDesc('year')
                        ->orderByDesc('month')
                        ->get()
                        ->mapWithKeys(fn (PayrollPeriod $period): array => [
                            $period->id => "{$period->month}/{$period->year}",
                        ])
                        ->all()),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                EditAction::make(),
                StopDeductionAction::make(),
                CompleteDeductionAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
