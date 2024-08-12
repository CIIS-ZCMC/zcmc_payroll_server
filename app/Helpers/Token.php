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

    public static function myToken(){
        return json_decode(request()->cookie(env("COOKIE_NAME")))->token ?? null;
    }

     public static function UserInfo(){
     $token = self::myToken();
       if ($token) {
        $accessToken = PersonalAccessToken::where('token',$token)->first();
        return decrypt($accessToken->abilities);
      }
        return null;
    }

}
