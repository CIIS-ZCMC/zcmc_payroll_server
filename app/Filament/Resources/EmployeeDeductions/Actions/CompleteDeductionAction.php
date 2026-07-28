<?php

namespace App\Filament\Resources\EmployeeDeductions\Actions;

use App\Models\EmployeeDeduction;
use App\Services\EmployeeDeductionService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

/**
 * Mark a standing deduction complete. Delegates to the existing
 * {@see EmployeeDeductionService::complete()} which sets status `completed` and
 * `is_active = false`. Hidden once the deduction is already completed.
 */
class CompleteDeductionAction extends Action
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
            ->visible(fn (EmployeeDeduction $record): bool => $record->status !== 'completed')
            ->action(function (EmployeeDeduction $record): void {
                app(EmployeeDeductionService::class)->complete($record->id);

                Notification::make()
                    ->title('Deduction completed')
                    ->success()
                    ->send();
            });
    }
}
