<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DocumentController extends Controller
{
    public function storeInput(Request $request)
    {
        $objDataJ = $request->all();
        
        return json_encode($objDataJ);
    }
}
