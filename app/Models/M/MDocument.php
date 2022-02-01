<?php

namespace App\Models\M;

use Jenssegers\Mongodb\Eloquent\Model;

class MDocument extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'm_documents';
    protected $primaryKey = '_id';

    protected $fillable = [
        'localCurrency',
        'localCurrencyCode',
        'cfdiVersion',
        'tipoDeComprobante',
        'dtDate',
        'serie',
        'folio',
        'lugarExpedicion',
        'objetoImp',
        'formaPago',
        'metodoPago',
        'currency',
        'tipoCambio',
        'tipoCambioReadonly',
        'subTotal',
        'discounts',
        'total',
        'receptor',
        'emisor',
        'retenciones',
        'traslados',
        'conceptos',
        'totalImpuestosTrasladados',
        'totalImpuestosRetenidos',
        'oCartaPorte',
        'carrier_id',
        'body_request'
    ];
}
