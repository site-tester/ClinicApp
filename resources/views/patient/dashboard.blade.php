@extends('patient.layouts.app')

@section('title', 'Patient Dashboard - FRYDT')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h1 class="mb-2">
                <i class="fas fa-hand-wave me-2"></i>Welcome back, {{ $user->name }}!
            </h1>
            <p class="mb-0 opacity-90">
                Manage your appointments and health records from your personal dashboard.
            </p>
        </div>
        <div class="col-md-4 text-end">
            <div class="d-flex align-items-center justify-content-end">
                <div class="me-3">
                    <small class="opacity-75">Today is</small><br>
                    <strong>{{ now()->format('l, F j, Y') }}</strong>
                </div>
                <i class="fas fa-calendar-day fa-2x opacity-75"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-primary">{{ $totalAppointments }}</div>
                <div class="stat-label">Total Appointments</div>
                <i class="fas fa-calendar-alt fa-2x text-primary opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-success">{{ $completedAppointments }}</div>
                <div class="stat-label">Completed</div>
                <i class="fas fa-check-circle fa-2x text-success opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-info">{{ $upcomingAppointments->count() }}</div>
                <div class="stat-label">Upcoming</div>
                <i class="fas fa-clock fa-2x text-info opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <div class="card">
            <div class="card-body stat-card">
                <div class="stat-number text-warning">{{ $cancelledAppointments }}</div>
                <div class="stat-label">Cancelled</div>
                <i class="fas fa-times-circle fa-2x text-warning opacity-25 mt-2"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Upcoming Appointments -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check me-2"></i>Upcoming Appointments
                </h5>
                <a href="{{ route('patient.book-appointment') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-plus me-1"></i>Book New
                </a>
            </div>
            <div class="card-body">
                @if($upcomingAppointments->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Service</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($upcomingAppointments as $appointment)
                                <tr>
                                    <td>
                                        <strong>{{ $appointment->appointment_datetime->format('M j, Y') }}</strong><br>
                                        <small class="text-muted">{{ $appointment->appointment_datetime->format('g:i A') }}</small>
                                    </td>
                                    <td>
                                        <i class="fas fa-stethoscope me-1 text-primary"></i>
                                        {{ $appointment->service->name }}
                                    </td>
                                    <td>{{ $appointment->employee->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ ucfirst($appointment->status) }}</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                            View All Appointments
                        </a>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-plus fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">No upcoming appointments</h6>
                        <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary mt-2">
                            <i class="fas fa-plus me-1"></i>Book Your First Appointment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions & Profile Summary -->
    <div class="col-lg-4 mb-4">
        <!-- Quick Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Quick Actions
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('patient.book-appointment') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-plus me-2"></i>Book Appointment
                    </a>
                    <a href="{{ route('patient.appointments') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list me-2"></i>View Appointments
                    </a>
                    <a href="{{ route('patient.profile') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-user-edit me-2"></i>Update Profile
                    </a>
                </div>
            </div>
        </div>

        <!-- Profile Summary -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-user me-2"></i>Profile Summary
                </h5>
            </div>
            <div class="card-body">
                @if($patientProfile)
                    <div class="mb-3">
                        <small class="text-muted d-block">Name</small>
                        <strong>{{ $user->name }}</strong>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Email</small>
                        <strong>{{ $user->email }}</strong>
                    </div>
                    @if($patientProfile->phone)
                    <div class="mb-3">
                        <small class="text-muted d-block">Phone</small>
                        <strong>{{ $patientProfile->phone }}</strong>
                    </div>
                    @endif
                    @if($patientProfile->gender)
                    <div class="mb-3">
                        <small class="text-muted d-block">Gender</small>
                        <strong>{{ $patientProfile->gender }}</strong>
                    </div>
                    @endif
                    @if($patientProfile->birth_date)
                    <div class="mb-3">
                        <small class="text-muted d-block">Age</small>
                        <strong>{{ \Carbon\Carbon::parse($patientProfile->birth_date)->age }} years old</strong>
                    </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="fas fa-user-plus fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-2">Complete your profile for better service</p>
                        <a href="{{ route('patient.profile') }}" class="btn btn-primary btn-sm">
                            Complete Profile
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Appointment History -->
@if($recentAppointments->count() > 0)
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history me-2"></i>Recent Appointment History
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Doctor/Staff</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentAppointments as $appointment)
                            <tr>
                                <td>{{ $appointment->appointment_datetime->format('M j, Y') }}</td>
                                <td>{{ $appointment->service->name }}</td>
                                <td>{{ $appointment->employee->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $appointment->status === 'completed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'info') }}">
                                        {{ ucfirst($appointment->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
