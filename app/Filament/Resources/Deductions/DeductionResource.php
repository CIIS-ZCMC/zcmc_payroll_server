<?php

namespace App\Filament\Resources\Deductions;

use App\Filament\Resources\Deductions\Pages\CreateDeduction;
use App\Filament\Resources\Deductions\Pages\EditDeduction;
use App\Filament\Resources\Deductions\Pages\ListDeductions;
use App\Filament\Resources\Deductions\Schemas\DeductionForm;
use App\Filament\Resources\Deductions\Tables\DeductionsTable;
use App\Models\Deduction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class DeductionResource extends Resource
{
    protected static ?string $model = Deduction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedReceiptPercent;

    protected static string|UnitEnum|null $navigationGroup = 'Deductions';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return DeductionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DeductionsTable::configure($table);
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
            'index' => ListDeductions::route('/'),
            'create' => CreateDeduction::route('/create'),
            'edit' => EditDeduction::route('/{record}/edit'),
        ];
    }
}
