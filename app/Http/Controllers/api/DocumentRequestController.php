<?php

namespace App\Http\Controllers\api;

use App\Models\DocumentRequest;
use App\Models\Carrier;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SXml\XmlGeneration;

class DocumentRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        $oObjData = (object) $request->info;

        $oCarrier = Carrier::where('fiscal_id', $oObjData->rfcTransportista)
                            ->where('is_deleted', false)
                            ->first();

        $oRequest = new DocumentRequest();

        $oRequest->dt_request = date('Y-m-d H:i:s');
        $oRequest->comp_version = $oObjData->versionComplemento;
        $oRequest->xml_version = $oObjData->versionCfdi;
        $oRequest->body_request_id = "";
        $oRequest->is_processed = false;
        $oRequest->is_deleted = false;
        $oRequest->carrier_id = $oCarrier->id_carrier;
        $oRequest->usr_new_id = 1;
        $oRequest->usr_upd_id = 1;

        $oRequest->save();

        XmlGeneration::generateCarta();
        $originalString = XmlGeneration::createOriginalString();

        return json_encode($originalString);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\DocumentRequest  $documentRequest
     * @return \Illuminate\Http\Response
     */
    public function show(DocumentRequest $documentRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\DocumentRequest  $documentRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(DocumentRequest $documentRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\DocumentRequest  $documentRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\DocumentRequest  $documentRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(DocumentRequest $documentRequest)
    {
        //
    }
}
