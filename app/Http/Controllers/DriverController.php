<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Driver;
use Illuminate\Http\Request;
use Validator;

class DriverController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $drivers = DB::table('f_trans_figures')->where([['tp_figure_id', '=', 1],['is_deleted', 'IS','FALSE']])->get();
        $tp_figure = DB::table('sat_figure_types')->get();
        $country = DB::table('s_country')->get();
        $state = DB::table('sat_states')->get();
        $carriers = DB::table('f_carriers')->where('is_deleted', 'IS','FALSE')->get();

        return view('ship/drivers/index', ['drivers' => $drivers, 'tp_figure' => $tp_figure,
                                            'country' => $country, 'state' => $state,
                                            'carriers' => $carriers]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        dd("hola");
        $validator = Validator::make($request->all(), [
            'full_name' => 'required',
            'RFC' => 'required|min:5|max:5',
            'licence' => 'required',
            'tp_figure' => 'required|not_in:0', 
            'carrier' => 'required|not_in:0',
            'country' => 'required|not_in:0',
            'zip_code' => 'required',
            'state' => 'required|not_in:0'
        ]);

        $validator->validate();
        $sta_name = explode (",", $request->state);
        $user_id = (auth()->check()) ? auth()->user()->id : null;

        // DB::insert('insert into f_trans_figures (fullname, fiscal_id, fiscal_fgr_id, driver_lic,
        // tp_figure_id, fis_address_id, carrier_id, usr_new_id, usr_upd_id, created_at, updated_at)
        // values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [$request->full_name, $request->RFC,
        // $request->RFC_ex, $request->licence, $request->tp_figure, $request->country, $request->carrier,
        // $user_id,$user_id,date("Y-m-d h:i:sa"),date("Y-m-d h:i:sa")]);

        $id_fig_trans = DB::table('f_trans_figures')->insertGetId(['fullname' => $request->full_name, 
        'fiscal_id' => $request->RFC, 'fiscal_fgr_id' => $request->RFC_ex, 'driver_lic' => $request->licence,
        'tp_figure_id' => $request->tp_figure, 'fis_address_id' => $request->country, 
        'carrier_id' => $request->carrier, 'usr_new_id' => $user_id, 'usr_upd_id' => $user_id,
        'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')]
        );

        DB::insert('insert into f_addresses (telephone, street, street_num_ext, street_num_int, 
        neighborhood, reference, locality, state, zip_code, trans_figure_id, country_id, state_id, 
        usr_new_id, usr_upd_id, created_at, updated_at) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
        [$request->telephone, $request->street, $request->street_num_ext, $request->street_num_int, 
        $request->neighborhood, $request->reference, $request->locality, $sta_name[1], $request->zip_code, 
        $id_fig_trans, $request->country, $sta_name[0], $user_id, $user_id, date('Y-m-d H:i:s'), 
        date('Y-m-d H:i:s')]);

        return redirect('/drivers');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function show(Driver $driver)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function edit(Driver $driver)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Driver $driver)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Driver  $driver
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        dd("des");
    }
}
