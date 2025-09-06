<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AppointmentBookingController extends Controller
{
    /**
     * Show the appointment booking form.
     *
     * @return \Illuminate\View\View
     */
    public function showForm()
    {
        $services = Service::all();
        $user = auth()->user();

        return view('appointments.appointment_booking', compact('services', 'user'));
    }

    /**
     * Get available employees based on service and date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableEmployees(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
        ]);

        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->appointment_date);
        $dayOfWeek = $date->dayOfWeekIso; // 1 for Monday, 7 for Sunday

        // Find employees with a schedule on the selected day
        $availableEmployees = User::role('employee')
            ->whereHas('employee_schedules', function ($query) use ($dayOfWeek) {
                $query->where('day_of_week', $dayOfWeek);
            })
            ->get();

        return response()->json($availableEmployees);
    }

    /**
     * Get available time slots for a specific employee and date.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
        ]);

        $employee = User::findOrFail($request->employee_id);
        $service = Service::findOrFail($request->service_id);
        $date = Carbon::parse($request->appointment_date);
        $dayOfWeek = $date->dayOfWeekIso;

        $schedule = $employee->employee_schedules()->where('day_of_week', $dayOfWeek)->first();

        if (!$schedule) {
            return response()->json(['error' => 'Employee not available on this day.'], 404);
        }

        $startTime = Carbon::parse($schedule->start_time);
        $endTime = Carbon::parse($schedule->end_time);
        $appointmentDuration = $service->duration_in_minutes;

        $existingAppointments = Appointment::where('employee_id', $employee->id)
            ->whereDate('appointment_datetime', $date->toDateString())
            ->get();

        $availableTimes = [];
        $currentTime = $startTime->copy();

        while ($currentTime->lt($endTime)) {
            $slotEnd = $currentTime->copy()->addMinutes($appointmentDuration);
            $isSlotAvailable = true;

            // Check for overlap with existing appointments
            foreach ($existingAppointments as $appointment) {
                $existingStart = Carbon::parse($appointment->appointment_datetime);
                $existingEnd = $existingStart->copy()->addMinutes($appointment->duration_in_minutes);

                if (
                    ($currentTime->gte($existingStart) && $currentTime->lt($existingEnd)) ||
                    ($slotEnd->gt($existingStart) && $slotEnd->lte($existingEnd)) ||
                    ($currentTime->lt($existingStart) && $slotEnd->gt($existingEnd))
                ) {
                    $isSlotAvailable = false;
                    break;
                }
            }

            if ($isSlotAvailable) {
                $availableTimes[] = $currentTime->format('H:i');
            }

            $currentTime->addMinutes($appointmentDuration);
        }

        return response()->json($availableTimes);
    }

    /**
     * Store a newly created appointment in storage.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'employee_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'patient_notes' => 'nullable|string',
        ]);

        $service = Service::findOrFail($request->service_id);
        $appointmentDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->appointment_date . ' ' . $request->appointment_time);

        Appointment::create([
            'patient_id' => auth()->user()->id,
            'employee_id' => $request->employee_id,
            'service_id' => $service->id,
            'appointment_datetime' => $appointmentDateTime,
            'duration_in_minutes' => $service->duration_in_minutes,
            'patient_notes' => $request->patient_notes,
            'status' => 'scheduled',
        ]);

        return redirect()->back()->with('success', 'Appointment successfully booked!');
    }
}
