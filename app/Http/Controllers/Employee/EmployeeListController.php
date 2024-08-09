<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use \App\Helpers\Helpers;
use \App\Helpers\Token;


class EmployeeListController extends Controller
{

    public function index(Request $request){

        return "ok";
    }

    public function AuthorizationPin(Request $request){
        $pincode = $request->pin;
        $user = Token::UserInfo();
        if($pincode == $user->authorization_pin){
            return response()->json([
                'message'=>'Access-Granted'
            ],200);
        }
        return response()->json([
            'message'=>'Access-Denied'
        ],401);
    }

}
