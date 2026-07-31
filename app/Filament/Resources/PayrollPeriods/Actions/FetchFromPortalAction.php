<?php

namespace App\Filament\Resources\PayrollPeriods\Actions;

use App\Exceptions\PortalCacheMissing;
use App\Services\Fetch\PayrollPortalSyncService;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Header action on the Payroll Periods list page that pulls a period's data
 * from the UMIS portal Redis cache into the payroll tables.
 */
class FetchFromPortalAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'fetchFromPortal';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Fetch from portal')
            ->icon(Heroicon::OutlinedCloudArrowDown)
            ->modalSubmitActionLabel('Fetch')
            ->schema([
                TextInput::make('year')
                    ->numeric()
                    ->default((int) date('Y'))
                    ->required(),
                TextInput::make('month')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(12)
                    ->default((int) date('n'))
                    ->required(),
                Select::make('employment_type')
                    ->options(['regular' => 'Regular', 'job-order' => 'Job order'])
                    ->default('regular')
                    ->required(),
                Select::make('period_type')
                    ->options(['first_half' => 'First half', 'second_half' => 'Second half'])
                    ->default('first_half')
                    ->required(),
            ])
            ->action(function (array $data): void {
                try {
                    $counts = app(PayrollPortalSyncService::class)->sync(
                        (string) $data['year'],
                        (string) $data['month'],
                        (string) $data['employment_type'],
                        (string) $data['period_type'],
                    );
                } catch (PortalCacheMissing $e) {
                    Notification::make()
                        ->title('Nothing to fetch')
                        ->body($e->getMessage())
                        ->warning()
                        ->send();

                    return;
                }

                Notification::make()
                    ->title('Payroll data fetched')
                    ->body(sprintf(
                        '%d employees, %d time records, %d salaries, %d computed, %d exclusions.',
                        $counts['employees'],
                        $counts['time_records'],
                        $counts['salaries'],
                        $counts['computed_salaries'],
                        $counts['exclusions'],
                    ))
                    ->success()
                    ->send();
            });
    }
}
