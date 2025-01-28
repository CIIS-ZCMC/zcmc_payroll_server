<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    use HasFactory;

    protected $connection = 'mysql2'; // This is the connection name located in config/database.php

    protected $table = 'employee_profiles';

    protected $primaryKey = 'id';

    public function personalInformation()
    {
        return $this->belongsTo(PersonalInformation::class);
    }

    public function assignedArea()
    {
        return $this->hasOne(AssignArea::class)->latest();
    }

    public function employmentType()
    {
        return $this->belongsTo(EmploymentType::class);
    }

    public function dailyTimeRecords()
    {
        return $this->hasMany(DailyTimeRecords::class, 'biometric_id', 'biometric_id');
    }

    public function employeeDtr()
    {
        return $this->hasMany(DailyTimeRecords::class, 'biometric_id', 'biometric_id');
    }

    public function approvedCTO()
    {
        return $this->hasMany(CtoApplication::class)->where('status', 'approved');
    }

    public function approvedOB()
    {
        return $this->hasMany(OfficialBusiness::class)->where('status', 'approved');
    }

    public function approvedOT()
    {
        return $this->hasMany(OfficialTime::class)->where('status', 'approved');
    }

    public function receivedLeave()
    {
        return $this->hasMany(LeaveApplication::class)->where('status', 'received');
    }

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class, 'employee_profile_id');
    }

    public function employeeLeaveCredits()
    {
        return $this->hasMany(EmployeeLeaveCredit::class);
    }

    public function schedule()
    {
        return $this->belongsToMany(Schedule::class, 'employee_profile_schedule')->withPivot('id', 'employee_profile_id');
    }

    public function findDesignation()
    {
        $assign_area = $this->assignedArea;
        $designation = !isset($assign_area->plantilla_id) ? $assign_area->designation ?? "" : $assign_area->plantilla->designation ?? "";
        return $designation;
    }

    public function dtrInvalidEntry()
    {
        return $this->hasMany(DailyTimeRecords::class, 'biometric_id', 'biometric_id')
            ->whereNull('first_out')
            ->whereNull('second_in')
            ->whereNull('second_out');
    }


    public function nigthDuties()
    {
        return $this->hasMany(DailyTimeRecords::class, 'biometric_id', 'biometric_id')
            ->whereTime('first_in', '>=', '18:00') // Check if first_in is 6:00 PM or later
            ->whereTime('first_out', '>=', '06:00') // Check if first_out is 6:00 AM or earlier
            ->orWhere(function ($query) {
                $query->whereTime('second_in', '>=', '18:00') // Check second_in for 6 PM
                    ->whereTime('second_out', '>=', '06:00'); // Check second_out for 6 AM
            });
    }
}
