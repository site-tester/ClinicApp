<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class EmployeeProfile extends Model
{
    //
    protected $table = 'employee_profiles';
    protected $guarded = ['id'];

    // RELATIONSHIPS
    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }
}
