<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SXml\XmlGeneration;

class DocumentController extends Controller
{
    public function storeInput(Request $request)
    {
        $objDataJ = $request->info;

        $domXmlObj = XmlGeneration::generateCarta();
        $originalString = XmlGeneration::createOriginalString($domXmlObj);
        XmlGeneration::loadCarta();
        
        return json_encode($objDataJ);
    }
}
