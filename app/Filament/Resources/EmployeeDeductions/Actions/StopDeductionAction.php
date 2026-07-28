<?php

namespace App\Filament\Resources\EmployeeDeductions\Actions;

use App\Models\EmployeeDeduction;
use App\Services\EmployeeDeductionService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Suspend a standing deduction. Delegates to the existing
 * {@see EmployeeDeductionService::stop()} which sets status `suspended`,
 * `is_active = false`, and stamps `stopped_at`. Hidden once the deduction is
 * already inactive.
 */
class StopDeductionAction extends Action
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
            ->visible(fn (EmployeeDeduction $record): bool => (bool) $record->is_active)
            ->action(function (EmployeeDeduction $record): void {
                app(EmployeeDeductionService::class)->stop($record->id);

                Notification::make()
                    ->title('Deduction stopped')
                    ->success()
                    ->send();
            });
    }
}
