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
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bx-file bx-sm" aria-hidden="true"></span>Cartas Porte
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="{{ route('documents', 1) }}"><span class="icon bx bx-file-blank bx-sm" aria-hidden="true"></span>Pendientes</a>
                        </li>
                        <li>
                            <a href="{{ route('documents', 2) }}"><span class="icon bx bxs-file-blank bx-sm" aria-hidden="true"></span>Por timbrar</a>
                        </li>
                        <li>
                            <a href="{{ route('documents', 3) }}"><span class="icon bx bxs-file-plus bx-sm" aria-hidden="true"></span>Timbradas</a>
                        </li>
                        <li>
                            <a href="{{ route('trailers') }}"><span class="icon bx bx-archive bx-sm" aria-hidden="true"></span>Todas</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-truck bx-sm" aria-hidden="true"></span>Transportistas
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="{{ route('carriers') }}"><span class="icon bx bxs-group bx-sm" aria-hidden="true"></span>Transportistas</a>
                        </li>
                        <li>
                            <a href="{{ route('vehicles') }}"><span class="icon bx bxs-key bx-sm" aria-hidden="true"></span>Vehiculos</a>
                        </li>
                        <li>
                            <a href="{{ route('trailers') }}"><span class="icon bx bxs-package bx-sm" aria-hidden="true"></span>Trailers</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="{{ route('drivers') }}">
                        <span class="icon bx bxs-id-card bx-sm" aria-hidden="true"></span>Figuras de transporte
                    </a>
                </li>
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-user bx-sm" aria-hidden="true"></span>Usuarios
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="{{ route('register') }}"><span class="icon bx bxs-user-plus bx-sm" aria-hidden="true"></span>Nuevo usuario</a>
                        </li>
                        <li>
                            <a href="{{ route('users') }}"><span class="icon bx bxs-user-detail bx-sm" aria-hidden="true"></span>Ver usuarios</a>
                        </li>
                        <li>
                            <a href="{{ route('role') }}"><span class="icon bx bxs-user-badge bx-sm" aria-hidden="true"></span>Roles de usuarios</a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-book-content bx-sm" aria-hidden="true"></span>Catálogos
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
                        <li>
                            <a href="{{ route('states') }}"><span class="icon bx bxs-map-alt bx-sm" aria-hidden="true"></span>Estados</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</aside>