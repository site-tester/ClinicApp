<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class EmployeeSchedule extends Model
{
    protected $table = 'employee_schedules';
    protected $guarded = ['id'];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
    ];

    // RELATIONSHIPS
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    // HELPER METHODS
    public function getDayNameAttribute()
    {
        $days = [
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday'
        ];

        return $days[$this->day_of_week] ?? 'Unknown';
    }
}
