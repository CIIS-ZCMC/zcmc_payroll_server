<?php

namespace App\Models\UMIS;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->whereTime('first_in', '>=', '18:00') // Check if first_in is 6:00 PM or later
                        ->whereTime('first_out', '<=', '12:00'); // Check if first_out is 6:00 AM or earlier
                })->orWhere(function ($q) {
                    $q->whereTime('second_in', '>=', '18:00') // Check second_in for 6 PM
                        ->whereTime('second_out', '<=', '12:00'); // Check second_out for 6 AM
                });
            });

        // return $this->hasMany(DailyTimeRecords::class, 'biometric_id', 'biometric_id')
        //     ->whereTime('first_in', '>=', '18:00') // Check if first_in is 6:00 PM or later
        //     ->whereTime('first_out', '>=', '06:00') // Check if first_out is 6:00 AM or earlier
        //     ->orWhere(function ($query) {
        //         $query->whereTime('second_in', '>=', '18:00') // Check second_in for 6 PM
        //             ->whereTime('second_out', '>=', '06:00'); // Check second_out for 6 AM
        //     });
    }

    public function getAbsentDates($year, $month)
    {
        $generateDateRange = function ($from, $to) {
            $start = Carbon::parse($from);
            $end = Carbon::parse($to);
            $dates = [];
            while ($start->lte($end)) {
                $dates[] = $start->format('Y-m-d');
                $start->addDay();
            }
            return $dates;
        };

        $schedule_dates = $this->schedule->map(function ($s) {
            return Carbon::parse($s->date)->format('Y-m-d');
        })->unique();

        $leave_dates = collect($this->leaveApplications)
            ->where('status', 'received')
            ->flatMap(function ($l) use ($generateDateRange) {
                return $generateDateRange($l->date_from, $l->date_to);
            })
            ->unique();

        $ob_dates = collect($this->approvedOB)
            ->flatMap(function ($ob) use ($generateDateRange) {
                return $generateDateRange($ob->date_from, $ob->date_to);
            })
            ->unique();

        $ot_dates = collect($this->approvedOT)
            ->flatMap(function ($ot) use ($generateDateRange) {
                return $generateDateRange($ot->date_from, $ot->date_to);
            })
            ->unique();

        $dtr_dates = $this->employeeDtr->pluck('dtr_date')->map(function ($d) {
            return Carbon::parse($d)->format('Y-m-d');
        })->unique();

        $holiday_dates = Holiday::all()
            ->filter(function ($holiday) use ($month) {
                return Carbon::createFromFormat('m-d', $holiday->month_day)->format('m') == str_pad($month, 2, '0', STR_PAD_LEFT);
            })
            ->map(function ($holiday) use ($year) {
                return Carbon::createFromFormat('Y-m-d', $year . '-' . $holiday->month_day)->format('Y-m-d');
            })->unique();

        $covered_dates = $leave_dates
            ->merge($ob_dates)
            ->merge($ot_dates)
            ->merge($dtr_dates)
            ->merge($holiday_dates)
            ->unique();

        $absent_dates = $schedule_dates->reject(function ($d) use ($covered_dates) {
            return $covered_dates->contains($d);
        })->values();

        return [
            'dates' => $absent_dates,
            'count' => $absent_dates->count()
        ];
    }
}
