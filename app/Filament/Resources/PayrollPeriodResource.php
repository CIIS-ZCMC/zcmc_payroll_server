<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PayrollPeriodResource\Pages;
use App\Filament\Resources\PayrollPeriodResource\RelationManagers;
use App\Models\PayrollPeriod;
use App\Services\FetchEmployeeService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Log;

class PayrollPeriodResource extends Resource
{
    protected static ?string $model = PayrollPeriod::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Payroll';

    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('month')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->maxLength(255),
                // Free text here would let someone save "Regular" or "first half",
                // which no import can ever match: FetchEmployeeService looks the
                // period up on these exact values, so a typo leaves a row that
                // stays permanently empty.
                Forms\Components\Select::make('employment_type')
                    ->required()
                    ->options([
                        'regular' => 'Regular',
                        'job_order' => 'Job Order',
                    ]),
                Forms\Components\TextInput::make('payroll_type')
                    ->required(),
                Forms\Components\Select::make('period_type')
                    ->required()
                    ->options([
                        'first_half' => 'First Half',
                        'second_half' => 'Second Half',
                    ]),
                Forms\Components\TextInput::make('period_start')
                    ->required(),
                Forms\Components\TextInput::make('period_end')
                    ->required(),
                Forms\Components\TextInput::make('days_of_duty')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\DateTimePicker::make('posted_at'),
                Forms\Components\DateTimePicker::make('locked_at'),
                Forms\Components\DateTimePicker::make('last_generated_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('employment_type'),
                Tables\Columns\TextColumn::make('payroll_type'),
                Tables\Columns\TextColumn::make('period_type'),
                Tables\Columns\TextColumn::make('period_start'),
                Tables\Columns\TextColumn::make('period_end'),
                Tables\Columns\TextColumn::make('days_of_duty'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('posted_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('locked_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('last_generated_at')
                    ->dateTime(),
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
                Tables\Actions\Action::make('importFromCache')
                    ->label('Import from cache')
                    ->icon('heroicon-o-download')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Import employee records from UMIS cache')
                    // Both consequences are easy to miss from the table: existing
                    // rows are rewritten, and the active period moves here.
                    ->modalSubheading(fn (PayrollPeriod $record): string => 'This re-imports '
                        . date('F', mktime(0, 0, 0, (int) $record->month, 1)) . " {$record->year} "
                        . "({$record->employment_type}, {$record->period_type}), overwriting the "
                        . 'employee records already stored for it, and makes it the active payroll '
                        . 'period. It takes around half a minute.')
                    ->modalButton('Import now')
                    ->action(fn (PayrollPeriod $record) => static::importFromCache(
                        (int) $record->year,
                        (int) $record->month,
                        $record->employment_type,
                        $record->period_type,
                    )),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    /**
     * Imports one period's employee records straight from the UMIS Redis cache.
     *
     * This is the same path as command:fetch-employee-time-records and the UMIS
     * webhook — FetchEmployeeService owns the writes, so the three entry points
     * can never drift apart. Nothing is fetched over the network here: the cache
     * must already hold the period, which "Request rebuild from UMIS" arranges.
     *
     * Sends its own notification and reports whether the import ran, so callers
     * only have to decide what to do with the boolean.
     */
    public static function importFromCache(
        int $year,
        int $month,
        string $employmentType,
        string $periodType
    ): bool {
        // A period is ~1,900 employees and takes ~25s; the CLI runs unlimited
        // but a web worker may not, so lift the ceiling for this request only.
        @set_time_limit(600);

        $service = app(FetchEmployeeService::class);
        $label = date('F', mktime(0, 0, 0, $month, 1)) . " {$year} ({$employmentType}, {$periodType})";
        $context = compact('year', 'month', 'employmentType', 'periodType');

        // Checked up front so a missing cache reads as "nothing to import"
        // rather than the generic failure getEmployeesForPeriod returns for
        // every problem — the two need different fixes.
        if (! $service->hasCacheForPeriod($year, $month, $employmentType, $periodType)) {
            Notification::make()
                ->title('Nothing cached for this period')
                ->body("UMIS holds no data for {$label}. Use \"Request rebuild from UMIS\" first, "
                    . 'then import once it reports back.')
                ->warning()
                ->persistent()
                ->send();

            return false;
        }

        try {
            $result = $service->getEmployeesForPeriod($year, $month, $employmentType, $periodType);
        } catch (\Throwable $th) {
            Log::error('Admin cache import failed: ' . $th->getMessage(), $context);

            Notification::make()
                ->title('Import failed')
                ->body($th->getMessage())
                ->danger()
                ->persistent()
                ->send();

            return false;
        }

        // getEmployeesForPeriod swallows its own exceptions and returns null, so
        // this covers both an unreadable payload and a rolled-back transaction.
        if ($result === null) {
            Notification::make()
                ->title('Import did not complete')
                ->body("Nothing was imported for {$label}. See storage/logs/laravel.log for the reason.")
                ->danger()
                ->persistent()
                ->send();

            return false;
        }

        Notification::make()
            ->title('Import complete')
            ->body(count($result) . " employees imported for {$label}. "
                . 'This is now the active payroll period.')
            ->success()
            ->send();

        return true;
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
            'index' => Pages\ListPayrollPeriods::route('/'),
            'create' => Pages\CreatePayrollPeriod::route('/create'),
            'edit' => Pages\EditPayrollPeriod::route('/{record}/edit'),
        ];
    }    
}
