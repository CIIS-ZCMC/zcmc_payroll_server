<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Reference row ids
    |--------------------------------------------------------------------------
    |
    | The payroll code addresses a handful of reference rows by their primary
    | key. These are the ids the seeders produce; they live here so that a
    | differently seeded environment can correct them without a code change.
    |
    | Deduction groups, in seeder order:
    |   1 Taxes, 2 GSIS, 3 SSS, 4 Pag-IBIG, 5 PhilHealth, 6 Others
    |
    */

    'receivables' => [
        'pera' => env('PAYROLL_RECEIVABLE_PERA', 1),
        'hazard' => env('PAYROLL_RECEIVABLE_HAZARD', 2),
    ],

    'deductions' => [
        'wtax' => env('PAYROLL_DEDUCTION_WTAX', 1),
        'phic' => env('PAYROLL_DEDUCTION_PHIC', 2),
    ],

    'deduction_groups' => [
        'tax' => 1,
        'gsis' => 2,
        'sss' => 3,
        'pagibig' => 4,
        'philhealth' => 5,
        'others' => 6,
    ],

    /*
    |--------------------------------------------------------------------------
    | Computation constants
    |--------------------------------------------------------------------------
    */

    // Duty days in a month, used to prorate PERA against absences.
    'required_duty_days' => env('PAYROLL_REQUIRED_DUTY_DAYS', 22),

    // Net pay below this lands an employee in the excluded list.
    'net_pay_exclusion_threshold' => env('PAYROLL_NET_PAY_THRESHOLD', 5000),

];
