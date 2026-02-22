<?php

namespace App\Enums;

class PayrollType
{
    const GENERAL = 'general_payroll';
    const SPECIAL_PAYROLL = 'special_payroll';
    const THIRTEENTH_MONTH_PAY = '13_month_pay';
    const NIGHT_DIFFERENTIAL = 'night_differential';

    // FOR PAYROLL PROCESS
    const REGULAR = 0;
    const JOBORDER = 1;
    const NIGHT = 2;
    const SPECIAL = 3;
    const THIRTEENTH_MONTH = 4;
}
