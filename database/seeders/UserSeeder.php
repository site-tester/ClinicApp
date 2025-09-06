<?php
namespace Database\Seeders;

use App\Models\PatientProfile;
use App\Models\EmployeeProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        User::create([
            'name'                => 'John Doe',
            'email'               => 'jdoe@email.com',
            'password'            => bcrypt('user123'),
            'registration_status' => 'Full',
            'remember_token'      => Str::random(60),
            'email_verified_at'   => now(),
        ]);

        User::create([
            'name'              => 'Mary Jane',
            'email'             => 'mjane@email.com',
            'password'          => bcrypt('user123'),
            'registration_status' => 'Semi',
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        PatientProfile::create([
            'user_id'                        => 1,
            'address'                        => '123 Main St, Cityville',
            'phone'                          => '093-456-7890',
            'birth_date'                     => '1990-01-01',
            'gender'                         => 'Male',
            'emergency_contact_name'         => 'Jane Doe',
            'emergency_contact_phone'        => '098-765-4321',
            'emergency_contact_relationship' => 'Sister',
            'philhealth_membership'          => 'Member',
            'philhealth_number'              => '1234-5678-9012',
            'image_path'                     => null,
        ]);

        User::create([
            'name'              => 'Clinic Admin',
            'email'             => 'admin@email.com',
            'password'          => bcrypt('admin123'),
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Clinic Employee',
            'email'             => 'employee@email.com',
            'password'          => bcrypt('staff123'),
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Clinic Doctor',
            'email'             => 'doctor@email.com',
            'password'          => bcrypt('doctor123'),
            'remember_token'    => Str::random(60),
            'email_verified_at' => now(),
        ]);

        EmployeeProfile::create([
            'employee_id' => 3,
            'position'    => 'Head Doctor',
            'hire_date'   => '2020-01-15',
            'gender'      => 'Male',
            'phone'       => '091-234-5678',
            'address'     => '456 Clinic Rd, Healthtown',
            'image_path'  => null,
            'pin'         => 123456,
        ]);
    }
}
