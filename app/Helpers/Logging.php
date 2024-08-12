<?php
namespace App\Helpers;


use Illuminate\Support\Facades\Log;
use App\Models\TransactionLog;
use App\Models\EmployeeList;


class Logging
{

public static function UserIPaddress(){
    return request()->ip();
}

public static function createRemarks($message,$serverReposnse){
    return "[$message ]  \n Server: ".$serverReposnse;
}

   /*
            Logging::RecordTransaction([
                'module'=>"UMIS/Authentication",
                'action'=>"Signin Failed",
                'status'=>401,
                'serverResponse'=>$th->getMessage(),
                'affected_entity'=>null,
                'remarks'=>"Signin attempt failed."
            ]);

    */
public static function RecordTransaction($data){

    $empdata = [];
    if(request()->user){
        $empdata = request()->user;
    }else {

        $userData = EmployeeList::where('employee_number',request()->employee_id)->first();
        if($userData){
            $empdata =[
                'employee_profile_id'=>$userData->employee_profile_id,
                'name'=>"$userData->lastname $userData->first_name $userData->middle_name",
                'employeeid'=>$userData->employee_number,
            ];
        }else {
            $empdata =[
                'employee_profile_id'=>0,
                'employeeid'=>request()->employee_id,
                'name'=>"Unknown Credentials"
            ];
        }


    }

    $data = array_merge([
        'ip_address'=> self::UserIPaddress(),
        'employee_profile_id'=>$empdata['employee_profile_id'],
        'employee_number'=>$empdata['employeeid'],
        'name'=> $empdata['name']
    ],$data);
    TransactionLog::create($data);
}

}
