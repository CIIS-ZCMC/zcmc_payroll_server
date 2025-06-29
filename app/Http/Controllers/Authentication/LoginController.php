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

        if ($result['is_failed']) {
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
            'token' => $token,
            'statusCode' => 200,
            'message' => 'Successfully authenticate user.'
        ], Response::HTTP_OK)
            ->cookie(
                env("COOKIE_NAME"),
                json_encode(['token' => $token]),
                env("COOKIE_EXPIRY"),
                '/',
                env("SESSION_DOMAIN"),
                false
            );
    }
    
    public function destroy(Request $request)
    {

        return response()->json([
            'message' => "Successfully signout."
        ], Response::HTTP_NO_CONTENT)->cookie(env("COOKIE_NAME"), '', -1);
        ;
    }
}
