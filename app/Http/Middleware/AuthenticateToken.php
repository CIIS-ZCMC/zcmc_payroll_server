<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Token;
use Illuminate\Http\Response;
use App\Models\PersonalAccessToken;
use Illuminate\Support\Facades\Log;

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


            $request->merge(['user' => Token::UserInfo()]);
            return $next($request);
        } catch (\Throwable $th) {
            Log::channel('code')->error($th);
            return response()->json(["data" => Token::myToken(), 'message' => 'un-authorized', 'response' => $th->getMessage(), 'addr' => ' Middleware/AuthenticateToken'], Response::HTTP_UNAUTHORIZED);
        }
    }
}
