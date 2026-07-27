<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;
use App\Filament\Resources\Activities\Pages\ViewActivity;
use App\Filament\Resources\Activities\Tables\ActivitiesTable;
use App\Models\Activity;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentList;

    protected static string|UnitEnum|null $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Activity Log';

    protected static ?string $modelLabel = 'activity';

    protected static ?int $navigationSort = 99;

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('log_name')->label('Module')->badge(),
            TextEntry::make('event')->badge(),
            TextEntry::make('subject_type')->label('Subject')
                ->formatStateUsing(fn ($state, $record) => class_basename((string) $state).' #'.$record->subject_id),
            TextEntry::make('causer_id')->label('Performed by')
                ->formatStateUsing(fn ($state, $record) => $state ? class_basename((string) $record->causer_type).' #'.$state : 'System / API'),
            TextEntry::make('description'),
            TextEntry::make('created_at')->dateTime(),
            TextEntry::make('properties')->label('Details')->columnSpanFull()
                ->formatStateUsing(fn ($state) => json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return ActivitiesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'view' => ViewActivity::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
