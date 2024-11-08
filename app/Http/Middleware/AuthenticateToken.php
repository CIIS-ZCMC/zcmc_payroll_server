<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Token;
use Illuminate\Http\Response;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;
use App\Models\TimeRecord;
use Illuminate\Support\Facades\DB;
use App\Helpers\Helpers;

class AuthenticateToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {

        try {
            $token = Token::myToken();


            $accessToken = PersonalAccessToken::where("token", $token);

            if (!$accessToken->count()) {
                return response()->json(['message' => 'Un-Authorized', 'response' => 'Please relogin'], Response::HTTP_UNAUTHORIZED);
            }

            $expiry = strtotime(date('Y-m-d H:i:s', strtotime($accessToken->first()->last_used_at . ' +' . env("TOKEN_EXPIRY") . " minutes")));
            $current = strtotime(date('Y-m-d H:i:s'));

            if ($expiry < $current) {
                //EXPIRED
                return response()->json(['message' => 'Un-Authorized', 'response' => 'Expired Token'], Response::HTTP_UNAUTHORIZED);
            }
            $timeRecord = TimeRecord::where('is_active', 1)->latest()->first();
            $tr = [];
            if ($timeRecord) {
                $tr = [
                    'month' => $timeRecord->month,
                    'year' => $timeRecord->year
                ];
            }


            $record = DB::table('time_records')
                ->select([
                    DB::raw('month as month_'),
                    DB::raw('year as year_'),
                ])
                ->where('is_active', 1)
                ->whereIn('employee_list_id', function ($query) {
                    $query->select('employee_list_id')
                        ->from('employee_salaries')
                        ->where('employment_type', '!=', 'Job Order');
                })
                ->limit(1)
                ->first();


            $JobOrderrecord = DB::table('time_records')
                ->select('fromPeriod', 'toPeriod')
                ->where('is_active', 1)
                ->where('month', '=', DB::raw("
                CASE
                    WHEN (
                        SELECT `month`
                        FROM `time_records`
                        WHERE `is_active` = 1
                        AND `employee_list_id` IN (
                            SELECT `employee_list_id`
                            FROM `employee_salaries`
                            WHERE `employment_type` != 'Job Order'
                        )
                        LIMIT 1
                    ) = 12 THEN 1
                    ELSE (
                        SELECT `month` + 1
                        FROM `time_records`
                        WHERE `is_active` = 1
                        AND `employee_list_id` IN (
                            SELECT `employee_list_id`
                            FROM `employee_salaries`
                            WHERE `employment_type` != 'Job Order'
                        )
                        LIMIT 1
                    )
                END
            "))
                ->whereIn('employee_list_id', function ($query) {
                    $query->select('employee_list_id')
                        ->from('employee_salaries')
                        ->where('employment_type', 'Job Order');
                })
                ->limit(1)
                ->first();


            $fromPeriod = 0;
            $toPeriod = 0;
            if ($JobOrderrecord) {
                $fromPeriod = $JobOrderrecord->fromPeriod;
                $toPeriod = $JobOrderrecord->toPeriod;
            }

            if ($record) {
                $month = $record->month_ + 1;
                $year = $record->year_;
                if ($month == 13) {
                    $month = 1;
                    $year = $year + 1;
                }

                $tr = [
                    'month' => $month,
                    'year' => $year,
                    'monthName' => date('F', strtotime($year . "-" . $month . "-1")),
                    'JOfromPeriod' => $fromPeriod,
                    'JOtoPeriod' => $toPeriod,
                ];
            } else {
                if (date('d') >= 11) {
                    $tr = [
                        'month' => date('m'),
                        'year' => date('Y'),
                        'monthName' => date('F', strtotime(date('Y') . "-" . date('m') . "-1")),
                        'JOfromPeriod' => $fromPeriod,
                        'JOtoPeriod' => $toPeriod,
                    ];
                } else {
                    $tr = [
                        'month' => Helpers::getPreviousMonthYear(date('m'), date('Y'))['month'],
                        'year' => Helpers::getPreviousMonthYear(date('m'), date('Y'))['year'],
                        'monthName' => date('F', strtotime(Helpers::getPreviousMonthYear(date('m'), date('Y'))['year'] . "-" . Helpers::getPreviousMonthYear(date('m'), date('Y'))['month'] . "-1")),
                        'JOfromPeriod' => $fromPeriod,
                        'JOtoPeriod' => $toPeriod,
                    ];
                }
            }

            $accessToken->update([
                'last_used_at' => now(),
            ]);

            $request->merge(['user' => Token::UserInfo(), 'processMonth' => $tr, 'umis' => env("UMIS")]);
            return $next($request);
        } catch (\Throwable $th) {
            Log::channel('code')->error($th);
            return response()->json(["data" => Token::myToken(), 'message' => 'un-authorized', 'response' => $th->getMessage(), 'addr' => ' Middleware/AuthenticateToken'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
