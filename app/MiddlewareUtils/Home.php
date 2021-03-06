<?php

namespace App\MiddlewareUtils;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Models\Carrier;

class Home {

    public static function home($oUser = null){
        $home = '';
        if($oUser->isCarrier()){
            $home = '
                <div class="row">
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 250px; text-align: justify;">
                    <h5 class="card-title">Paso 1: Revisa tus datos fiscales.</h5>
                    <p class="card-text">
                        Asegúrate de que tus datos fiscales sean correctos, puedes revisarlos y actualizarlos
                        si es necesario en la sección "Mis datos fiscales" del menú en el lado izquierdo de tu pantalla, 
                        o presionando el botón "Ir a mis datos fiscales".
                    </p>
                    <br>
                    <a href='.route("editar_carrierFiscalData", auth()->user()->carrier()->first()->id_carrier).' class="btn btn-primary">Ir a mis datos fiscales</a>
                    </div>
                </div>
                </div>
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 250px; text-align: justify;">
                    <h5 class="card-title">Paso 2: Registra una serie para tus documentos.</h5>
                    <p class="card-text">
                    Es necesario registrar una serie y folio, para tus documentos, sin esta no será posible 
                    editar ni timbrar tus documentos cartas porte, la serie que registres es libre, puedes 
                    registrar la serie con el nombre que tú desees. Para acceder a la vista de series, 
                    presiona en la sección "Catálogos" del menú en el lado izquierdo de tu pantalla, 
                    luego presiona en "Series", o presionando el botón "Ir a mis series", al presionar 
                    el botón se te dirigirá a la vista series donde podrás comprobar la lista de tus 
                    series registradas.  
                    </p>
                    <br>
                    <a href="series" class="btn btn-primary">Ir a mis series</a>
                    </div>
                </div>
                </div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 250px; text-align: justify;">
                    <h5 class="card-title">Paso 3: Registra al menos una aseguradora para tus vehículos.</h5>
                    <p class="card-text">
                        Es necesario registrar al menos a una aseguradora para poder proceder con el registro de tus 
                        vehículos, puedes acceder a la vista de aseguradoras en la sección "Catálogos" del menú en el 
                        lado izquierdo de tu pantalla presionando en "Aseguradoras", o presionando el botón "Ir a mis 
                        aseguradoras", al presionar el botón se te dirigirá a la vista aseguradora donde podrás 
                        comprobar la lista de tus aseguradoras registradas.
                    </p>
                    <br>
                    <a href="insurances" class="btn btn-primary">Ir a mis aseguradoras</a>
                    </div>
                </div>
                </div>
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 250px; text-align: justify;">
                    <h5 class="card-title">Paso 4: Registra un vehículo.</h5>
                    <p class="card-text">
                        Puedes acceder a la vista de vehículos desde la sección "Vehículos" en el menú del lado izquierdo de tu pantalla, 
                        o presionando el botón Ir a mis vehículos", al presionar el botón se te dirigirá a la vista vehículos donde 
                        podrás revisar la lista de tus vehículos registrados.
                    </p>
                    <br>
                    <a href="vehicles" class="btn btn-primary">Ir a mis vehículos</a>
                    </div>
                </div>
                </div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 250px; text-align: justify;">
                    <h5 class="card-title">Paso 5: Registra un remolque.</h5>
                    <p class="card-text">
                        Puedes acceder a la vista de remolques desde la sección "Remolques" en el menú del lado izquierdo de tu 
                        pantalla, o presionando el botón Ir a mis remolques", al presionar el botón se te dirigirá a la vista 
                        remolques donde podrás revisar la lista de tus remolques registrados.
                    </p>
                    <br>
                    <a href="trailers" class="btn btn-primary">Ir a mis remolques</a>
                    </div>
                </div>
                </div>
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 250px; text-align: justify;">
                    <h5 class="card-title">Paso 6: Registra un chofer.</h5>
                    <p class="card-text">
                        Puedes acceder a la vista de choferes desde la sección "Choferes" en el menú del lado 
                        izquierdo de tu pantalla, o presionando el botón "Ir a mis choferes", al presionar el 
                        botón se te dirigirá a la vista choferes donde podrás verificar la lista de tus choferes 
                        registrados.
                    </p>
                    <br>
                    <a href="trailers" class="btn btn-primary">Ir a mis choferes</a>
                    </div>
                </div>
                </div>
            </div>
            ';
        }
        return $home;
    }
}