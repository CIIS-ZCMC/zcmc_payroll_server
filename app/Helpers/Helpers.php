<?php

namespace App\Helpers;

use App\Models\TimeRecord;
use DB;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

class Helpers
{

    public static function registerSystemLogs($request, $moduleID, $status, $remarks)
    {
        $ip = $request->ip();
        $user = $request->user;
        $permission = $request->permission;
        list($module, $action) = explode(' ', $permission);

        return [
            'employee_profile_id' => $user->id,
            'module_id' => $moduleID,
            'action' => $action,
            'module' => $module,
            'status' => $status,
            'remarks' => $remarks,
            'ip_address' => $ip
        ];
    }

    public static function decryptSensitiveData($data, $fieldsToDecrypt = [])
    {

        if (is_array($data)) {
            $data = (object) $data;
        }
        foreach ($fieldsToDecrypt as $field) {
            if (property_exists($data, $field)) {

                try {

                    $data->$field = decrypt($data->$field);
                } catch (Exception $e) {

                    $data->$field = 'Unable to decrypt';
                }
            }

        }


        return (array) $data;
    }


    public static  function mergeAndGetUniqueReceivables(array $data) {
        // Initialize an empty array to hold merged results
        $mergedReceivables = [];

        // Loop through each sub-array in the input data
        foreach ($data as $subArray) {
            foreach ($subArray as $item) {
                // Check if the item has a receivable_id
                if (isset($item['receivable_id'])) {
                    // Use the receivable_id as a unique key for uniqueness
                    $mergedReceivables[$item['receivable_id']] = [
                        'receivable_id' => $item['receivable_id'],
                        'receivable' => $item['receivable'],
                        'amount' => $item['amount'],
                    ];
                }
            }
        }

        // Return the unique merged array values
        return array_values($mergedReceivables);
    }

    public static function DateFormats($date)
    {
        $timestamp = strtotime($date); // Convert the date to a timestamp

        return [
            'customFormat' => date('h:iA F j,Y', $timestamp),
            'ISO' => date('Y-m-d', $timestamp),                   // 2024-09-20
            'US' => date('m/d/Y', $timestamp),                     // 09/20/2024
            'EU' => date('d/m/Y', $timestamp),                     // 20/09/2024
            'FullMonth' => date('F j, Y', $timestamp),            // September 20, 2024
            'Suffix' => date('jS F Y', $timestamp),               // 20th September 2024
            'Dot' => date('Y.m.d', $timestamp),                    // 2024.09.20
            'Dash' => date('d-m-Y', $timestamp),                   // 20-09-2024
            'DayDate' => date('D, d M Y', $timestamp),            // Fri, 20 Sep 2024
            'FullDay' => date('l, F j, Y', $timestamp),           // Friday, September 20, 2024
            'FullDateTime' => date('Y-m-d H:i:s', $timestamp),    // 2024-09-20 00:00:00
            'USDateTime' => date('m/d/Y H:i', $timestamp),        // 09/20/2024 00:00
            'EUDateTime' => date('d/m/Y H:i', $timestamp),        // 20/09/2024 00:00
            'Slash' => date('Y/m/d', $timestamp),                  // 2024/09/20
            'AbbrMonth' => date('M d, Y', $timestamp),            // Sep 20, 2024
            'Time' => date('H:i:s', $timestamp),                   // 00:00:00
            'ISO8601' => date('Y-m-d\TH:i:sP', $timestamp)        // 2024-09-20T00:00:00+00:00
        ];
    }

    public static function customRound($numericValue)
    {
        return (double) number_format($numericValue, 2, '.', '');
    }


    public static function errorLog($controller, $module, $errorMessage)
    {
        Log::channel('custom-error')->error($controller . ' Controller [' . $module . ']: message: ' . $errorMessage);
    }

    public static function umisPOSTrequest($api, $data)
    {
        $client = new Client();
        return json_decode($client->request('POST', request()->umis . '/' . $api, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode($data),
        ])->getBody(), true);
    }
    public static function umisGETrequest($api)
    {
        $client = new Client();
        $response = $client->request('GET', request()->umis . '/' . $api);
        return json_decode($response->getBody(), true);
    }

    public static function getPreviousMonthYear($month, $year)
    {
        // Calculate the previous month
        $previousMonth = $month - 1;
        $previousYear = $year;

        // If the previous month is less than 1, set it to 12 (December)
        if ($previousMonth < 1) {
            $previousMonth = 12;
            $previousYear = $year - 1;
        }

        return [
            'month' => (string) $previousMonth,
            'year' => (string) $previousYear
        ];
    }

    public static function getAdvanceMonthYear($month, $year)
    {
        $advMonth = $month + 1;
        $advYear = $year;

        if ($advMonth >= 13) {
            $advMonth = 1;
            $advYear = $year + 1;
        }

        return [
            'month' => (string) $advMonth,
            'year' => (string) $advYear
        ];
    }

    public static function getAllArea()
    {
        $divisions = DB::connection('mysql2')->select('SELECT * FROM divisions');
        $departments = DB::connection('mysql2')->select('SELECT * FROM departments');
        $sections = DB::connection('mysql2')->select('SELECT * FROM sections');
        $units = DB::connection('mysql2')->select('SELECT * FROM units');
        $all_areas = [];

        foreach ($divisions as $division) {
            $area = [
                'area' => $division->id,
                'name' => $division->name,
                'sector' => 'division',
                'code' => $division->code
            ];
            $all_areas[] = $area;
        }

        foreach ($departments as $department) {
            $area = [
                'area' => $department->id,
                'name' => $department->name,
                'sector' => 'department',
                'code' => $department->code
            ];
            $all_areas[] = $area;
        }

        foreach ($sections as $section) {
            $area = [
                'area' => $section->id,
                'name' => $section->name,
                'sector' => 'section',
                'code' => $section->code
            ];
            $all_areas[] = $area;
        }

        foreach ($units as $unit) {
            $area = [
                'area' => $unit->id,
                'name' => $unit->name,
                'sector' => 'unit',
                'code' => $unit->code
            ];
            $all_areas[] = $area;
        }

        return $all_areas;
    }

    public static function convertToStdObject($genpayrollList)
    {

        if (is_object($genpayrollList)) {
            $genpayrollList = [$genpayrollList];
        } elseif (is_array($genpayrollList)) {
            foreach ($genpayrollList as &$entry) {
                if (is_array($entry)) {
                    $entry = (object) $entry;
                }
            }
        }

        return $genpayrollList;
    }

}
