<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Str;

class Token
{

    public static function generateToken()
    {
        return hash('sha256', Str::random(80));
    }

    // public static function myToken(){
    //     return json_decode(request()->cookie(env("COOKIE_NAME")))->token ?? request()->bearerToken();
    // }

    public static function myToken()
    {
        $cookieValue = request()->cookie(env("COOKIE_NAME_PAYROLL"));

        // Ensure $cookieValue is a string
        if (is_string($cookieValue)) {
            $decodedValue = json_decode($cookieValue);
            return $decodedValue->token ?? request()->bearerToken();
        }
        return request()->bearerToken();
    }

    public static function UserInfo()
    {
        $token = self::myToken();
        if ($token) {
            $accessToken = PersonalAccessToken::where('token', $token)->first();
            if ($accessToken) {
                return decrypt($accessToken->abilities);
            }
        }
        return null;
    }
}
