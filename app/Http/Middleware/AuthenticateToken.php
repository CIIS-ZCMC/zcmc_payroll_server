<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;
use App\Helpers\Token;
use Symfony\Component\HttpFoundation\Response;
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
            $token = $this->extractToken($request);

            if (!$token) {
                return response()->json(['message' => 'Unauthorized. Token missing.'], Response::HTTP_UNAUTHORIZED);
            }

            $userSession = PersonalAccessToken::where('token', $token)
                ->where('expire_at', '>', now())
                ->first();

            if (!$userSession) {
                return response()->json(['message' => 'Unauthorized. Invalid or expired token.'], Response::HTTP_UNAUTHORIZED);
            }

            $request->merge(['user' => $userSession]);
            return $next($request);
        } catch (\Throwable $th) {
            return response()->json(["data" => Token::myToken(), 'message' => 'un-authorized', 'response' => $th->getMessage(), 'addr' => ' Middleware/AuthenticateToken'], Response::HTTP_UNAUTHORIZED);
        }
    }

    private function extractToken(Request $request)
    {
        if ($request->bearerToken()) {
            return $request->bearerToken();
        }

        if ($request->has('token')) {
            return $request->get('token');
        }

        if ($request->cookie(env('COOKIE_NAME'))) {
            $cookie = json_decode($request->cookie(env('COOKIE_NAME')), true);
            return $cookie['token'] ?? null;
        }

        return null;
    }
}
