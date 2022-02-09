<aside class="sidebar">
    <div class="sidebar-start">
        <div class="sidebar-head">
            <a href="/" class="logo-wrapper" title="Home">
                <span class="sr-only">Home</span>
                <span class="logo" aria-hidden="true"><img src="{{ asset('img/svg/orange.svg') }}"></span>
            </a>
            <button class="sidebar-toggle transparent-btn" title="Menu" type="button">
                <span class="sr-only">Toggle menu</span>
                <span class="icon menu-toggle" aria-hidden="true"></span>
            </button>
        </div>
        <div class="sidebar-body">
            <ul class="sidebar-body-menu">
                <li>
                    <a href="{{ route('home') }}">
                        <span class="icon bx bxs-home bx-sm" aria-hidden="true"></span>Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('home') }}">
                        <span class="icon bx bxs-book-reader bx-sm" aria-hidden="true"></span>Manual de usuario
                    </a>
                </li>
                {!! session()->has('menu') ? session('menu') : "" !!}
                
            </ul>
        </div>
    </div>
</aside>