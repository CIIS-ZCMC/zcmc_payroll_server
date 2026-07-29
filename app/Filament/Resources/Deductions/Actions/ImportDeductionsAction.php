<?php

namespace App\Filament\Resources\Deductions\Actions;

use App\Models\Deduction;
use App\Models\PayrollPeriod;
use App\Services\BulkEmployeeDeductionService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Filament header action that imports employee deductions from a CSV file.
 *
 * A two-step wizard modal: the operator uploads the file (with optional
 * deduction / payroll-period overrides), reviews the parsed preview — matched
 * rows and per-row errors — then confirms the import. All parsing and
 * persistence is delegated to {@see BulkEmployeeDeductionService}; this action
 * only wires the panel UI to that service.
 */
class ImportDeductionsAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importDeductions';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Deductions')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->modalWidth('3xl')
            ->modalSubmitActionLabel('Import')
            ->steps([
                Step::make('Upload')
                    ->description('Choose a deduction CSV file')
                    ->schema([
                        FileUpload::make('file')
                            ->label('CSV file')
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->storeFiles(false)
                            ->required()
                            ->live(),
                        Select::make('deduction_id')
                            ->label('Deduction')
                            ->options(fn (): array => Deduction::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->helperText("Leave blank to resolve from the file's Code cell."),
                        Select::make('payroll_period_id')
                            ->label('Payroll period')
                            ->options(fn (): array => static::payrollPeriodOptions())
                            ->searchable()
                            ->helperText('Optional. Scope the imported deductions to a payroll period.'),
                    ]),
                Step::make('Review')
                    ->description('Confirm the matched rows before importing')
                    ->schema([
                        Placeholder::make('preview')
                            ->label('Preview')
                            ->content(fn (Get $get): HtmlString => static::renderPreview($get)),
                    ]),
            ])
            ->action(function (array $data): void {
                $contents = static::readContents($data['file'] ?? null);

                if ($contents === null) {
                    Notification::make()
                        ->title('No file uploaded')
                        ->danger()
                        ->send();

                    return;
                }

                $result = app(BulkEmployeeDeductionService::class)->import(
                    $contents,
                    $data['deduction_id'] ?? null,
                    $data['payroll_period_id'] ?? null,
                );

                static::sendResultNotification($result);
            });
    }

    /**
     * Build the payroll-period select options, most recent first.
     *
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

    /**
     * Render the review-step preview from a no-persist {@see BulkEmployeeDeductionService::parse()} pass.
     */
    protected static function renderPreview(Get $get): HtmlString
    {
        $contents = static::readContents($get('file'));

        if ($contents === null) {
            return new HtmlString('<p class="text-sm text-gray-500">Upload a file on the previous step to see a preview.</p>');
        }

        $parsed = app(BulkEmployeeDeductionService::class)->parse(
            $contents,
            $get('deduction_id') ?: null,
        );

        $lines = [];

        if ($parsed['deduction'] === null) {
            $lines[] = '<p class="text-sm font-semibold text-danger-600">Unknown deduction — could not resolve a code from the file. Select a deduction on the previous step.</p>';
        } else {
            $lines[] = sprintf(
                '<p class="text-sm"><span class="font-semibold">Deduction:</span> %s (%s)</p>',
                e($parsed['deduction']['name']),
                e($parsed['deduction']['code']),
            );
        }

        $period = $parsed['period'];
        if ($period['month'] && $period['year']) {
            $lines[] = sprintf('<p class="text-sm"><span class="font-semibold">Effective period:</span> %d/%d</p>', $period['month'], $period['year']);
        }

        $lines[] = sprintf(
            '<p class="text-sm"><span class="font-semibold">Matched rows:</span> %d</p>',
            count($parsed['rows']),
        );

        if ($parsed['errors'] !== []) {
            $items = array_map(static function (array $error): string {
                if (($error['error'] ?? null) === 'employee_not_found') {
                    return sprintf('Row %s: employee %s not found', e((string) ($error['row'] ?? '?')), e((string) ($error['employee_number'] ?? '')));
                }

                if (($error['error'] ?? null) === 'unknown_deduction_code') {
                    return sprintf('Unknown deduction code: %s', e((string) ($error['value'] ?? '')));
                }

                return e((string) ($error['error'] ?? 'error'));
            }, $parsed['errors']);

            $lines[] = sprintf(
                '<p class="mt-2 text-sm font-semibold text-warning-600">%d row(s) will be skipped:</p><ul class="ml-4 list-disc text-sm text-warning-600">%s</ul>',
                count($items),
                implode('', array_map(static fn (string $item): string => '<li>'.$item.'</li>', $items)),
            );
        }

        return new HtmlString(implode('', $lines));
    }

    /**
     * Send a success/warning notification summarising the import result.
     *
     * @param  array{deduction: array{id: int, code: string, name: string}|null, stored: int, batches: int, errors: array<int, array<string, mixed>>}  $result
     */
    protected static function sendResultNotification(array $result): void
    {
        if ($result['deduction'] === null) {
            Notification::make()
                ->title('Import failed')
                ->body('Could not resolve the deduction from the file. Nothing was imported.')
                ->danger()
                ->send();

            return;
        }

        $errorCount = count($result['errors']);
        $body = sprintf('%d row(s) imported in %d batch(es).', $result['stored'], $result['batches']);

        if ($errorCount > 0) {
            Notification::make()
                ->title('Deductions imported with warnings')
                ->body($body.sprintf(' %d row(s) were skipped.', $errorCount))
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Deductions imported')
            ->body($body)
            ->success()
            ->send();
    }

    /**
     * Read the raw contents of the uploaded file, normalising the possible
     * shapes of a {@see FileUpload} state (single object or keyed array). Both
     * {@see TemporaryUploadedFile} and {@see UploadedFile} expose `get()`.
     */
    protected static function readContents(mixed $state): ?string
    {
        if (is_array($state)) {
            $state = reset($state) ?: null;
        }

        if ($state instanceof TemporaryUploadedFile || $state instanceof UploadedFile) {
            return (string) $state->get();
        }

        return null;
    }
}
