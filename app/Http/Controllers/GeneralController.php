<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneralController extends Controller
{
    public function error(Request $request)
    {
        $mssge = isset($request->error_message) ? $request->error_message : "Error";

        return view('error')->with('error_message', $mssge);
    }
}
