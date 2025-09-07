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
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Request::is('services') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/services') }}">Services</a></li>
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Request::is('about-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/about-us') }}">About</a></li>
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Request::is('contact-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/contact-us') }}">Contact</a></li>
                @else
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Route::currentRouteName() === 'landing' ? 'active fw-bolder' : '' }}"
                            href="/">Home</a></li>
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Request::is('services') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/services') }}">Services</a></li>
                    <li class="nav-item"><a
                            class="btn mx-1 fw-semibold {{ Request::is('about-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/about-us') }}">About</a></li>
                    <li class="nav-item"><a
                            class="btn mx- fw-semibold1 {{ Request::is('contact-us') ? 'active fw-bolder' : '' }}"
                            href="{{ url('/contact-us') }}">Contact</a></li>

                @endauth

            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="col-3 navbar-nav ms-auto float-end">
                <!-- Authentication Links -->
                @guest
                    {{-- @if (Route::has('login')) --}}
                    <li class="nav-item">
                        <a class="nav-link text-black fw-semibold"
                            href="{{ route('backpack.auth.login') }}">{{ __('Login') }}</a>
                    </li>
                    {{-- @endif --}}

                    {{-- @if (Route::has('register')) --}}
                    <li class="nav-item">
                        <a class="nav-link text-black fw-semibold"
                            href="{{ route('backpack.auth.register') }}">{{ __('Sign Up') }}</a>
                    </li>
                    {{-- @endif --}}
                @else
                    <li class="nav-item dropdown">
                        <a class="nav-link text-black dropdown-toggle mx-2" href="#" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }}
                        </a>

                        <ul class="dropdown-menu dropdown-menu-end">
                            @hasanyrole('Admin|Staff|Doctor', 'backpack')
                                <li class="dropdown-item text-center">
                                    <a class="btn btn-success text-nowrap fw-bolder mx-2"
                                        href="{{ route('backpack.dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                            @endhasanyrole

                            @hasrole('Patient', 'backpack')
                                <li class="dropdown-item text-center">
                                    <a class="btn btn-success text-nowrap fw-bolder mx-2"
                                        href="{{ route('patient.dashboard') }}">
                                        Dashboard
                                    </a>
                                </li>
                            @endhasrole

                            <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('patient.logout') }}"
                            onclick="event.preventDefault();
                                                    document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right"></i>&nbsp;
                            {{ __('Logout') }}
                        </a>
                    </li>
                    <form id="logout-form" action="{{ route('patient.logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </ul>
                </li>
            @endguest
            </ul>
        </div>
    </div>
</nav>
