<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="max-height: 100%">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

        <link rel="stylesheet" href="/css/colors.css">

    <!-- Scripts -->
    @yield('css')
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <style>
        /* Base theme variables */
        :root {
            --theme-bg: #b2ffb2;
            --theme-text: #333333;
            --theme-border: #99e699;
            --theme-hover: #99e699;
            --theme-focus: #66cc66;
        }

        * {
            color: var(--theme-text);
            font-size: 17px
        }

        /* Utility class */
        .bg-text-theme {
            background-color: var(--theme-bg);
            color: var(--theme-text);
        }

        /* General text color */
        .text-theme {
            color: var(--theme-text);
        }

        /* Buttons */
        .btn-theme {
            background-color: var(--theme-bg);
            color: var(--theme-text);
            border: 1px solid var(--theme-border);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .btn-theme:hover {
            background-color: var(--theme-hover);
            border-color: var(--theme-focus);
        }

        /* Inputs, textareas, selects */
        .input-theme,
        textarea,
        select {
            border: 1px solid var(--theme-border);
            background-color: #fff;
            color: var(--theme-text);
            padding: 0.5rem;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }

        .input-theme:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: var(--theme-focus);
            box-shadow: 0 0 0 3px rgba(102, 204, 102, 0.3);
        }

        /* Links */
        a.theme-link {
            color: var(--theme-text);
            text-decoration: none;
            border-bottom: 1px solid transparent;
            transition: all 0.2s ease-in-out;
        }

        a.theme-link:hover {
            color: var(--theme-focus);
            border-color: var(--theme-focus);
        }

        /* Tables */
        .table-theme th {
            background-color: var(--theme-bg);
            color: var(--theme-text);
        }

        .table-theme td {
            border: 1px solid var(--theme-border);
            padding: 0.5rem;
        }
    </style>
</head>

<body class="mh-100">
    <div id="app" class="mh-100">
        

        @include('layouts.navbar')

        <main class="py-4 ">
            @yield('content')
        </main>

        @include('layouts.footer')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.min.js"
        integrity="sha384-7qAoOXltbVP82dhxHAUje59V5r2YsVfBafyUDxEdApLPmcdhBPg1DKg1ERo0BZlK" crossorigin="anonymous">
    </script>
</body>
</html>
