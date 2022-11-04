<?php

namespace App\Http\Controllers;

use App\Utils\CfdiUtils;

class CfdiController extends Controller
{

    public function index($id)
    {
        $pdf = CfdiUtils::index($id);
        
        if (!is_null($pdf->pdf)) {
            $data = base64_decode($pdf->pdf);
            header('Content-Type: application/pdf');
            echo $data;
        }
        else {
            return redirect('documents')->with(['icon' => "error", 'message' => 'El PDF no ha sido generado']);
        }
    }
}