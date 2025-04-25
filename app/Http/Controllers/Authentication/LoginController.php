<?php

namespace App\Http\Controllers\Authentication;

use App\Helpers\UmisHttpRequestHelper;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use \App\Helpers\Helpers;
use \App\Helpers\Token;
use \App\Helpers\Logging;
use \App\Models\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    private function __destroySession($employee_id)
    {
        PersonalAccessToken::where('employee_id', $employee_id)->delete();
    }

    public function store(Request $request)
    {
        $result = UmisHttpRequestHelper::post("auth-with-api-key-credential", [
            'employee_id' => $request->employee_id,
            'password' => $request->password,
        ]);

        if($result['is_failed']){
            return response()->json([
                'message' => $result['message']
            ], Response::HTTP_UNAUTHORIZED);
        }

        $generatedToken = Token::generateToken();
        $data = $result['data'];    
        $user_details = $data['user_details'];
        $employee_details = $user_details['employee_details'];
        $contact = $employee_details['contact'];

        $session = $data['session'];

        $employee_id = $user_details['employee_id'];
        $employee_designation = $user_details['designation'];
        $employee_email = $contact['email_address'];
        $employee_name = $data['user_details']['name'];
        $token = $session['token'];
        $permissions = json_encode($data['permissions']);
        $last_used_at = Carbon::now();
        $expire_at = Carbon::now()->addMinutes(30);

        self::__destroySession($employee_id);

        $user = [
            'employee_id' => $employee_id,
            'email' => $employee_email,
            'name' => $employee_name,
            'token' => $token,
            'permissions' => $permissions,
            'last_used_at' => $last_used_at,
            'expire_at' => $expire_at
        ];

        $access_token = PersonalAccessToken::create($user);

        return response()->json([
            'data' => [
                'access_token' => $access_token,
                'name' => $employee_name,
                'email' => $employee_email,
                'designation' => $employee_designation,
                'permissions' => json_decode($permissions)
            ],
            'Token' => $token,
            'statusCode' => 200,
            'message' => 'Successfully authenticate user.'
        ], Response::HTTP_OK)
            ->cookie(
                env("COOKIE_NAME"), 
                json_encode(['token' => $token]), 
                env("COOKIE_EXPIRY"), 
                '/', 
                env("SESSION_DOMAIN"), 
                false);
    }

    public function Signin(Request $request)
    {
        try {
            $LoginResponse = Helpers::umisPOSTrequest("sign-in", [
                'employee_id' => $request->employee_id,
                'password' => $request->password,
            ]);

            if (!array_key_exists('data', $LoginResponse) && array_key_exists('message', $LoginResponse)) {
                if ($LoginResponse['message'] == "expired-optional") {
                    return response()->json([
                        'message' => 'Password Expired. Please redirect to UMIS to change or manage your password',
                        'responseData' => [],
                        'statusCode' => 307
                    ]);
                }
            }

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
                    $expiry = strtotime(date('Y-m-d H:i:s', strtotime($AccessToken->first()->last_used_at . ' +' . env("TOKEN_EXPIRY_PAYROLL") . " minutes")));
                    // $expiry = strtotime(date('Y-m-d H:i:s', strtotime($AccessToken->first()->last_used_at . ' +' . env("TOKEN_EXPIRY_PAYROLL") . " minutes")));
                    $current = strtotime(date('Y-m-d H:i:s'));
                    if ($expiry <= $current) {
                        //EXPIRED
                        //RENEW TOKEN AND LASTUSED
                        $AccessToken->update([
                            'token' => $generatedToken,
                            'permissions' => encrypt($data, true),
                            'last_used_at' => now(),
                        ]);
                    }
                    //RENEW LASTUSED ONLY
                    $AccessToken->update([
                        'token' => $generatedToken,
                        'permissions' => encrypt($data, true),
                        'last_used_at' => now(),
                    ]);
                } else {

                    $AccessToken->update([
                        'token' => $generatedToken,
                        'permissions' => encrypt($data, true),
                        'last_used_at' => now(),
                    ]);
                }
            } else {
                //No cookie detected
                PersonalAccessToken::create([
                    'employee_id' => $data['employee_id'],
                    'name' => $data['name'],
                    'token' => $generatedToken,
                    'permissions' => encrypt($data, true),
                    'last_used_at' => now(),
                ]);
            }

            Logging::RecordTransaction([
                'module' => "UMIS/Authentication",
                'action' => "Signin Success",
                'status' => 202,
                'serverResponse' => "Login Success",
                'affected_entity' => null,
                'remarks' => "Signin attempt successful."
            ]);

            return response()->json([
                'message' => 'Login Success',
                'responseData' => [],
                'Token' => $generatedToken,
                'statusCode' => 200
            ])->cookie(env("COOKIE_NAME"), json_encode(['token' => $generatedToken]), env("COOKIE_EXPIRY"), '/', env("SESSION_DOMAIN"), false);
            // ])->cookie(env("COOKIE_NAME"), json_encode(['token' => $generatedToken]), env("COOKIE_EXPIRY_PAYROLL"), '/', env("SESSION_DOMAIN"), false);

        } catch (\Throwable $th) {

            Logging::RecordTransaction([
                'module' => "UMIS/Authentication",
                'action' => "Signin Failed",
                'status' => 401,
                'serverResponse' => $th->getMessage(),
                'affected_entity' => null,
                'remarks' => "Signin attempt failed."
            ]);

            return response()->json([
                'message' => "Login failed \n You have entered an incorrect credentials ",
                'Response' => $th->getMessage()
            ], 401);
        }
    }

    public function ReAuthenticate()
    {
        return response()->json([
            'statusCode' => 200,
            'Data' => Token::UserInfo()
        ]);
    }

    public function validateSession()
    {
        return response()->json([
            'message' => "Successfully validate session."
        ],Response::HTTP_NO_CONTENT);   
    }

    public function destroy(Request $request)
    {

        return response()->json([
            'message' => "Successfully signout."
        ],Response::HTTP_NO_CONTENT)->cookie(env("COOKIE_NAME"), '', -1);;
    }
}
