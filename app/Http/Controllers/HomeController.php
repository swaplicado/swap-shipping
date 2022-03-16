<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Manifest;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {   
        // auth()->user()->authorizeRoles(['admin','driver', 'user']);
        // auth()->user()->authorizePermission(['A2', 'A3']);
        // return redirect()->route('documents', 1);

        $showPanel = false;
        $pendingDocuments = 0;
        $processedDocuments = 0;
        $stampedDocuments = 0;
        if (auth()->user()->isCarrier() || auth()->user()->isDriver()) {
            $showPanel = true;
            $idCarrier = 0;
            if (auth()->user()->isCarrier()) {
                $idCarrier =auth()->user()->carrier()->first()->id_carrier;
            }
            else {
                $idCarrier =auth()->user()->driver()->first()->carrier_id;
            }

            $pendingDocuments = (Document::where('carrier_id', $idCarrier)
                                    ->where('is_processed', false)
                                    ->where('is_deleted', false)
                                    ->get())->count();

            $processedDocuments = (Document::where('carrier_id', $idCarrier)
                                    ->where('is_processed', true)
                                    ->where('is_signed', false)
                                    ->where('is_deleted', false)
                                    ->get())->count();

            $stampedDocuments = (Document::where('carrier_id', $idCarrier)
                                    ->where('is_processed', true)
                                    ->where('is_signed', true)
                                    ->where('is_deleted', false)
                                    ->get())->count();
        }

        if(auth()->user()->isCarrier()){
            $bManifestSigned = Manifest::where('carrier_id', auth()->user()->carrier()->value('id_carrier'))->where('is_signed', true)->count() > 0;
            if(!$bManifestSigned){
                \Session::flash('notification', 'No has firmado tu carta manifiesto');
            }
        }

        return view('home')->with([
            'showPanel' => $showPanel,
            'pendingDocuments' => $pendingDocuments,
            'processedDocuments' => $processedDocuments,
            'stampedDocuments' => $stampedDocuments
        ]);
    }
}
