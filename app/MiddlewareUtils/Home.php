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
                    <div class="card-body" style="height: 150pt;">
                    <h5 class="card-title">Paso 1: Revisa tus datos fiscales</h5>
                    <p class="card-text">
                        Asegurate de que tus datos fiscales sean correctos, puedes revisarlos y actualizarlos
                        si es necesario presionando el boton "Ir a mis datos fiscales".
                    </p>
                    <br>
                    <a href='.route("editar_carrierFiscalData", auth()->user()->carrier()->first()->id_carrier).' class="btn btn-primary">Ir a mis datos fiscales</a>
                    </div>
                </div>
                </div>
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 150pt;">
                    <h5 class="card-title">Paso 2: Agrega al menos una aseguradora para tus vehículos</h5>
                    <p class="card-text">Es necesario registrar al menos a una aseguradora para poder proceder
                        con el registro de tus vehiculos, puedes acceder a la vista de aseguradoras presionando el boton
                        "Ir a mis aseguradoras", al presionar el boton se te dirigira a la vista aseguradoras donde podras
                        revisar la lista de tus aseguradoras registradas, editar tus aseguradoras y registrar aseguradoras.
                    </p>
                    <br>
                    <a href="insurances" class="btn btn-primary">Ir a mis aseguradoras</a>
                    </div>
                </div>
                </div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 150pt;">
                    <h5 class="card-title">Paso 3: Registra algun vehículo</h5>
                    <p class="card-text">
                        Puedes acceder a la vista de vehículos presionando el boton
                        "Ir a mis vehículos", al presionar el boton se te dirigira a la vista vehículos donde podras
                        revisar la lista de tus vehículos registrados, editar tus vehículos y agregar mas vehículos.
                    </p>
                    <br>
                    <a href="vehicles" class="btn btn-primary">Ir a mis vehículos</a>
                    </div>
                </div>
                </div>
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 150pt;">
                    <h5 class="card-title">Paso 4: Registra algun remolque</h5>
                    <p class="card-text">
                        Puedes acceder a la vista de remolques presionando el boton
                        "Ir a mis remolques", al presionar el boton se te dirigira a la vista remolques donde podras
                        revisar la lista de tus remolques registrados, editar tus remolques y agregar mas remolques.  
                    </p>
                    <br>
                    <a href="trailers" class="btn btn-primary">Ir a mis remolques</a>
                    </div>
                </div>
                </div>
            </div>
            <br>
            
            <div class="row">
                <div class="col-sm-6">
                <div class="card">
                    <div class="card-body" style="height: 150pt;">
                    <h5 class="card-title">Paso 5: Registra algun chofer</h5>
                    <p class="card-text">
                        Puedes acceder a la vista de choferes presionando el boton
                        "Ir a mis choferes", al presionar el boton se te dirigira a la vista choferes donde podras
                        revisar la lista de tus choferes registrados, editar tus choferes y agregar mas choferes.
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