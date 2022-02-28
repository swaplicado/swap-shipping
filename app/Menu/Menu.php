<?php

namespace App\Menu;

use App\User;

class Menu {

    public static function createMenu($oUser = null)
    {
        $menu = "";
        if($oUser->hasAnyPermission(['100'])){
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
            if($oUser->hasPermission('111')){
                $route = route('documents', 1);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-file-blank bx-sm" aria-hidden="true"></span>Por procesar</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('121')){
                $route = route('documents', 2);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-file-blank bx-sm" aria-hidden="true"></span>Por timbrar</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('131')){
                $route = route('documents', 3);
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-file-plus bx-sm" aria-hidden="true"></span>Timbradas</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('141')){
                $route = route('documents', 0);
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

        if($oUser->hasAnyPermission(['200'])){
            if($oUser->isCarrier()){
                $menu = $menu.'
                    <li>
                        <a href="'.route('editar_carrierFiscalData', $oUser->carrier()->first()->id_carrier).'">
                            <span class="icon bx bxs-user-rectangle bx-sm" aria-hidden="true"></span>Mis datos fiscales
                        </a>
                    </li>
                ';
            }
            if($oUser->hasPermission('211')){
                $route = route('carriers');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-group bx-sm" aria-hidden="true"></span>Transportistas</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('221')){
                $route = route('vehicles');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-key bx-sm" aria-hidden="true"></span>Vehiculos</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('231')){
                $route = route('trailers');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-package bx-sm" aria-hidden="true"></span>Remolques</a>
                        </li>
                ';
            }
        }

        if($oUser->hasAnyPermission(['300'])){
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

        if($oUser->hasAnyPermission(['240'])){
            $menu = $menu.'
            ';
            if($oUser->hasPermission('241')){
                $route = route('parners');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-detail bx-sm" aria-hidden="true"></span>Asociados</a>
                        </li>
                ';
            }
        }

        if($oUser->hasAnyPermission(['400'])){
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
            if($oUser->hasPermission('411')){
                $route = route('states');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-map-alt bx-sm" aria-hidden="true"></span>Estados</a>
                        </li>
                ';
                $route = route('municipalities');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-map-alt bx-sm" aria-hidden="true"></span>Municipios</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('421')){
                $route = route('insurances');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-clinic bx-sm" aria-hidden="true"></span>Aseguradoras</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('431')){
                $route = route('series');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bx-sort-a-z bx-sm" aria-hidden="true"></span>Series</a>
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
                        <span class="icon bx bxs-user bx-sm" aria-hidden="true"></span>Usuarios
                        <span class="category__btn transparent-btn" title="Open list">
                            <span class="sr-only">Open list</span>
                            <span class="icon arrow-down" aria-hidden="true"></span>
                        </span>
                    </a>
                    <ul class="cat-sub-menu">
            ';
            if($oUser->hasPermission('511')){
                $route = route('users');
                $menu = $menu.'
                        <li>
                            <a href="'.$route.'"><span class="icon bx bxs-user-detail bx-sm" aria-hidden="true"></span>Ver usuarios</a>
                        </li>
                ';
            }
            if($oUser->hasPermission('611')){
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

        if($oUser->hasAnyPermission(['000'])){
            $menu = $menu.'
            <li>
                <a href="'.route('config').'">
                    <span class="icon bx bxs-cog bx-sm" aria-hidden="true"></span>Configuración
                </a>
            </li>
            ';
        }

        return $menu;
    }
}