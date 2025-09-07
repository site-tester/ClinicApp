<?php
namespace App\Http\Controllers\Admin;

// use Backpack\CRUD\app\Http\Controllers\CrudController;
// use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Class AppointmentCrudController
 * @package App\Http\Controllers\Admin
 * @property-read \Backpack\CRUD\app\Library\CrudPanel\CrudPanel $crud
 */
class AppointmentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    /**
     * Configure the CrudPanel object.
     *
     * @return void
     */
    public function setup()
    {
        CRUD::setModel(\App\Models\Appointment::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/appointment');
        CRUD::setEntityNameStrings('appointment', 'appointments');

        // Check if the authenticated user has the 'patient' role
        $isPatient = backpack_user()->hasRole('Patient');

        // Limit access for patients
        if ($isPatient) {
            $this->crud->denyAccess(['update', 'delete', 'show']);
            $this->crud->allowAccess(['create', 'list']);
            $this->crud->addClause('where', 'patient_id', backpack_user()->id);
        } else {
            // For staff and admin, allow full access to manage appointments
            $this->crud->allowAccess(['create', 'update', 'delete', 'show']);
        }
    }

    public function showBookingForm()
    {
        $services = Service::all();
        $user     = auth()->user();

        return view('appointments.index', compact('services', 'user'));
    }

    /**
     * Define what happens when the List operation is loaded.
     *
     * @see  https://backpackforlaravel.com/docs/crud-operations-list
     * @return void
     */
    protected function setupListOperation()
    {
        CRUD::addColumn([
            'name'      => 'patient_id',
            'label'     => 'Patient',
            'type'      => 'relationship',
            'attribute' => 'name',
            'model'     => 'App\Models\User',
        ]);

        CRUD::addColumn([
            'name'           => 'employee_id',
            'label'          => 'Employee',
            'type'           => 'relationship',
            'attribute'      => 'name',
            'model'          => 'App\Models\User',
            'visibleInTable' => ! backpack_user()->hasRole('Patient'),
        ]);

        CRUD::addColumn([
            'name'      => 'service_id',
            'label'     => 'Service',
            'type'      => 'relationship',
            'attribute' => 'name',
            'model'     => 'App\Models\Service',
        ]);

        CRUD::addColumn([
            'name'  => 'appointment_datetime',
            'label' => 'Date & Time',
            'type'  => 'datetime',
        ]);

        CRUD::addColumn([
            'name'  => 'status',
            'label' => 'Status',
            'type'  => 'text',
        ]);

        // Add filters for staff/admin
        // if (!backpack_user()->hasRole('Patient')) {
        //     CRUD::addFilter([
        //         'name' => 'status',
        //         'type' => 'select',
        //         'label' => 'Status'
        //     ], function () {
        //         return ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'];
        //     }, function ($value) {
        //         $this->crud->addClause('where', 'status', $value);
        //     });

        //     CRUD::addFilter([
        //         'name' => 'employee_id',
        //         'type' => 'select',
        //         'label' => 'Employee'
        //     ], function () {
        //         return User::role('employee')->pluck('name', 'id')->toArray();
        //     }, function ($value) {
        //         $this->crud->addClause('where', 'employee_id', $value);
        //     });
        // }
    }

    /**
     * Define what happens when the Create operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operations-create
     * @return void
     */
    protected function setupCreateOperation()
    {
        $isPatient = backpack_user()->hasRole('Patient');

        if ($isPatient) {
            CRUD::setValidation([
                'service_id'           => 'required|exists:services,id',
                'employee_id'          => 'required|exists:users,id',
                'appointment_datetime' => 'required|date|after:now',
            ]);

            CRUD::addField([
                'name'  => 'patient_id',
                'type'  => 'hidden',
                'value' => backpack_user()->id,
            ]);

            CRUD::addField([
                'name'      => 'service_id',
                'label'     => 'Service',
                'type'      => 'select',
                'entity'    => 'service',
                'model'     => 'App\Models\Service',
                'attribute' => 'name',
            ]);

            CRUD::addField([
                'name'      => 'employee_id',
                'label'     => 'Employee',
                'type'      => 'select',
                'entity'    => 'employee',
                'model'     => 'App\Models\User',
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->role('employee')->get();
                }),
            ]);

            CRUD::addField([
                'name'  => 'date',
                'label' => 'Appointment Date',
                'type'  => 'date',
            ]);

            CRUD::addField([
                'name'    => 'time',
                'label'   => 'Appointment Time',
                'type'    => 'select_from_array',
                'options' => $this->generateTimeSlots(),
            ]);

            CRUD::addField([
                'name'  => 'patient_notes',
                'label' => 'Notes',
                'type'  => 'textarea',
            ]);
        } else {
            // Staff/admin view
            CRUD::setValidation([
                'patient_id'           => 'required',
                'employee_id'          => 'required',
                'service_id'           => 'required',
                'appointment_datetime' => 'required|date',
            ]);

            CRUD::addField([
                'name'      => 'patient_id',
                'label'     => 'Patient',
                'type'      => 'select',
                'entity'    => 'patient',
                'model'     => 'App\Models\User',
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->role('Patient')->get();
                }),
            ]);

            CRUD::addField([
                'name'      => 'employee_id',
                'label'     => 'Employee',
                'type'      => 'select',
                'entity'    => 'employee',
                'model'     => 'App\Models\User',
                'attribute' => 'name',
                'options'   => (function ($query) {
                    return $query->role('employee')->get();
                }),
            ]);

            CRUD::addField([
                'name'      => 'service_id',
                'label'     => 'Service',
                'type'      => 'select',
                'entity'    => 'service',
                'model'     => 'App\Models\Service',
                'attribute' => 'name',
            ]);

            CRUD::addField([
                'name'  => 'date',
                'label' => 'Appointment Date',
                'type'  => 'date',
            ]);

            // CRUD::addField([
            //     'name' => 'time',
            //     'label' => 'Appointment Time',
            //     'type' => 'time',
            // ]);
            CRUD::addField([
                'name'    => 'time',
                'label'   => 'Appointment Time',
                'type'    => 'select_from_array',
                'options' => $this->generateTimeSlots(),
            ]);

            CRUD::addField([
                'name'    => 'status',
                'label'   => 'Status',
                'type'    => 'select_from_array',
                'options' => ['scheduled' => 'Scheduled', 'completed' => 'Completed', 'cancelled' => 'Cancelled'],
                'default' => 'scheduled',
            ]);

            CRUD::addField([
                'name'  => 'patient_notes',
                'label' => 'Patient Notes',
                'type'  => 'textarea',
            ]);

            CRUD::addField([
                'name'  => 'employee_notes',
                'label' => 'Employee Notes',
                'type'  => 'textarea',
            ]);

            CRUD::addField([
                'name'    => 'duration_in_minutes',
                'label'   => 'Duration (minutes)',
                'type'    => 'number',
                'default' => 30,
            ]);
        }
    }

    /**
     * Define what happens when the Update operation is loaded.
     *
     * @see https://backpackforlaravel.com/docs/crud-operations-update
     * @return void
     */
    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store()
    {
        $isPatient = backpack_user()->hasRole('Patient');

        if ($isPatient) {
            // Patient-specific validation and store
            $request = $this->crud->getRequest();
            $request->validate([
                'service_id'           => 'required',
                'employee_id'          => 'required',
                'appointment_datetime' => 'required|date|after:now',
                'patient_notes'        => 'nullable|string',
            ]);

            $service             = Service::findOrFail($request->service_id);
            $appointmentDateTime = Carbon::parse($request->appointment_datetime);

            // Check if the chosen time slot is available
            $employeeSchedule = User::findOrFail($request->employee_id)
                ->employee_schedules()
                ->where('day_of_week', $appointmentDateTime->dayOfWeekIso)
                ->first();

            if (! $employeeSchedule) {
                return redirect()->back()->withErrors(['appointment_datetime' => 'Employee is not available on this day.'])->withInput();
            }

            $existingAppointments = Appointment::where('employee_id', $request->employee_id)
                ->whereDate('appointment_datetime', $appointmentDateTime->toDateString())
                ->get();

            $isAvailable = true;
            foreach ($existingAppointments as $appointment) {
                $existingStart     = Carbon::parse($appointment->appointment_datetime);
                $existingEnd       = $existingStart->copy()->addMinutes($appointment->duration_in_minutes);
                $newAppointmentEnd = $appointmentDateTime->copy()->addMinutes($service->duration_in_minutes);

                if ($appointmentDateTime->lt($existingEnd) && $newAppointmentEnd->gt($existingStart)) {
                    $isAvailable = false;
                    break;
                }
            }

            if (! $isAvailable) {
                return redirect()->back()->withErrors(['appointment_datetime' => 'The selected time slot is already booked.'])->withInput();
            }

            // Create the appointment
            $appointment = Appointment::create([
                'patient_id'           => backpack_user()->id,
                'employee_id'          => $request->employee_id,
                'service_id'           => $service->id,
                'appointment_datetime' => $appointmentDateTime,
                'duration_in_minutes'  => $service->duration_in_minutes,
                'patient_notes'        => $request->patient_notes,
                'status'               => 'scheduled',
            ]);

            return redirect()->route('appointment.index')->with('success', 'Appointment successfully booked!');
        } else {
            // Staff/admin store operation
            $request = $this->crud->validateRequest();

            // Set duration based on service
            $service = Service::findOrFail($request->service_id);
            $request->request->set('duration_in_minutes', $service->duration_in_minutes);

            $this->crud->create($request->all());

            return redirect()->route('appointment.index')->with('success', 'Appointment successfully created!');
        }
    }

    /**
     * Generate time slots in 30-minute increments.
     *
     * @return array
     */
    protected function generateTimeSlots(): array
    {
        $slots = [];
        $start = Carbon::createFromTime(0, 0, 0);
        $end   = Carbon::createFromTime(23, 59, 59);

        while ($start->lte($end)) {
            $slots[$start->format('H:i')] = $start->format('H:i A');
            $start->addMinutes(30);
        }

        return $slots;
    }
}
