<?php

namespace App\Filament\Resources\NightDifferentialRules;

use App\Filament\Resources\NightDifferentialRules\Pages\CreateNightDifferentialRule;
use App\Filament\Resources\NightDifferentialRules\Pages\EditNightDifferentialRule;
use App\Filament\Resources\NightDifferentialRules\Pages\ListNightDifferentialRules;
use App\Filament\Resources\NightDifferentialRules\Schemas\NightDifferentialRuleForm;
use App\Filament\Resources\NightDifferentialRules\Tables\NightDifferentialRulesTable;
use App\Models\NightDifferentialRule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class NightDifferentialRuleResource extends Resource
{
    protected static ?string $model = NightDifferentialRule::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMoon;

    protected static string|UnitEnum|null $navigationGroup = 'Payroll Setup';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return NightDifferentialRuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return NightDifferentialRulesTable::configure($table);
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
            'index' => ListNightDifferentialRules::route('/'),
            'create' => CreateNightDifferentialRule::route('/create'),
            'edit' => EditNightDifferentialRule::route('/{record}/edit'),
        ];
    }
}
