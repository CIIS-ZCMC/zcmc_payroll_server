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
}
