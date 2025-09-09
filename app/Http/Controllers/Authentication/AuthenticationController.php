<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user;
        $authorization_pin = decrypt($user->authorization_pin);

        if ($authorization_pin != $request->authorization_pin) {
            return response()->json([
                'message' => 'Authentication failed',
                'statusCode' => 401,
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'message' => 'Authentication successful',
            'statusCode' => 200,
        ], Response::HTTP_OK);
    }
}
