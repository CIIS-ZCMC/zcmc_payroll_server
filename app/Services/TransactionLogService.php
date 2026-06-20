<?php

namespace App\Services;

use App\Models\TransactionLog;

class TransactionLogService
{
    public static function log(array $data)
    {
        TransactionLog::create([
            'module' => $data['module'] ?? null,
            'action' => $data['action'],
            'status' => $data['status'],
            'ip_address' => request()->ip(),
            'remarks' => $data['remarks'] ?? null,
            'serverResponse' => $data['serverResponse'] ? json_encode($data['serverResponse']) : null,
            'affected_entity' => $data['affected_entity'] ?? null,
            'employee_profile_id' => $data['employee_profile_id'] ?? null,
            'employee_number' => $data['employee_number'] ?? null,
            'name' => $data['name'] ?? 'System',
        ]);
    }
}
