<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Appointment;
use App\Models\EmployeeSchedule;
use App\Models\PatientProfile;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use CrudTrait;
    use HasRoles;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function patientAppointments()
    {
        return $this->hasMany(Appointment::class, 'patient_id');
    }

// A user (as employee) can have many appointments
    public function employeeAppointments()
    {
        return $this->hasMany(Appointment::class, 'employee_id');
    }

// A user (as employee) can have a schedule
    public function schedules()
    {
        return $this->hasMany(EmployeeSchedule::class, 'employee_id');
    }

// Patient profile relationship (already exists)
    public function patientProfile()
    {
        return $this->hasOne(PatientProfile::class);
    }
}
