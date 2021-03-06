<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ env('APP_NAME', "SWAP-Shipping") }}</title>

    <!-- Favicon -->
    {{-- <link rel="shortcut icon" href="{{asset('img/svg/logo.svg')}}" type="image/x-icon"> --}}
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>

    <!-- Custom styles -->
    <link rel="stylesheet" href="{{ asset('css/Bootstrap/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/daterangepicker/daterangepicker.css') }}">
    @yield('headStyles')

    <!--- Scripts --->
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('js/Bootstrap/bootstrap.js') }}"></script>
    <script src="{{ asset('js/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('js/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/daterangepicker/daterangepicker.min.js') }}"></script>
    <script src="{{ asset('js/waitSwal/wait.js') }}"></script>
    @yield('headJs')
</head>
<style>
    .dataTables_wrapper {
        font-size: 0.7em;
    }

</style>
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
                    @if(session('message'))
                        <script>
                            msg = "<?php echo session('message'); ?>";
                            myIcon = "<?php echo session('icon'); ?>"

                            Swal.fire({
                                icon: myIcon,
                                title: msg
                            })
                        </script>
                    @endif
                    @if(session('notification') || isset($notification))
                        <script>
                            msg = "<?php echo !is_null(session('notification')) ? session('notification') : $notification; ?>";
                            Swal.fire({
                                position: 'top-end',
                                title: msg,
                                icon: 'info',
                                showConfirmButton: true,
                                allowOutsideClick: false
                            })
                        </script>
                        @php
                            session()->forget('notification');
                        @endphp
                    @endif
                    @yield('content')
                </div>
            </main>
            <br>
            @include('layouts.footer')
        </div>
    </div>

    <!-- Chart library -->
    <script src="{{ asset('js/plugins/chart.min.js') }}"></script>
    <!-- Icons library -->
    <script src="{{ asset('js/plugins/feather.min.js') }}"></script>
    <!-- Custom scripts -->
    <script src="{{ asset('js/script.js') }}"></script>

    <script src="{{ asset('js/myapp/gui/SGui.js') }}"></script>

    @yield('scripts')
</body>

</html>