<?php

namespace App\Filament\Resources\Activities\Tables;

use App\Models\Activity;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ActivitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('log_name')
                    ->label('Module')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        default => 'gray',
                    })
                    ->sortable(),
                TextColumn::make('subject_type')
                    ->label('Subject')
                    ->formatStateUsing(fn ($state, $record) => class_basename((string) $state).' #'.$record->subject_id)
                    ->searchable(),
                TextColumn::make('causer_id')
                    ->label('By')
                    ->formatStateUsing(fn ($state, $record) => $state ? class_basename((string) $record->causer_type).' #'.$state : 'System / API'),
                TextColumn::make('description')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('log_name')
                    ->label('Module')
                    ->options(fn (): array => Activity::query()->distinct()->orderBy('log_name')->pluck('log_name', 'log_name')->all()),
                SelectFilter::make('event')
                    ->options([
                        'created' => 'Created',
                        'updated' => 'Updated',
                        'deleted' => 'Deleted',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}
