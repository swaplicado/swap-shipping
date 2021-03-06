<!-- ! Main nav -->
<nav class="main-nav--bg">
    <div class="container main-nav">
        <div class="main-nav-start">

        </div>
        <div class="main-nav-end">
            <button class="sidebar-toggle transparent-btn" title="Menu"
                type="button">
                <span class="sr-only">Toggle menu</span>
                <span class="icon menu-toggle--gray" aria-hidden="true"></span>
            </button>

            {{-- <button class="theme-switcher gray-circle-btn" type="button"
                title="Switch theme">
                <span class="sr-only">Switch theme</span>
                <i class="sun-icon" data-feather="sun" aria-hidden="true"></i>
                <i class="moon-icon" data-feather="moon" aria-hidden="true"></i>
            </button> --}}

            <div>{{ Auth::user()->username }}</div>

            <div class="nav-user-wrapper">
                <button href="##" class="nav-user-btn dropdown-btn" title="Mi perfil" type="button">
                    <span class="sr-only">Mi perfil</span>
                    <span class="nav-user-img">
                        <picture><source
                                srcset="{{ asset('img/avatar/profile2.png') }}"
                                type="image/webp"><img
                                src="{{ asset('img/avatar/profile2.png') }}"
                                alt="User
                                name"></picture>
                    </span>
                </button>
                <ul class="users-item-dropdown nav-user-dropdown dropdown">
                    <li>
                        <a href="{{ route('profile') }}">
                            <i data-feather="user" aria-hidden="true"></i>
                            <span>Mi Perfil</span>
                        </a>
                    </li>
                    <li>
                        <a class="danger" href="{{ route('logout') }}">
                            <i data-feather="log-out" aria-hidden="true"></i>
                            <span>Cerrar sesi??n</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>