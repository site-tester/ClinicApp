<?php

use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ServicesController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

// Public routes
Route::get('/homepage', function () {
    return view('pages.landing');
})->name('homepage');

// Services routes (public)
Route::prefix('services')->group(function () {
    Route::get('/', [ServicesController::class, 'index'])->name('services.index');
    Route::get('/{id}', [ServicesController::class, 'show'])->name('services.show')->where('id', '[0-9]+');
    Route::get('/type/{type}', [ServicesController::class, 'getByType'])->name('services.by-type')->where('type', 'single|package');
    Route::get('/api/data', [ServicesController::class, 'getServices'])->name('services.api.data');
});

// Redirect /login to admin login
Route::get('/login', function () {
    return redirect('/admin/login');
})->name('login');

// Root route - redirect based on authentication and role
Route::get('/', function () {
    if (backpack_auth()->check()) {
        $user = backpack_auth()->user();
        if ($user->hasRole('Patient')) {
            return redirect()->route('patient.dashboard');
        }
        return redirect('/admin/dashboard');
    }
    return redirect('/homepage');
})->name('home');

// Patient routes (authenticated patients only)
Route::middleware(['auth:backpack'])->group(function () {
    Route::middleware([RoleMiddleware::class . ':Patient'])
        ->prefix('patient')
        ->name('patient.')
        ->controller(PatientDashboardController::class)
        ->group(function () {

            // Dashboard
            Route::get('/dashboard', 'index')->name('dashboard');

            // Appointments
            Route::get('/appointments', 'appointments')->name('appointments');
            Route::get('/book-appointment', 'bookAppointment')->name('book-appointment');
            Route::post('/book-appointment', 'storeAppointment')->name('store-appointment');

            // AJAX routes for appointment booking
            Route::get('/api/available-employees', 'getAvailableEmployees')->name('api.available-employees');
            Route::get('/api/available-time-slots', 'getAvailableTimeSlots')->name('api.available-time-slots');

            // Profile
            Route::get('/profile', 'profile')->name('profile');
            Route::put('/profile', 'updateProfile')->name('update-profile');

            // Payments
            Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
            Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create');
            Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
            Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');

            // PayPal callback routes
            Route::get('/payments/success', [PaymentController::class, 'success'])->name('payment.success');
            Route::get('/payments/cancel', [PaymentController::class, 'cancel'])->name('payment.cancel');
        });
});

// Attendance routes (for employees)
Route::middleware(['auth:backpack', 'role:Employee|Doctor|Admin'])->group(function () {
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/process', [App\Http\Controllers\AttendanceController::class, 'process'])->name('attendance.process');
});

// Page Manager routes (if you're using Backpack's Page Manager) - Keep this at the END
Route::get('{page}/{subs?}', [App\Http\Controllers\PageController::class, 'index'])
    ->where(['page' => '^(((?=(?!admin))(?=(?!\/)).))*$', 'subs' => '.*'])
    ->name('pages');
