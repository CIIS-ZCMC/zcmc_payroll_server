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

    public static function getPreviousMonthYear(int $month, int $year): array
    {
        if ($month === 1) {
            return [
                "month"=>12,
                "year"=>$year-1

            ];
        } else {
            return [
                "month"=>$month - 1,
                "year"=>$year

            ];
        }
    }


    public static function getNextMonthYear(int $month, int $year): array
    {
            if ($month === 12) {
                return [
                    "month" => 1,
                    "year"  => $year + 1
                ];
            } else {
                return [
                    "month" => $month + 1,
                    "year"  => $year
                ];
            }
        }
   


}
