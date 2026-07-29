<?php

namespace App\Filament\Resources\EmployeeReceivables\Actions;

use App\Models\PayrollPeriod;
use App\Models\Receivable;
use App\Services\BulkEmployeeReceivableService;
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
 * Filament header action that imports standing employee receivables from a CSV
 * file. A two-step wizard: upload the file (with optional receivable /
 * payroll-period overrides), review the parsed preview — matched rows and
 * per-row errors — then confirm. Parsing and persistence are delegated to
 * {@see BulkEmployeeReceivableService}; this action only wires the panel UI.
 */
class ImportReceivablesAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'importReceivables';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label('Import Receivables')
            ->icon(Heroicon::OutlinedArrowUpTray)
            ->modalWidth('3xl')
            ->modalSubmitActionLabel('Import')
            ->steps([
                Step::make('Upload')
                    ->description('Choose a receivable CSV file')
                    ->schema([
                        FileUpload::make('file')
                            ->label('CSV file')
                            ->acceptedFileTypes(['text/csv', 'text/plain'])
                            ->storeFiles(false)
                            ->required()
                            ->live(),
                        Select::make('receivable_id')
                            ->label('Receivable')
                            ->options(fn (): array => Receivable::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->helperText("Leave blank to resolve from the file's Code cell."),
                        Select::make('payroll_period_id')
                            ->label('Payroll period')
                            ->options(fn (): array => static::payrollPeriodOptions())
                            ->searchable()
                            ->helperText('Optional. Scope the imported receivables to a payroll period.'),
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

                $result = app(BulkEmployeeReceivableService::class)->import(
                    $contents,
                    $data['receivable_id'] ?? null,
                    $data['payroll_period_id'] ?? null,
                );

                static::sendResultNotification($result);
            });
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

    /**
     * Render the review-step preview from a no-persist {@see BulkEmployeeReceivableService::parse()} pass.
     */
    protected static function renderPreview(Get $get): HtmlString
    {
        $contents = static::readContents($get('file'));

        if ($contents === null) {
            return new HtmlString('<p class="text-sm text-gray-500">Upload a file on the previous step to see a preview.</p>');
        }

        $parsed = app(BulkEmployeeReceivableService::class)->parse(
            $contents,
            $get('receivable_id') ?: null,
        );

        $lines = [];

        if ($parsed['receivable'] === null) {
            $lines[] = '<p class="text-sm font-semibold text-danger-600">Unknown receivable — could not resolve a code from the file. Select a receivable on the previous step.</p>';
        } else {
            $lines[] = sprintf(
                '<p class="text-sm"><span class="font-semibold">Receivable:</span> %s (%s)</p>',
                e($parsed['receivable']['name']),
                e($parsed['receivable']['code']),
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

                if (($error['error'] ?? null) === 'unknown_receivable_code') {
                    return sprintf('Unknown receivable code: %s', e((string) ($error['value'] ?? '')));
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
     * @param  array{receivable: array{id: int, code: string, name: string}|null, stored: int, batches: int, errors: array<int, array<string, mixed>>}  $result
     */
    protected static function sendResultNotification(array $result): void
    {
        if ($result['receivable'] === null) {
            Notification::make()
                ->title('Import failed')
                ->body('Could not resolve the receivable from the file. Nothing was imported.')
                ->danger()
                ->send();

            return;
        }

        $errorCount = count($result['errors']);
        $body = sprintf('%d row(s) imported in %d batch(es).', $result['stored'], $result['batches']);

        if ($errorCount > 0) {
            Notification::make()
                ->title('Receivables imported with warnings')
                ->body($body.sprintf(' %d row(s) were skipped.', $errorCount))
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title('Receivables imported')
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
