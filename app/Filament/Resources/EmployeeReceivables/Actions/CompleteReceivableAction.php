<?php

namespace App\Filament\Resources\EmployeeReceivables\Actions;

use App\Models\EmployeeReceivable;
use App\Services\EmployeeReceivableService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Mark a standing receivable complete. Delegates to the existing
 * {@see EmployeeReceivableService::complete()} which sets status `completed` and
 * `is_active = false`. Hidden once the receivable is already completed.
 */
class CompleteReceivableAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'complete';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Complete')
            ->icon(Heroicon::OutlinedCheckCircle)
            ->color('success')
            ->requiresConfirmation()
            ->visible(fn (EmployeeReceivable $record): bool => $record->status !== 'completed')
            ->action(function (EmployeeReceivable $record): void {
                app(EmployeeReceivableService::class)->complete($record->id);

                Notification::make()
                    ->title('Receivable completed')
                    ->success()
                    ->send();
            });
    }
}
