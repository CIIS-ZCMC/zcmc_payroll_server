<?php

namespace App\Filament\Resources\EmployeeDeductions;

use App\Filament\Resources\EmployeeDeductions\Pages\CreateEmployeeDeduction;
use App\Filament\Resources\EmployeeDeductions\Pages\EditEmployeeDeduction;
use App\Filament\Resources\EmployeeDeductions\Pages\ListEmployeeDeductions;
use App\Filament\Resources\EmployeeDeductions\RelationManagers\LogsRelationManager;
use App\Filament\Resources\EmployeeDeductions\RelationManagers\PaymentsRelationManager;
use App\Filament\Resources\EmployeeDeductions\Schemas\EmployeeDeductionForm;
use App\Filament\Resources\EmployeeDeductions\Tables\EmployeeDeductionsTable;
use App\Models\EmployeeDeduction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeDeductionResource extends Resource
{
    protected static ?string $model = EmployeeDeduction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Employee Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Schema $schema): Schema
    {
        return EmployeeDeductionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeeDeductionsTable::configure($table);
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
            'index' => ListEmployeeDeductions::route('/'),
            'create' => CreateEmployeeDeduction::route('/create'),
            'edit' => EditEmployeeDeduction::route('/{record}/edit'),
        ];
    }
}
