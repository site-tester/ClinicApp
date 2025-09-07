<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        //Maternity & Delivery Services
        Service::create([
            'name' => 'Pregnancy test',
            'description' => 'A test to determine ',
            'duration_in_minutes' => 15,
            'price' => 20.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Postpartum Checkup',
            'description' => 'A comprehensive postpartum checkup to ensure the health and well-being of both mother and baby after childbirth.',
            'duration_in_minutes' => 45,
            'price' => 100.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Normal Spontaneous Package',
            'description' => 'A comprehensive package for normal spontaneous deliveries, including prenatal care, delivery, and postpartum care.',
            'duration_in_minutes' => 120,
            'price' => 1500.00,
            'type' => 'package',
        ]);

        Service::create([
            'name' => 'Prenatal Checkup',
            'description' => 'A comprehensive prenatal checkup to monitor the health and development of the baby and the well-being of the mother during pregnancy.',
            'duration_in_minutes' => 30,
            'price' => 75.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Ultrasound Scan',
            'description' => 'A non-invasive imaging test that uses sound waves to create images of the inside of the body, commonly used during pregnancy to monitor fetal development.',
            'duration_in_minutes' => 30,
            'price' => 150.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Newborn Screening Test',
            'description' => 'A series of tests performed on newborns to screen for certain genetic, metabolic, hormonal, and functional conditions that may not be apparent at birth but can cause serious health problems if not detected and treated early.',
            'duration_in_minutes' => 20,
            'price' => 50.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Newborn Hearing Test',
            'description' => 'A screening test performed on newborns to assess their hearing ability and identify any potential hearing loss or issues that may require further evaluation and intervention.',
            'duration_in_minutes' => 15,
            'price' => 40.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Newborn Package',
            'description' => 'A comprehensive package for newborn care, including initial assessments, screenings, and essential health services for the baby\'s well-being.',
            'duration_in_minutes' => 60,
            'price' => 300.00,
            'type' => 'package',
        ]);

        Service::create([
            'name' => 'Immunization',
            'description' => 'A service that provides vaccinations to protect against various infectious diseases, promoting overall health and immunity.',
            'duration_in_minutes' => 15,
            'price' => 25.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Ear Piercing',
            'description' => 'A service that involves creating a small hole in the earlobe to accommodate earrings, typically performed in a safe and hygienic manner.',
            'duration_in_minutes' => 10,
            'price' => 30.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Family Planning Consultation',
            'description' => 'A consultation service that provides information and guidance on various family planning methods and options to help individuals and couples make informed decisions about their reproductive health.',
            'duration_in_minutes' => 30,
            'price' => 50.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'DMPSA (Injectable Contraceptive)',
            'description' => 'A long-acting injectable contraceptive that provides effective birth control for up to three months, helping to prevent unintended pregnancies.',
            'duration_in_minutes' => 15,
            'price' => 75.00,
            'type' => 'single',
        ]);

        Service::create([
            'name' => 'Subdermal Implant',
            'description' => 'A small, flexible rod that is inserted under the skin of the upper arm to provide long-term contraception for up to three years, offering a convenient and effective birth control option.',
            'duration_in_minutes' => 30,
            'price' => 200.00,
            'type' => 'single',
        ]);
    }
}
