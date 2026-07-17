<?php

namespace App\Filament\Resources\ExcludedEmployeeResource\Pages;

use App\Filament\Resources\ExcludedEmployeeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateExcludedEmployee extends CreateRecord
{
    protected static string $resource = ExcludedEmployeeResource::class;
}
