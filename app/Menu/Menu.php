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
            if($oUser->user_type_id == 3){
                $menu = $menu.'
                    <li>
                        <a href="'.route('editar_carrier', $oUser->carrier()->first()->id_carrier).'">
                            <span class="icon bx bxs-user-rectangle bx-sm" aria-hidden="true"></span>Mis datos fiscales
                        </a>
                    </li>
                ';
            }
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
            if($oUser->user_type_id == 4){
                $menu = $menu.'
                    <li>
                        <a href="'.route('editar_driver', $oUser->driver()->first()->id_trans_figure).'">
                            <span class="icon bx bxs-user-rectangle bx-sm" aria-hidden="true"></span>Mis datos fiscales
                        </a>
                    </li>
                ';
            }
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

        if($oUser->hasAnyPermission(['240'])){
            $menu = $menu.'
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-user bx-sm" aria-hidden="true"></span>Asociados
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
            ';
            if($oUser->hasPermission('241')){
                $route = route('crear_parner');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-plus bx-sm" aria-hidden="true"></span>Agregar socio</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('242')){
                $route = route('parners');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-detail bx-sm" aria-hidden="true"></span>Ver socios</a>
                        </li>
                ';
            }

            $menu =  $menu.'
                    </ul>
                </li>
            ';
        }

        if($oUser->hasAnyPermission(['500'])){
            $menu = $menu.'
                <li>
                    <a class="show-cat-btn" href="##">
                        <span class="icon bx bxs-book-content bx-sm" aria-hidden="true"></span>Catálogos
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
            ';
            if($oUser->hasPermission('510')){
                $route = route('states');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-map-alt bx-sm" aria-hidden="true"></span>Estados</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('520')){
                $route = route('insurances');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-clinic bx-sm" aria-hidden="true"></span>Aseguradoras</a>
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