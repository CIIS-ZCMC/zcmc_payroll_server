<?php

namespace App\Filament\Resources\Employees\RelationManagers;

use BackedEnum;
use App\Models\EmployeeTimeRecord;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;

class TimeRecordsRelationManager extends RelationManager
{
    protected static string $relationship = 'timeRecords';

    protected static string|BackedEnum|null $icon = Heroicon::OutlinedClock;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('#')
                    ->label('No.')
                    ->rowIndex(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->formatStateUsing(fn($state) => $state ? 'Active' : 'Inactive'),
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('period.id')
                    ->label('Payroll Period')
                    ->formatStateUsing(function ($state, EmployeeTimeRecord $record): ?string {
                        $period = $record->period;
                        if (! $period) {
                            return null;
                        }

                        $monthName = is_numeric($period->month)
                            ? date('F', mktime(0, 0, 0, (int) $period->month, 10))
                            : $period->month;
                        $periodType = str($period->period_type)->replace('_', ' ')->title();

                        return "{$period->year} {$monthName} ({$periodType})";
                    })
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('period', function (Builder $q) use ($search): void {
                            $q->where(function (Builder $sub) use ($search): void {
                                $normalizedSearch = trim($search);
                                $searchUnderscore = str_replace(' ', '_', $normalizedSearch);

                                $sub->where('year', 'like', "%{$normalizedSearch}%")
                                    ->orWhere('month', 'like', "%{$normalizedSearch}%")
                                    ->orWhere('period_type', 'like', "%{$searchUnderscore}%");

                                $parsedMonth = date_parse($normalizedSearch)['month'] ?? null;
                                if (is_int($parsedMonth)) {
                                    $sub->orWhere('month', $parsedMonth);
                                }
                            });
                        });
                    }),
                TextColumn::make('total_working_minutes')
                    ->label('Total Working Minutes')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_working_minutes_with_leave')
                    ->label('Total Working Minutes (with Leave)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_working_hours_with_leave')
                    ->label('Total Working Hours (with Leave)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_working_hours')
                    ->label('Total Working Hours')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_overtime_minutes')
                    ->label('Total Overtime (Mins)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_undertime_minutes')
                    ->label('Total Undertime (Mins)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_official_business_minutes')
                    ->label('Total Official Business (Mins)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_official_time_minutes')
                    ->label('Total Official Time (Mins)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_leave_minutes')
                    ->label('Total Leave (Mins)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('total_night_duty_hours')
                    ->label('Total Night Duty (Hours)')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_of_present_days_with_leave')
                    ->label('Present Days (with Leave)')
                    ->numeric(),
                TextColumn::make('no_of_present_days')
                    ->label('Present Days')
                    ->numeric(),
                TextColumn::make('no_of_leave_wo_pay')
                    ->label('Leave Without Pay')
                    ->numeric(),
                TextColumn::make('no_of_leave_w_pay')
                    ->label('Leave With Pay')
                    ->numeric(),
                TextColumn::make('no_of_absences')
                    ->label('Absences')
                    ->numeric(),
                TextColumn::make('no_of_invalid_entry')
                    ->label('Invalid Entry')
                    ->numeric(),
                TextColumn::make('no_of_day_off')
                    ->label('Day Off')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('no_of_schedule')
                    ->label('Schedule')
                    ->numeric(),
                TextColumn::make('night_duties')
                    ->label('Night Duties')
                    ->numeric()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('absent_dates')
                    ->label('Absent Dates')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Status')
                    ->searchable(),
                TextColumn::make('locked_at')
                    ->label('Locked At')
                    ->dateTime(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
                AssociateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DissociateAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DissociateBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
