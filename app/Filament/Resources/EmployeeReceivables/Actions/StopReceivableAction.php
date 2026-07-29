<?php

namespace App\Filament\Resources\EmployeeReceivables\Actions;

use App\Models\EmployeeReceivable;
use App\Services\EmployeeReceivableService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Suspend a standing receivable. Delegates to the existing
 * {@see EmployeeReceivableService::stop()} which sets status `suspended`,
 * `is_active = false`, and stamps `stopped_at`. Hidden once the receivable is
 * already inactive.
 */
class StopReceivableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'stop';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Stop')
            ->icon(Heroicon::OutlinedNoSymbol)
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (EmployeeReceivable $record): bool => (bool) $record->is_active)
            ->action(function (EmployeeReceivable $record): void {
                app(EmployeeReceivableService::class)->stop($record->id);

                Notification::make()
                    ->title('Receivable stopped')
                    ->success()
                    ->send();
            });
    }
}
