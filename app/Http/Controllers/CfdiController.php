<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Utils\CfdiUtils;
use App\Models\Document;
use App\Models\M\MDocument;

class CfdiController extends Controller
{

    public function index($id){
        $pdf = CfdiUtils::index($id);
        // return view('pdf', ['pdf' => $pdf]);
        $data = base64_decode($pdf->pdf);
        header('Content-Type: application/pdf');
        echo $data;
    }
}