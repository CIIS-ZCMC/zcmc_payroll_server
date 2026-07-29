<?php

namespace App\Filament\Resources\EmployeeReceivables;

use App\Filament\Resources\EmployeeReceivables\Pages\CreateEmployeeReceivable;
use App\Filament\Resources\EmployeeReceivables\Pages\EditEmployeeReceivable;
use App\Filament\Resources\EmployeeReceivables\Pages\ListEmployeeReceivables;
use App\Filament\Resources\EmployeeReceivables\RelationManagers\LogsRelationManager;
use App\Filament\Resources\EmployeeReceivables\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\EmployeeReceivables\Schemas\EmployeeReceivableForm;
use App\Filament\Resources\EmployeeReceivables\Tables\EmployeeReceivablesTable;
use App\Models\EmployeeReceivable;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeReceivableResource extends Resource
{
    protected static ?string $model = EmployeeReceivable::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedWallet;

    protected static string|UnitEnum|null $navigationGroup = 'Employee Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return EmployeeReceivableForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeReceivablesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            PaymentsRelationManager::class,
            LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployeeReceivables::route('/'),
            'create' => CreateEmployeeReceivable::route('/create'),
            'edit' => EditEmployeeReceivable::route('/{record}/edit'),
        ];
    }
}
