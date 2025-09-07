<?php
namespace App\Http\Controllers\Auth;

use Backpack\CRUD\app\Http\Controllers\Auth\LoginController as BackpackLoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends BackpackLoginController
{
    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        // Check if user has Patient role
        // if ($user->hasRole('Patient')) {
        //     return redirect()->route('patient.dashboard');
        // }

        if ($user && $user->hasRole('Patient')) {
            // redirect patients to patient dashboard
            return redirect()->intended(route('patient.dashboard'));
        }

        // For admin/staff users, redirect to admin dashboard
        return redirect()->intended(route('backpack.dashboard'));
    }

    /**
     * Get the post-register / post-login redirect path.
     *
     * @return string
     */
    public function redirectPath()
    {
        if (method_exists($this, 'redirectTo')) {
            return $this->redirectTo();
        }

        return property_exists($this, 'redirectTo') ? $this->redirectTo : config('backpack.base.route_prefix', 'admin') . '/dashboard';
    }

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        // If user is already logged in, redirect appropriately
        if (backpack_auth()->check()) {
            if (backpack_user()->hasRole('Patient')) {
                return redirect()->route('patient.dashboard');
            }
            return redirect()->route('backpack.dashboard');
        }

        $this->data['title']    = trans('backpack::base.login'); // set the page title
        $this->data['username'] = $this->username();

        return view(backpack_view('auth.login'), $this->data);
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('homepage');
    }
}
