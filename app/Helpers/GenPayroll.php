<?php

namespace App\Helpers;

use App\Models\TimeRecord;
use DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class GenPayroll
{

    public static function extractNumericValue($currencyString) {

        $numericString = preg_replace('/[^\d.]/', '', $currencyString);
        return floatval($numericString);
    }


}
