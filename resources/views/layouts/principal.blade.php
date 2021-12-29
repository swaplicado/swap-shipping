<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', "SWAP-Shipping") }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="./img/svg/logo.svg" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom styles -->
    <link rel="stylesheet" href="{{ asset('css/Bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    @yield('headStyles')

    <!--- Scripts --->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/Bootstrap/bootstrap.js') }}"></script>
    @yield('headJs')
</head>

<body>
    <div class="layer"></div>
    <!-- ! Body -->
    <a class="skip-link sr-only" href="#skip-target">Skip to content</a>
    <div class="page-flex">
        <!-- ! Sidebar -->
        @yield('aside')
        <div class="main-wrapper">
            @yield('nav-up')
            <!-- ! Main -->
            <main class="main users chart-page" id="skip-target">
                <div class="container">
                    @yield('content')
                </div>
            </main>
            @yield('footer')
        </div>
    </div>
    @yield('scripts')
</body>

</html>