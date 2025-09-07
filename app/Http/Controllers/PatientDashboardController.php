<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Service;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    public function __construct()
    {
    }

    /**
     * Display the patient dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = auth()->user();
        $patientProfile = $user->patientProfile;

        // Get upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $user->id)
            ->where('appointment_datetime', '>=', Carbon::now())
            ->where('status', 'scheduled')
            ->orderBy('appointment_datetime')
            ->limit(5)
            ->with(['service', 'employee'])
            ->get();

        // Get recent appointment history
        $recentAppointments = Appointment::where('patient_id', $user->id)
            ->where('appointment_datetime', '<', Carbon::now())
            ->orderBy('appointment_datetime', 'desc')
            ->limit(5)
            ->with(['service', 'employee'])
            ->get();

        // Get appointment statistics
        $totalAppointments = Appointment::where('patient_id', $user->id)->count();
        $completedAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', 'completed')->count();
        $cancelledAppointments = Appointment::where('patient_id', $user->id)
            ->where('status', 'cancelled')->count();

        return view('patient.dashboard', compact(
            'user',
            'patientProfile',
            'upcomingAppointments',
            'recentAppointments',
            'totalAppointments',
            'completedAppointments',
            'cancelledAppointments'
        ));
    }

    /**
     * Show appointment booking form
     */
    public function bookAppointment()
    {
        $services = Service::all();
        $user = auth()->user();

        return view('patient.book-appointment', compact('services', 'user'));
    }

    /**
     * Show patient appointments
     */
    public function appointments()
    {
        $user = auth()->user();

        $appointments = Appointment::where('patient_id', $user->id)
            ->orderBy('appointment_datetime', 'desc')
            ->with(['service', 'employee'])
            ->paginate(10);

        return view('patient.appointments', compact('appointments', 'user'));
    }

    /**
     * Show patient profile
     */
    public function profile()
    {
        $user = auth()->user();
        $patientProfile = $user->patientProfile;

        return view('patient.profile', compact('user', 'patientProfile'));
    }

    /**
     * Update patient profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $patientProfile = $user->patientProfile;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'emergency_contact_relationship' => 'nullable|string|max:100',
        ]);

        // Update user table
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        // Update patient profile if exists
        if ($patientProfile) {
            $patientProfile->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
                'emergency_contact_relationship' => $request->emergency_contact_relationship,
            ]);
        }

        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Get available employees for a service and date
     */
    public function getAvailableEmployees(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
        ]);

        $date = Carbon::parse($request->appointment_date);
        $dayOfWeek = $date->dayOfWeekIso;

        $availableEmployees = User::role('employee')
            ->whereHas('schedules', function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek);
            })
            ->get(['id', 'name']);

        return response()->json($availableEmployees);
    }

    /**
     * Get available time slots for an employee
     */
    public function getAvailableTimeSlots(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date|after:today',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->appointment_date);
        $dayOfWeek = $date->dayOfWeekIso;

        // Get employee schedule for the day
        $schedule = $employee->schedules()->where('day_of_week', $dayOfWeek)->first();

        if (!$schedule) {
            return response()->json(['error' => 'Employee not available on this day.'], 404);
        }

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $appointmentDuration = $service->duration_in_minutes;

        // Get existing appointments for the employee on that day
        $existingAppointments = Appointment::where('employee_id', $employee->id)
            ->whereDate('appointment_datetime', $date->toDateString())
            ->where('status', '!=', 'cancelled')
            ->get();

        $availableSlots = [];
        $currentTime = $startTime->copy();

        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($appointmentDuration);
            $isSlotAvailable = true;

            // Check for conflicts with existing appointments
            foreach ($existingAppointments as $appointment) {
                $existingStart = Carbon::parse($appointment->appointment_datetime);
                $existingEnd = $existingStart->copy()->addMinutes($appointment->duration_in_minutes);

                if ($currentTime->lt($existingEnd) && $slotEnd->gt($existingStart)) {
                    $isSlotAvailable = false;
                    break;
                }
            }

            if ($isSlotAvailable) {
                $availableSlots[] = [
                    'time' => $currentTime->format('H:i'),
                    'display' => $currentTime->format('g:i A'),
                ];
            }

            $currentTime->addMinutes(30); // 30-minute intervals
        }

        return response()->json($availableSlots);
    }

    /**
     * Store new appointment
     */
    public function storeAppointment(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'patient_notes' => 'nullable|string|max:1000',
        ]);

        $service = Service::findOrFail($request->service_id);
        $appointmentDateTime = Carbon::createFromFormat(
            'Y-m-d H:i',
            $request->appointment_date . ' ' . $request->appointment_time
        );

        // Double-check availability
        $existingAppointment = Appointment::where('employee_id', $request->employee_id)
            ->where('appointment_datetime', $appointmentDateTime)
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existingAppointment) {
            return back()->withErrors(['appointment_time' => 'This time slot is no longer available.']);
        }

        Appointment::create([
            'patient_id' => auth()->id(),
            'employee_id' => $request->employee_id,
            'service_id' => $service->id,
            'appointment_datetime' => $appointmentDateTime,
            'end_time' => $appointmentDateTime->copy()->addMinutes($service->duration_in_minutes),
            'duration_in_minutes' => $service->duration_in_minutes,
            'patient_notes' => $request->patient_notes,
            'status' => 'scheduled',
        ]);

        return redirect()->route('patient.appointments')
            ->with('success', 'Appointment booked successfully!');
    }
}
