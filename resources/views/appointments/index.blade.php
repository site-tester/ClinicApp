<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book an Appointment</title>
    <!-- Bootstrap CSS from a CDN -->
    <link
        href="https://www.google.com/search?q=https://cdn.jsdelivr.net/npm/bootstrap%405.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous">
</head>

<body class="bg-light">

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm rounded-3">
                    <div class="card-header bg-primary text-white text-center rounded-top-3">
                        <h4 class="mb-0">Book an Appointment</h4>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <strong>Oops!</strong> There were some problems with your input.<br><br>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"
                                    aria-label="Close"></button>
                            </div>
                        @endif

                        <form id="appointment-form" action="{{ route('appointments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $user->id }}">

                            <div class="mb-3">
                                <label for="service" class="form-label">Select Service</label>
                                <select class="form-select rounded-pill" id="service" name="service_id" required>
                                    <option value="" selected disabled>Choose a service...</option>
                                    @foreach ($services as $service)
                                        <option value="{{ $service->id }}"
                                            data-duration="{{ $service->duration_in_minutes }}">{{ $service->name }}
                                            ({{ $service->duration_in_minutes }} mins)</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="date" class="form-label">Appointment Date</label>
                                <input type="date" class="form-control rounded-pill" id="date"
                                    name="appointment_date" min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="employee" class="form-label">Select Employee</label>
                                <select class="form-select rounded-pill" id="employee" name="employee_id" disabled
                                    required>
                                    <option value="" selected disabled>Select a service and date first...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="time" class="form-label">Select Time Slot</label>
                                <select class="form-select rounded-pill" id="time" name="appointment_time" disabled
                                    required>
                                    <option value="" selected disabled>Select an employee first...</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="patient_notes" class="form-label">Notes for the Clinic (optional)</label>
                                <textarea class="form-control rounded-3" id="patient_notes" name="patient_notes" rows="3"></textarea>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg rounded-pill">Book
                                    Appointment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS from a CDN -->

    <script
        src="https://www.google.com/search?q=https://cdn.jsdelivr.net/npm/bootstrap%405.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const serviceSelect = document.getElementById('service');
            const dateInput = document.getElementById('date');
            const employeeSelect = document.getElementById('employee');
            const timeSelect = document.getElementById('time');

            // Function to fetch available employees based on service and date
            function fetchEmployees() {
                const serviceId = serviceSelect.value;
                const date = dateInput.value;
                if (!serviceId || !date) {
                    return;
                }

                // Reset employee and time fields
                employeeSelect.disabled = true;
                employeeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                selected disabled & gt;
                Loading employees... & lt;
                /option&gt;&#39;;
                timeSelect.disabled = true;
                timeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                selected disabled & gt;
                Select an employee first... & lt;
                /option&gt;&#39;;

                fetch(`/appointments/available-employees?service_id=${serviceId}&amp;appointment_date=${date}`)
                    .then(response = & gt; response.json())
                    .then(employees = & gt;
                    {
                        employeeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                        selected disabled & gt;
                        Choose an employee... & lt;
                        /option&gt;&#39;;
                        if (employees.length & gt; 0) {
                            employees.forEach(employee = & gt;
                            {
                                const option = document.createElement( & #39;option&# 39;);
                                option.value = employee.id;
                                option.textContent = employee.name;
                                employeeSelect.appendChild(option);
                            });
                            employeeSelect.disabled = false;
                        } else {
                            employeeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                            selected disabled & gt;
                            No employees available. & lt;
                            /option&gt;&#39;;
                        }
                    })
                    .catch(error = & gt;
                    {
                        console.error( & #39;Error fetching employees:&# 39;, error);
                        employeeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                        selected disabled & gt;
                        Failed to load employees. & lt;
                        /option&gt;&#39;;
                    });
            }

            // Function to fetch available time slots
            function fetchTimeSlots() {
                const employeeId = employeeSelect.value;
                const serviceId = serviceSelect.value;
                const date = dateInput.value;
                if (!employeeId || !serviceId || !date) {
                    return;
                }

                // Reset time field
                timeSelect.disabled = true;
                timeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                selected disabled & gt;
                Loading time slots... & lt;
                /option&gt;&#39;;

                fetch(
                        `/appointments/available-times?employee_id=${employeeId}&amp;service_id=${serviceId}&amp;appointment_date=${date}`)
                    .then(response = & gt; response.json())
                    .then(times = & gt;
                    {
                        timeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                        selected disabled & gt;
                        Choose a time... & lt;
                        /option&gt;&#39;;
                        if (times.length & gt; 0) {
                            times.forEach(time = & gt;
                            {
                                const option = document.createElement( & #39;option&# 39;);
                                option.value = time;
                                option.textContent = time;
                                timeSelect.appendChild(option);
                            });
                            timeSelect.disabled = false;
                        } else {
                            timeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                            selected disabled & gt;
                            No time slots available. & lt;
                            /option&gt;&#39;;
                        }
                    })
                    .catch(error = & gt;
                    {
                        console.error( & #39;Error fetching time slots:&# 39;, error);
                        timeSelect.innerHTML = & #39;&lt;option value= & quot; & quot;
                        selected disabled & gt;
                        Failed to load time slots. & lt;
                        /option&gt;&#39;;
                    });
            }

            serviceSelect.addEventListener( & #39;change&# 39;, fetchEmployees);
            dateInput.addEventListener( & #39;change&# 39;, fetchEmployees);
            employeeSelect.addEventListener( & #39;change&# 39;, fetchTimeSlots);
        });
    </script>

</body>

</html>
