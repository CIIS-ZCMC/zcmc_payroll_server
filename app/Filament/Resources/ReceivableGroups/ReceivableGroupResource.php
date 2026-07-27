<?php

namespace App\Filament\Resources\ReceivableGroups;

use App\Filament\Resources\ReceivableGroups\Pages\CreateReceivableGroup;
use App\Filament\Resources\ReceivableGroups\Pages\EditReceivableGroup;
use App\Filament\Resources\ReceivableGroups\Pages\ListReceivableGroups;
use App\Filament\Resources\ReceivableGroups\Schemas\ReceivableGroupForm;
use App\Filament\Resources\ReceivableGroups\Tables\ReceivableGroupsTable;
use App\Models\ReceivableGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ReceivableGroupResource extends Resource
{
    protected static ?string $model = ReceivableGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Receivables';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ReceivableGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ReceivableGroupsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReceivableGroups::route('/'),
            'create' => CreateReceivableGroup::route('/create'),
            'edit' => EditReceivableGroup::route('/{record}/edit'),
        ];
    }
}
