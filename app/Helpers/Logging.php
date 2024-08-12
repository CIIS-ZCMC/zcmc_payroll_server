<?php
namespace App\Helpers;


use Illuminate\Support\Facades\Log;
use App\Models\TransactionLog;
class Logging
{

public static function UserIPaddress(){
    return request()->ip();
}


   /*
    $data = [
        'module'=>,
        'action'=>,
        'status'=>,
        'remarks'=>,
    ];
    */

public static function RecordTransaction($data){

    $data = array_merge([
        'ip_address'=> self::UserIPaddress(),
        'employee_profile_id'=>request()->user['employee_profile_id'],
        'name'=> request()->user['name']
    ],$data);
    TransactionLog::create($data);
}

}
