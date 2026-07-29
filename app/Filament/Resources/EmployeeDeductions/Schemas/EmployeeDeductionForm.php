<?php

namespace App\Filament\Resources\EmployeeDeductions\Schemas;

use App\Models\Deduction;
use App\Models\Employee;
use App\Models\EmployeeDeduction;
use App\Models\PayrollPeriod;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EmployeeDeductionForm
{
    /**
     * @param  bool  $includeEmployee  Whether to render the employee select. The
     *                                 employee relation manager sets the FK
     *                                 automatically, so it passes false there.
     */
    public static function configure(Schema $schema, bool $includeEmployee = true): Schema
    {
        return $schema
            ->components([
                Select::make('employee_id')
                    ->label('Employee')
                    ->options(fn (): array => static::employeeOptions())
                    ->searchable()
                    ->required()
                    ->visible($includeEmployee),
                Select::make('deduction_id')
                    ->label('Deduction')
                    ->options(fn (): array => Deduction::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->required(),
                Select::make('payroll_period_id')
                    ->label('Payroll period')
                    ->options(fn (): array => static::payrollPeriodOptions())
                    ->searchable()
                    ->helperText('Optional. Leave blank for an open-ended standing deduction.'),
                TextInput::make('amount')
                    ->numeric()
                    ->prefix('₱')
                    ->required(),
                TextInput::make('percentage')
                    ->numeric()
                    ->suffix('%')
                    ->helperText('Optional. When set, the amount is derived from base salary during computation.'),
                Select::make('billing_cycle')
                    ->options([
                        'monthly' => 'Monthly',
                        'semi-monthly' => 'Semi-monthly',
                        'quarterly' => 'Quarterly',
                        'annually' => 'Annually',
                    ])
                    ->default('monthly')
                    ->required(),
                DatePicker::make('effective_date')
                    ->required(),
                DatePicker::make('end_date'),
                Select::make('status')
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                        'completed' => 'Completed',
                        'suspended' => 'Suspended',
                    ])
                    ->default('active')
                    ->required(),
                Toggle::make('is_active')
                    ->default(true),
                Toggle::make('is_default'),
                Textarea::make('remarks')
                    ->columnSpanFull(),
                Section::make('Terms & balance')
                    ->schema([
                        Placeholder::make('terms_total')
                            ->label('Total terms')
                            ->content(fn (?EmployeeDeduction $record): string => (string) ($record?->terms?->total_terms ?? '—')),
                        Placeholder::make('terms_paid')
                            ->label('Paid terms')
                            ->content(fn (?EmployeeDeduction $record): string => (string) ($record?->terms?->paid_terms ?? '—')),
                        Placeholder::make('terms_amount')
                            ->label('Term amount')
                            ->content(fn (?EmployeeDeduction $record): string => static::money($record?->terms?->term_amount)),
                        Placeholder::make('terms_total_amount')
                            ->label('Total amount')
                            ->content(fn (?EmployeeDeduction $record): string => static::money($record?->terms?->total_amount)),
                        Placeholder::make('terms_remaining')
                            ->label('Remaining balance')
                            ->content(fn (?EmployeeDeduction $record): string => static::money($record?->terms?->remaining_balance)),
                        Placeholder::make('terms_completed_at')
                            ->label('Completed at')
                            ->content(fn (?EmployeeDeduction $record): string => (string) ($record?->terms?->completed_at ?? '—')),
                    ])
                    ->columns(3)
                    ->hidden(fn (?EmployeeDeduction $record): bool => $record === null),
            ]);
    }

    /**
     * @return array<int, string>
     */
    protected static function employeeOptions(): array
    {
        return Employee::query()
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->mapWithKeys(fn (Employee $employee): array => [
                $employee->id => trim("{$employee->employee_number} — {$employee->last_name}, {$employee->first_name}"),
            ])
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected static function payrollPeriodOptions(): array
    {
        return PayrollPeriod::query()
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->mapWithKeys(fn (PayrollPeriod $period): array => [
                $period->id => trim(sprintf(
                    '%s/%s — %s (%s)',
                    $period->month,
                    $period->year,
                    $period->employment_type,
                    $period->payroll_type,
                )),
            ])
            ->all();
    }

    protected static function money(mixed $value): string
    {
        return $value === null ? '—' : '₱'.number_format((float) $value, 2);
    }
}
