<?php

namespace App\Filament\Resources\PayrollPeriodResource\Pages;

use App\Filament\Resources\PayrollPeriodResource;
use App\Services\FetchEmployeeService;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Log;

class ListPayrollPeriods extends ListRecords
{
    protected static string $resource = PayrollPeriodResource::class;

    protected function getActions(): array
    {
        return [
            $this->importFromCacheAction(),
            $this->requestUmisRebuildAction(),
            Actions\CreateAction::make(),
        ];
    }

    /**
     * Imports a period from the UMIS Redis cache without waiting on a webhook.
     *
     * The counterpart to "Request rebuild from UMIS": that one asks UMIS to
     * build the cache, this one pulls what is already in it. Use it to backfill
     * past periods, or to recover a webhook that never arrived — it is the
     * command:fetch-employee-time-records manual mode, in the browser.
     *
     * The period row does not have to exist yet; the import creates it.
     */
    protected function importFromCacheAction(): Actions\Action
    {
        return Actions\Action::make('importFromCache')
            ->label('Import from cache')
            ->icon('heroicon-o-download')
            ->color('success')
            ->modalHeading('Import employee records from UMIS cache')
            ->modalSubheading('Reads a period that UMIS has already cached and imports its employee '
                . 'records. The period becomes the active payroll period, deactivating the others. '
                . 'Around half a minute per period — keep this tab open until it finishes.')
            ->modalButton('Import now')
            ->form([
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2030)
                    ->default(now()->year),
                Forms\Components\Select::make('month')
                    ->required()
                    ->options(collect(range(1, 12))->mapWithKeys(fn ($m) => [
                        $m => date('F', mktime(0, 0, 0, $m, 1)),
                    ])->all())
                    ->default(now()->month),
                Forms\Components\Select::make('employment_type')
                    ->required()
                    ->options([
                        'regular' => 'Regular',
                        'job_order' => 'Job Order',
                    ])
                    ->default('regular'),
                Forms\Components\Select::make('period_type')
                    ->required()
                    ->options([
                        'first_half' => 'First Half',
                        'second_half' => 'Second Half',
                    ])
                    ->default('first_half'),
            ])
            ->action(function (array $data): void {
                PayrollPeriodResource::importFromCache(
                    (int) $data['year'],
                    (int) $data['month'],
                    $data['employment_type'],
                    $data['period_type'],
                );
            });
    }

    /**
     * Asks UMIS to rebuild a period's time record cache.
     *
     * Only the request is made here — it returns as soon as UMIS has queued the
     * build. The records arrive later, on their own, when UMIS calls this
     * server's api/umis/time-records-cached webhook. Nothing needs to be
     * clicked a second time.
     */
    protected function requestUmisRebuildAction(): Actions\Action
    {
        return Actions\Action::make('requestUmisRebuild')
            ->label('Request rebuild from UMIS')
            ->icon('heroicon-o-refresh')
            ->color('secondary')
            ->modalHeading('Request rebuild from UMIS')
            ->modalSubheading('UMIS builds the period in the background, which takes several minutes. '
                . 'When it finishes it notifies this server and the records import automatically — '
                . 'and that period becomes the active payroll period, deactivating the others.')
            ->modalButton('Request rebuild')
            ->form([
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->numeric()
                    ->minValue(2020)
                    ->maxValue(2030)
                    ->default(now()->year),
                Forms\Components\Select::make('month')
                    ->required()
                    ->options(collect(range(1, 12))->mapWithKeys(fn ($m) => [
                        $m => date('F', mktime(0, 0, 0, $m, 1)),
                    ])->all())
                    ->default(now()->month),
                Forms\Components\Select::make('employment_type')
                    ->required()
                    ->options([
                        'regular' => 'Regular',
                        'job_order' => 'Job Order',
                    ])
                    ->default('regular'),
                Forms\Components\Select::make('period_type')
                    ->required()
                    ->options([
                        'first_half' => 'First Half',
                        'second_half' => 'Second Half',
                    ])
                    ->default('first_half'),
            ])
            ->action(function (array $data): void {
                $year = (int) $data['year'];
                $month = (int) $data['month'];

                try {
                    $response = app(FetchEmployeeService::class)->triggerUmisCache(
                        $year,
                        $month,
                        $data['employment_type'],
                        $data['period_type']
                    );
                } catch (\Throwable $th) {
                    // Guzzle throws on any non-2xx, so a rejected API key or an
                    // unreachable UMIS lands here rather than in the branch below.
                    Log::error('UMIS rebuild request failed: ' . $th->getMessage(), $data);

                    Notification::make()
                        ->title('Could not reach UMIS')
                        ->body($th->getMessage())
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                if ($response['is_failed'] ?? false) {
                    Notification::make()
                        ->title('UMIS rejected the request')
                        ->body($response['message'] ?? 'No reason given.')
                        ->danger()
                        ->persistent()
                        ->send();

                    return;
                }

                // UMIS answers 'queued' for a fresh build and 'already_in_progress'
                // when one is running; both mean a webhook is coming.
                $status = $response['data']['status'] ?? 'queued';

                Notification::make()
                    ->title($status === 'already_in_progress'
                        ? 'A build is already running'
                        : 'Rebuild requested')
                    ->body('UMIS is building ' . date('F', mktime(0, 0, 0, $month, 1)) . " {$year} "
                        . "({$data['employment_type']}, {$data['period_type']}). "
                        . 'The records will import automatically when it finishes.')
                    ->success()
                    ->send();
            });
    }
}
