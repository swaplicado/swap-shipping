<?php

namespace App\Forms;

use App\User;
use Illuminate\Support\Facades\DB;
use App\Models\Carrier;

class Forms {

    public static function createForm($oUser = null)
    {
        $carriers = Carrier::where('is_deleted', 0)->pluck('id_carrier','fullname');
        $option = '';
        foreach($carriers as $c => $index){
            $option = $option.'<option value="'.$index.'">'.$c.'</option>';
        }
        $form = '';
        if($oUser->isAdmin()){
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
}