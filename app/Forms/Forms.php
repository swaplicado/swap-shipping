<?php

namespace App\Forms;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Models\Carrier;
use App\Models\Sat\FiscalAddress;
use App\Models\Sat\States;

class Forms {

    public static function createForm($oUser = null)
    {
        $carriers = Carrier::where('is_deleted', 0)->pluck('id_carrier','fullname');
        $option = '';
        foreach($carriers as $c => $index){
            $option = $option.'<option value="'.$index.'">'.$c.'</option>';
        }
        $form = '';
        if($oUser->isAdmin() || $oUser->isClient()){
            $form = '
                <div class="form-group">
                    <label for="carrier" class="form-label">Transportista</label>
                    <select class="form-select" name="carrier">
                        <option value="0" selected>Transportista</option>'.$option.'
                    </select>
                </div>
            ';
        }
        return $form;
    }

    public static function myProfile($oUser = null)
    {
        $form = '';
        if($oUser->isCarrier()){
            $carrier = $oUser->carrier()->first();
            $form = '
            <div class="form-group">
                <label for="contact1" class="form-label">Contacto 1</label>
                <input name="contact1" type="text" class="form-control" value='.$carrier->contact1.' required>
            </div>
            <div class="form-group">
                <label for="telephone1" class="form-label">Teléfono 1</label>
                <input name="telephone1" type="text" class="form-control" value='.$carrier->telephone1.' required>
            </div>
            <div class="form-group">
                <label for="contact2" class="form-label">Contacto 2</label>
                <input name="contact2" type="text" class="form-control" value='.$carrier->contact2.'>
            </div>
            <div class="form-group">
                <label for="telephone2" class="form-label">Teléfono 2</label>
                <input name="telephone2" type="text" class="form-control" value='.$carrier->contact2.'>
            </div>
            ';
        } else if ($oUser->isDriver()) {
            $driver = $oUser->driver()->first();
            $countrys = FiscalAddress::orderBy('description', 'ASC')->pluck('id', 'description');
            $optionsCty = '';
            foreach($countrys as $cty => $index){
                if($driver->FAddress->country_id == $index){
                    $optionsCty = $optionsCty.'<option selected value='.$index.'>'.$cty.'</option>';
                }else{
                    if (251 == $index){
                        $optionsCty = $optionsCty.'<option selected value='.$index.'>'.$cty.'</option>';
                    }else{
                        $optionsCty = $optionsCty.'<option value='.$index.'>'.$cty.'</option>';
                    }
                }
            }

            $states = States::orderBy('state_name', 'ASC')->pluck('id', 'state_name');
            $optionsSt = '';
            foreach($states as $st => $index){
                $arr = array("id" => $index, "name" => $st );
                $json = json_encode($arr);
                if($driver->FAddress->state_id == $index){
                    $optionsSt = $optionsSt.'<option selected value='.$json.'>'.$st.'</option>';
                }else{
                    $optionsSt = $optionsSt.'<option value='.$json.'>'.$st.'</option>';
                }
            }

            $form = '
            <div class="form-group">
                <label for="RFC" class="form-label">RFC</label>
                <input name="RFC" type="text" class="form-control" value='.$driver->fiscal_id.' required>
            </div>
            <div class="form-group">
                <label for="RFC_ex" class="form-label">RFC extrangero</label>
                <input name="RFC_ex" type="text" class="form-control" value='.$driver->fiscal_fgr_id.'>
            </div>
            <div class="form-group">
                <label for="licence" class="form-label">Licencia</label>
                <input name="licence" type="text" class="form-control" value='.$driver->driver_lic.' required>
            </div>
            <div class="form-group">
                <label for="country" class="form-label">País</label>
                <select class="form-select" name="country" required>
                    <option value="0" selected>País</option>
                    '.$optionsCty.'
                </select>
            </div>
            <div class="form-group">
                <label for="zip_code" class="form-label">Código postal</label>
                <input name="zip_code" type="text" class="form-control" value='.$driver->FAddress->zip_code.' required>
            </div>
            <div class="form-group">
                <label for="state" class="form-label">Estado</label>
                <select class="form-select" name="state" required>
                    <option value="0" selected>Estado</option>
                    '.$optionsSt.'
                </select>
            </div>
            <div class="form-group">
                <label for="locality" class="form-label">Localidad</label>
                <input name="locality" type="text" class="form-control" value='.$driver->FAddress->locality.'>
            </div>
            <div class="form-group">
                <label for="neighborhood" class="form-label">Colonia</label>
                <input name="neighborhood" type="text" class="form-control" value='.$driver->FAddress->neighborhood.'>
            </div>
            <div class="form-group">
                <label for="street" class="form-label">Calle</label>
                <input name="street" type="text" class="form-control" value='.$driver->FAddress->street.'>
            </div>
            <div class="form-group">
                <label for="street_num_ext" class="form-label">Numero exterior</label>
                <input name="street_num_ext" type="text" class="form-control" value='.$driver->FAddress->street_num_ext.'>
            </div>
            <div class="form-group">
                <label for="street_num_int" class="form-label">Numero interior</label>
                <input name="street_num_int" type="text" class="form-control" value='.$driver->FAddress->street_num_int.'>
            </div>
            <div class="form-group">
                <label for="reference" class="form-label">Referencia</label>
                <input name="reference" type="text" class="form-control" value='.$driver->FAddress->reference.'>
            </div>
            <div class="form-group">
                <label for="telephone" class="form-label">Teléfono</label>
                <input name="telephone" type="text" class="form-control" value='.$driver->FAddress->telephone.'>
            </div>
            ';
        }
        return $form;
    }

    public static function profileRoute($oUser = null){
        $route = '';

        $oUser->isAdmin() ? $route = 'actualizar_profileAdmin' : '';
        $oUser->isClient() ? $route = 'actualizar_profileClient' : '';
        $oUser->isCarrier() ? $route = 'actualizar_profileCarrier' : '';
        $oUser->isDriver() ? $route = 'actualizar_profileDriver' : '';

        return $route;
    }
}