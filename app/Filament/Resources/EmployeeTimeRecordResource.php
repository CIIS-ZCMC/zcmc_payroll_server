<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeTimeRecordResource\Pages;
use App\Filament\Resources\EmployeeTimeRecordResource\RelationManagers;
use App\Models\EmployeeTimeRecord;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeTimeRecordResource extends Resource
{
    protected static ?string $model = EmployeeTimeRecord::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Employees';

    protected static ?int $navigationSort = 30;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('employee_id')
                    ->required(),
                Forms\Components\TextInput::make('payroll_period_id')
                    ->required(),
                Forms\Components\TextInput::make('total_working_minutes')
                    ->required(),
                Forms\Components\TextInput::make('total_working_minutes_with_leave')
                    ->required(),
                Forms\Components\TextInput::make('total_working_hours')
                    ->required(),
                Forms\Components\TextInput::make('total_working_hours_with_leave')
                    ->required(),
                Forms\Components\TextInput::make('total_overtime_minutes')
                    ->required(),
                Forms\Components\TextInput::make('total_undertime_minutes')
                    ->required(),
                Forms\Components\TextInput::make('total_official_business_minutes')
                    ->required(),
                Forms\Components\TextInput::make('total_official_time_minutes')
                    ->required(),
                Forms\Components\TextInput::make('total_leave_minutes')
                    ->required(),
                Forms\Components\TextInput::make('total_night_duty_hours')
                    ->required(),
                Forms\Components\TextInput::make('no_of_present_days')
                    ->required(),
                Forms\Components\TextInput::make('no_of_present_days_with_leave')
                    ->required(),
                Forms\Components\TextInput::make('no_of_leave_wo_pay')
                    ->required(),
                Forms\Components\TextInput::make('no_of_leave_w_pay')
                    ->required(),
                Forms\Components\TextInput::make('no_of_absences')
                    ->required(),
                Forms\Components\TextInput::make('no_of_invalid_entry')
                    ->required(),
                Forms\Components\TextInput::make('no_of_day_off')
                    ->required(),
                Forms\Components\TextInput::make('no_of_schedule')
                    ->required(),
                Forms\Components\Textarea::make('night_duties')
                    ->required(),
                Forms\Components\Textarea::make('absent_dates')
                    ->required(),
                Forms\Components\TextInput::make('month')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('year')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('from')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('to')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('status')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Toggle::make('is_active')
                    ->required(),
                Forms\Components\DateTimePicker::make('locked_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('employee_id'),
                Tables\Columns\TextColumn::make('payroll_period_id'),
                Tables\Columns\TextColumn::make('total_working_minutes'),
                Tables\Columns\TextColumn::make('total_working_minutes_with_leave'),
                Tables\Columns\TextColumn::make('total_working_hours'),
                Tables\Columns\TextColumn::make('total_working_hours_with_leave'),
                Tables\Columns\TextColumn::make('total_overtime_minutes'),
                Tables\Columns\TextColumn::make('total_undertime_minutes'),
                Tables\Columns\TextColumn::make('total_official_business_minutes'),
                Tables\Columns\TextColumn::make('total_official_time_minutes'),
                Tables\Columns\TextColumn::make('total_leave_minutes'),
                Tables\Columns\TextColumn::make('total_night_duty_hours'),
                Tables\Columns\TextColumn::make('no_of_present_days'),
                Tables\Columns\TextColumn::make('no_of_present_days_with_leave'),
                Tables\Columns\TextColumn::make('no_of_leave_wo_pay'),
                Tables\Columns\TextColumn::make('no_of_leave_w_pay'),
                Tables\Columns\TextColumn::make('no_of_absences'),
                Tables\Columns\TextColumn::make('no_of_invalid_entry'),
                Tables\Columns\TextColumn::make('no_of_day_off'),
                Tables\Columns\TextColumn::make('no_of_schedule'),
                Tables\Columns\TextColumn::make('night_duties'),
                Tables\Columns\TextColumn::make('absent_dates'),
                Tables\Columns\TextColumn::make('month'),
                Tables\Columns\TextColumn::make('year'),
                Tables\Columns\TextColumn::make('from'),
                Tables\Columns\TextColumn::make('to'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('locked_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
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
            'index' => Pages\ListEmployeeTimeRecords::route('/'),
            'create' => Pages\CreateEmployeeTimeRecord::route('/create'),
            'edit' => Pages\EditEmployeeTimeRecord::route('/{record}/edit'),
        ];
    }    
}
