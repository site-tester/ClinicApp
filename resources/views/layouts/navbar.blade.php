<nav class="navbar navbar-expand-md navbar-dark shadow-sm bg-text-theme"sticky-top>
    <div class="container">
        <a class="navbar-brand pe-5" href="{{ url('/') }}">
            <img src="{{ asset('images/logo.png') }}" alt="logo image" width="35">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse row px-1 px-md-auto justify-content-between" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="col navbar-nav ms-auto border-start border-white ps-3">
                @guest
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Route::currentRouteName() === 'landing' ? 'active fw-bolder' : '' }}"
                            href="/">Home</a></li>
                    <li class="nav-item"><a class="btn mx-1 fw-semibold {{ Request::is('about-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/about-us') }}">About</a></li>
                    <li class="nav-item"><a class="btn mx-1 fw-semibold {{ Request::is('contact-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/contact-us') }}">Contact</a></li>
                @else
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Route::currentRouteName() === 'landing' ? 'active fw-bolder' : '' }}"
                            href="/">Home</a></li>
                    <li class="nav-item"><a class="btn mx-1 fw-semibold {{ Request::is('about-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/about-us') }}">About</a></li>
                    <li class="nav-item"><a class="btn mx- fw-semibold1 {{ Request::is('contact-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/contact-us') }}">Contact</a></li>

                @endauth

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="col-3 navbar-nav ms-auto float-end">
                <!-- Authentication Links -->
                @guest
                    {{-- @if (Route::has('login')) --}}
                        <li class="nav-item">
                            <a class="nav-link text-black fw-semibold" href="{{ route('backpack.auth.login') }}">{{ __('Login') }}</a>
                        </li>
                    {{-- @endif --}}

                    {{-- @if (Route::has('register')) --}}
                        <li class="nav-item">
                            <a class="nav-link text-black fw-semibold" href="{{ route('backpack.auth.register') }}">{{ __('Sign Up') }}</a>
                        </li>
                    {{-- @endif --}}
                @else
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle mx-2" href="#" role="button" data-bs-toggle="dropdown"
                            aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                            {{-- @if (Auth::user()->unreadNotifications->count() > 0)
                                <span class="text-bg-danger badge  text-center">
                                    !
                                </span>
                            @endif --}}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            @hasrole('Client')
                                <li>
                                    <a href="{{ route('profile') }}" class="dropdown-item"><i
                                            class="bi bi-person-square"></i>&nbsp;
                                        Profile</a>
                                </li>

                                {{-- Notif --}}
                                {{-- @php
                                    $unreadMyDashboardNotification = Auth::user()
                                        ->unreadNotifications()
                                        ->where('data->title', 'My Dashboard Notification');


                                    $unreadDonationRequestNotification = Auth::user()
                                        ->unreadNotifications()
                                        ->where('data->title', 'Donation Request Notification')
                                        ->where('data->notified_to', Auth::user()->id);

                                @endphp --}}
                                {{-- <li>
                                    <a href="{{ route('my.donation') }}" class="dropdown-item"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="14.41" height="14.41"
                                            viewBox="0 0 14 14">
                                            <path fill="currentColor" fill-rule="evenodd"
                                                d="M9.03.22a.75.75 0 1 0-1.06 1.06l.72.72H6.5a.5.5 0 0 0-.5.5v5a.5.5 0 0 0 .5.5h2.875V3.5a.625.625 0 1 1 1.25 0V8H13.5a.5.5 0 0 0 .5-.5v-5a.5.5 0 0 0-.5-.5h-2.19l.72-.72A.75.75 0 0 0 10.97.22l-.97.97zM1.843 7H0v4l1.828 1.828A4 4 0 0 0 4.657 14H10.5a1.5 1.5 0 0 0 0-3H7.723a2.11 2.11 0 0 1-3.515.892l-1.45-1.45a.625.625 0 1 1 .884-.884l1.45 1.45a.86.86 0 0 0 1.306-1.11L4.672 8.172A4 4 0 0 0 1.843 7"
                                                clip-rule="evenodd" />
                                        </svg>&nbsp;
                                        My Dashboard
                                        {{-- @if ($unreadMyDashboardNotification->exists())
                                            <span class="text-bg-danger badge text-center">
                                                !
                                            </span>
                                        @endif --}}
                                </a>
                        </li>

                        <li>
                            <a href="{{ route('donation.request') }}" class="dropdown-item">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="bi bi-chat-right-heart-fill" viewBox="0 0 16 16">
                                    <path
                                        d="M16 2a2 2 0 0 0-2-2H2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h9.586a1 1 0 0 1 .707.293l2.853 2.853a.5.5 0 0 0 .854-.353zM8 3.993c1.664-1.711 5.825 1.283 0 5.132-5.825-3.85-1.664-6.843 0-5.132" />
                                </svg>&nbsp;
                                Donation Request
                                @if ($unreadDonationRequestNotification->exists())
                                    <span class="text-bg-danger badge text-center">
                                        !
                                    </span>
                                @endif

                            </a>
                        </li> --}}

                        {{-- <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li class="dropdown-item text-center">
                                    <a class=" btn btn-success text-nowrap fw-bolder mx-2" href="/donate-now">Donate Now</a>
                                <li> --}}
                    @endhasrole
                    {{--  --}}
                    @hasanyrole('Barangay Representative|Content Manager|Muncipal Admin')
                        <li class="dropdown-item text-center">
                            <a class=" btn btn-success text-nowrap fw-bolder mx-2" href="/admin">Admin Dashboard</a>
                        <li>
                        @endhasanyrole
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('logout') }}"
                            onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>&nbsp;
                            {{ __('Logout') }}
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </ul>
                </li>
            @endguest
            </ul>
        </div>
    </div>
</nav>
