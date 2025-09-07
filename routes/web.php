<?php
// routes/web.php - Complete updated file

use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\ServicesController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Spatie\Permission\Middleware\RoleMiddleware;

Route::get('/homepage', function () {
    return view('pages.landing');
})->name('homepage');

Route::prefix('services')->group(function () {
    // Main services page
    Route::get('/', [ServicesController::class, 'index'])->name('services.index');

    // Individual service details
    Route::get('/{id}', [ServicesController::class, 'show'])->name('services.show')->where('id', '[0-9]+');

    // Services by type (single or package)
    Route::get('/type/{type}', [ServicesController::class, 'getByType'])->name('services.by-type')->where('type', 'single|package');

    // API endpoint for services data
    Route::get('/api/data', [ServicesController::class, 'getServices'])->name('services.api.data');
});

// Fix the authentication routes - Add these FIRST before other routes
Route::get('/login', function () {
    return redirect()->route('backpack.auth.login');
})->name('login');

// Route::post('/patient/logout', 'App\Http\Controllers\Controller@logout')->name('patient.logout');
Route::post('patient/logout', function () {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/homepage');
})->name('patient.logout');

Route::get('/home', function () {
    if (backpack_auth()->check()) {
        if (backpack_auth()->user()->hasRole('Patient')) {
            return redirect()->route('patient.dashboard');
        }
        return redirect()->route('backpack.dashboard');
    }
    return redirect()->route('backpack.auth.login');
})->name('home');

// Include Backpack's custom authentication routes
Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => 'web',
], function () {

    // Override Backpack's authentication routes
    Route::get('login', function () {
        if (backpack_auth()->check()) {
            if (backpack_auth()->user()->hasRole('Patient')) {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('backpack.dashboard');
        }

        $data['title']    = trans('backpack::base.login');
        $data['username'] = config('backpack.base.authentication_column_name', 'email');
        return view(backpack_view('auth.login'), $data);
    })->name('backpack.auth.login');

    Route::post('login', function (\Illuminate\Http\Request $request) {
        $credentials = $request->only(config('backpack.base.authentication_column_name', 'email'), 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user(); // backpack_auth()->user();

            // Check user role and redirect accordingly
            if ($user && $user->hasRole('Patient')) {
                return redirect()->route('patient.dashboard');
            }

            return redirect()->route('backpack.dashboard');
        }

        return back()->withErrors([
            config('backpack.base.authentication_column_name', 'email') => 'The provided credentials do not match our records.',
        ])->onlyInput(config('backpack.base.authentication_column_name', 'email'));
    });

    Route::post('logout', function (\Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('backpack.auth.login');
    })->name('backpack.auth.logout');

    Route::get('logout', function (\Illuminate\Http\Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('backpack.auth.login');
    });
});

// Redirect root to appropriate dashboard based on role
Route::get('/', function () {
    if (backpack_auth()->check()) {
        if (backpack_auth()->user()->hasRole('Patient')) {
            return redirect()->route('patient.dashboard');
        }
        return redirect()->route('backpack.dashboard');
    }
    // return redirect()->route('backpack.auth.login');
    return redirect('/homepage');
});

// Patient-specific routes - Protected by auth and role middleware
Route::middleware([Authenticate::class])->group(function () {

    // Patient Dashboard Routes
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

        });

    // Redirect authenticated patients from admin routes
    Route::get('/admin', function () {
        if (backpack_auth()->user()->hasRole('Patient')) {
            return redirect()->route('patient.dashboard');
        }
        return redirect()->route('backpack.dashboard');
    });

});

// Override Backpack's post-login redirect - Make sure this comes BEFORE backpack routes
Route::get('/admin/dashboard', function () {

    dd(backpack_auth()->check(), backpack_user());

    if (backpack_auth()->check()) {
        return redirect()->route('backpack.dashboard');
    }

    if (! backpack_auth()->check()) {
        return redirect()->route('backpack.auth.login');
    }

    if (backpack_auth()->user()->hasRole('Patient')) {
        return redirect()->route('patient.dashboard');
    }

    return view(backpack_view('dashboard'));
})->middleware(config('backpack.base.middleware_key', 'admin'))->name('backpack.dashboard');

// Your existing Backpack admin routes
// Route::group([
//     'prefix'     => config('backpack.base.route_prefix', 'admin'),
//     'middleware' => array_merge(
//         (array) config('backpack.base.web_middleware', 'web'),
//         (array) config('backpack.base.middleware_key', 'admin')
//     ),
//     'namespace'  => 'App\Http\Controllers\Admin',
// ], function () {
//     // Include backpack routes
//     Route::crud('patient', 'PatientCrudController');
//     Route::crud('appointment', 'AppointmentCrudController');
//     Route::crud('inventory', 'InventoryCrudController');
//     Route::crud('inventory-category', 'InventoryCategoryCrudController');
//     Route::crud('inventory-movements', 'InventoryMovementsCrudController');

//     // Stock management routes
//     Route::get('inventory/{id}/manage-stock', 'App\Http\Controllers\Admin\InventoryMovementsCrudController@manageStock')->name('inventory.manage-stock');
//     Route::post('inventory/{id}/update-stock', 'App\Http\Controllers\Admin\InventoryMovementsCrudController@updateStock')->name('inventory.update-stock');
// });

// Attendance routes (for employees)
Route::middleware([Authenticate::class])->group(function () {
    Route::get('/attendance', [App\Http\Controllers\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/process', [App\Http\Controllers\AttendanceController::class, 'process'])->name('attendance.process');
});

// Public appointment booking (if you want to keep the original one)
// Route::middleware([Authenticate::class])->group(function () {
//     Route::get('/appointment-booking', [App\Http\Controllers\AppointmentBookingController::class, 'showForm'])->name('appointment.booking');
//     Route::post('/appointment-booking', [App\Http\Controllers\AppointmentBookingController::class, 'store'])->name('appointment.store');
//     Route::get('/appointment-booking/employees', [App\Http\Controllers\AppointmentBookingController::class, 'getAvailableEmployees']);
//     Route::get('/appointment-booking/times', [App\Http\Controllers\AppointmentBookingController::class, 'getAvailableTimes']);
// });

// Page Manager routes (if you're using Backpack's Page Manager) - Keep this at the END
Route::get('{page}/{subs?}', [App\Http\Controllers\PageController::class, 'index'])
    ->where(['page' => '^(((?=(?!admin))(?=(?!\/)).))*$', 'subs' => '.*'])
    ->name('pages');
