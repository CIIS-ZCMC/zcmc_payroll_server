<?php

namespace App\Filament\Resources\EmployeeComputedSalaryResource\Pages;

use App\Filament\Resources\EmployeeComputedSalaryResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEmployeeComputedSalary extends CreateRecord
{
    protected static string $resource = EmployeeComputedSalaryResource::class;
}
