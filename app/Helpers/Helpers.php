<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class Helpers
{
    public static function registerSystemLogs($request, $moduleID, $status, $remarks)
    {
        $ip = $request->ip();
        $user = $request->user;
        $permission = $request->permission;
        list($module, $action) = explode(' ', $permission);

        return [
            'employee_profile_id' => $user->id,
            'module_id' => $moduleID,
            'action' => $action,
            'module' => $module,
            'status' => $status,
            'remarks' => $remarks,
            'ip_address' => $ip
        ];
    }

    public static function errorLog($controller, $module, $errorMessage)
    {
        Log::channel('custom-error')->error($controller . ' Controller [' . $module . ']: message: ' . $errorMessage);
    }
}