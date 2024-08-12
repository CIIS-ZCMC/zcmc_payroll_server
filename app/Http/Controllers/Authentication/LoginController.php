<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\Helpers\Helpers;
use \App\Helpers\Token;
use \App\Models\PersonalAccessToken;


class LoginController extends Controller
{
    public function Signin(Request $request)
    {
        try {
            $LoginResponse = Helpers::umisPOSTrequest("sign-in", [
                'employee_id' => $request->employee_id,
                'password' => $request->password,
            ]);
            $GetInformation = Helpers::umisPOSTrequest("getUserInformations", [
                'profileID' => $LoginResponse['data']['employee_profile_id']
            ]);
            $data = array_merge($GetInformation, $LoginResponse['data']);
            $AccessToken = PersonalAccessToken::where('employee_id', $data['employee_id'])
                ->where("name", $data['name']);
            $generatedToken = Token::generateToken();

            /**
             * Add validation code here
             * to check for access in [ Payroll Modules ]
             */
            if ($AccessToken->count() >= 1) {
                if ($AccessToken->first()->token == Token::myToken()) {
                    //check expiry
                    $expiry = strtotime(date('Y-m-d H:i:s', strtotime($AccessToken->first()->last_used_at . ' +' . env("TOKEN_EXPIRY") . " minutes")));
                    $current = strtotime(date('Y-m-d H:i:s'));
                    if ($expiry <= $current) {
                        //EXPIRED
                        //RENEW TOKEN AND LASTUSED
                        $AccessToken->update([
                            'token' => $generatedToken,
                            'abilities' => encrypt($data, true),
                            'last_used_at' => now(),
                        ]);
                    }
                    //RENEW LASTUSED ONLY
                    $AccessToken->update([
                        'token' => $generatedToken,
                        'abilities' => encrypt($data, true),
                        'last_used_at' => now(),
                    ]);
                }
            } else {
                //No cookie detected
                PersonalAccessToken::create([
                    'employee_id' => $data['employee_id'],
                    'name' => $data['name'],
                    'token' => $generatedToken,
                    'abilities' => encrypt($data, true),
                    'last_used_at' => now(),
                ]);
            }
            return response()->json([
                'message' => 'Login Success'
            ], 200)->cookie(env("COOKIE_NAME"), json_encode(['token' => $generatedToken]), env("COOKIE_EXPIRY"), '/', env("SESSION_DOMAIN"), false);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => "Login failed",
                'Response' => $th->getMessage()
            ], 401);
        }
    }
}
