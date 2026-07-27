<?php

namespace App\Filament\Resources\LateDeductionMatrices;

use App\Filament\Resources\LateDeductionMatrices\Pages\CreateLateDeductionMatrix;
use App\Filament\Resources\LateDeductionMatrices\Pages\EditLateDeductionMatrix;
use App\Filament\Resources\LateDeductionMatrices\Pages\ListLateDeductionMatrices;
use App\Filament\Resources\LateDeductionMatrices\Schemas\LateDeductionMatrixForm;
use App\Filament\Resources\LateDeductionMatrices\Tables\LateDeductionMatricesTable;
use App\Models\LateDeductionMatrix;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LateDeductionMatrixResource extends Resource
{
    protected static ?string $model = LateDeductionMatrix::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll Setup';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return LateDeductionMatrixForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LateDeductionMatricesTable::configure($table);
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
            'index' => ListLateDeductionMatrices::route('/'),
            'create' => CreateLateDeductionMatrix::route('/create'),
            'edit' => EditLateDeductionMatrix::route('/{record}/edit'),
        ];
    }
}
