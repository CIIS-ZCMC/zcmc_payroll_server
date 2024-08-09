<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Helpers\Token;
use Illuminate\Http\Response;
use App\Models\PersonalAccessToken;

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
            $accessToken = PersonalAccessToken::where("token",$token);

            return response()->json(["data" => $token, 'message' => 'un-authorized'], Response::HTTP_UNAUTHORIZED);



            return $next($request);
        } catch (\Throwable $th) {
            return response()->json(["data" => $token, 'message' => 'un-authorized','response'=>$th->getMessage()], Response::HTTP_UNAUTHORIZED);


        }


    }
}
