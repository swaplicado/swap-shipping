<?php

namespace App\Menu;

use App\User;

class Menu {

    public static function createMenu($oUser = null)
    {
        $menu = "";
        if($oUser->hasAnyPermission(['110', '120', '130', '140'])){
            $menu = $menu.'
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bx-file bx-sm" aria-hidden="true"></span>Cartas Porte
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
            ';
            if($oUser->hasPermission('110')){
                $route = route('documents', 1);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-file-blank bx-sm" aria-hidden="true"></span>Pendientes</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('120')){
                $route = route('documents', 2);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-file-blank bx-sm" aria-hidden="true"></span>Por timbrar</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('130')){
                $route = route('documents', 3);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-file-plus bx-sm" aria-hidden="true"></span>Timbradas</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('140')){
                $route = route('documents', 3);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-archive bx-sm" aria-hidden="true"></span>Todas</a>
                        </li>
                ';
            }

            $menu =  $menu.'
                    </ul>
                </li>
            ';
        }

        if($oUser->hasAnyPermission(['210', '220', '230'])){
            $menu = $menu.'
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-truck bx-sm" aria-hidden="true"></span>Transportistas
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
            ';
            if($oUser->hasPermission('210')){
                $route = route('carriers');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-group bx-sm" aria-hidden="true"></span>Transportistas</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('220')){
                $route = route('vehicles');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-key bx-sm" aria-hidden="true"></span>Vehiculos</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('230')){
                $route = route('trailers');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-package bx-sm" aria-hidden="true"></span>Trailers</a>
                        </li>
                ';
            }

            $menu =  $menu.'
                    </ul>
                </li>
            ';
        }

        if($oUser->hasAnyPermission(['310'])){
            $route = route('drivers');
            $menu = $menu.'
                <li>
                    <a href="'.$route.'">
                        <span class="icon bx bxs-id-card bx-sm" aria-hidden="true"></span>Choferes
                    </a>
                </li>
            ';
        }

        if($oUser->hasAnyPermission(['410', '420', '430'])){
            $menu = $menu.'
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-user bx-sm" aria-hidden="true"></span>Usuarios
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
            ';
            if($oUser->hasPermission('410')){
                $route = route('register');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-plus bx-sm" aria-hidden="true"></span>Nuevo usuario</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('420')){
                $route = route('users');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-detail bx-sm" aria-hidden="true"></span>Ver usuarios</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('430')){
                $route = route('role');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-badge bx-sm" aria-hidden="true"></span>Roles de usuarios</a>
                        </li>
                ';
            }

            $menu =  $menu.'
                    </ul>
                </li>
            ';
        }

        return $menu;
    }
}