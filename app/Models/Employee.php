<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Employee extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'employees';

    protected $primaryKey = 'id';

    protected $fillable = [
        'employee_profile_id',
        'employee_number',
        'first_name',
        'last_name',
        'middle_name',
        'extension_name',
        'designation',
        'assigned_area',
        'is_newly_hired',
        'is_excluded',
        'is_resigned',
        'status',
    ];
    public $timestamps = true;

    protected $appends = ['full_name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('employee')
            ->logFillable()
            ->logOnlyDirty();
    }


    public function getFullNameAttribute(): string
    {
        $middleInitial = $this->middle_name
            ? strtoupper(substr($this->middle_name, 0, 1)) . '.'
            : '';

        return "{$this->last_name}, {$this->first_name} {$middleInitial}";
    }

    public function employeeSalary()
    {
        return $this->hasOne(EmployeeSalary::class);
    }

    public function employeeTimeRecords()
    {
        return $this->hasOne(EmployeeTimeRecord::class);
    }

    public function employeeComputedSalary()
    {
        return $this->hasOne(EmployeeComputedSalary::class);
    }

    public function employeeDeductions()
    {
        return $this->hasMany(EmployeeDeduction::class);
    }

    public function employeeReceivables()
    {
        return $this->hasMany(EmployeeReceivable::class);
    }

    public function excludedEmployees()
    {
        return $this->belongsTo(ExcludedEmployee::class);
    }

    public function employeePayroll()
    {
        return $this->hasMany(EmployeePayroll::class);
    }

    public function groupedDeductions($deductions = null)
    {
        $deductions = collect($deductions ?? $this->employeeDeductions);
    
        return $deductions
            ->filter(fn ($item) => $item->deductions) // prevent null
            ->groupBy(fn ($item) => $item->deductions->deduction_group_id)
            ->map(function ($items) {
                $group = $items->first()->deductions->deductionGroup;
    
                return [
                    'group_id' => $group?->id,
                    'group_name' => $group?->name,
                    'group_total' => $items->sum('amount'),
                    'employee_id' => $this->id,
                    'deduction_details' => $items->map(function ($deduction) {
                        return [
                            'id' => $deduction->id,
                            'employee_id' => $deduction->employee_id,
                            'payroll_period_id' => $deduction->payroll_period_id,
                            'deduction_id' => $deduction->deduction_id,
                            'deduction_group_id' => $deduction->deductions?->deduction_group_id,
                            'name' => $deduction->deductions?->name,
                            'code' => $deduction->deductions?->code,
                            'amount' => $deduction->amount,
                            'billing_cycle' => $deduction->billing_cycle,
                        ];
                    })->values(),
                ];
            })
            ->values();
    }

    public function getGroupedDeductionsAttribute()
    {
        return $this->groupedDeductions($this->employeeDeductions ?? collect());
    }
}

