<?php

namespace App\Filament\Resources\DeductionGroups;

use App\Filament\Resources\DeductionGroups\Pages\CreateDeductionGroup;
use App\Filament\Resources\DeductionGroups\Pages\EditDeductionGroup;
use App\Filament\Resources\DeductionGroups\Pages\ListDeductionGroups;
use App\Filament\Resources\DeductionGroups\Schemas\DeductionGroupForm;
use App\Filament\Resources\DeductionGroups\Tables\DeductionGroupsTable;
use App\Models\DeductionGroup;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeductionGroupResource extends Resource
{
    protected static ?string $model = DeductionGroup::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderMinus;

    protected static string|UnitEnum|null $navigationGroup = 'Deductions';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return DeductionGroupForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeductionGroupsTable::configure($table);
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
            'index' => ListDeductionGroups::route('/'),
            'create' => CreateDeductionGroup::route('/create'),
            'edit' => EditDeductionGroup::route('/{record}/edit'),
        ];
    }
}
