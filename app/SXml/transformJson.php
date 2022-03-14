<?php

namespace App\SXml;

use App\Models\LocalOrigins;

class transformJson
{
    public static function transfom($data){
        $objData = (object) $data;
        
        $jobject = json_encode($objData);
        $json = json_decode($jobject);

        $infoValues = array(
            'rfcTransportista'=>$objData->rfcTransportista,
            'moneda'=>$objData->moneda
            );
            
        $info = [];
        $ubicaciones = [];
        $mercancia = [];
        $mercancias = [];

        $infoOrigen = LocalOrigins::where([['origin_code',$json->idOrigen],['is_deleted',0]])
                                    ->select(
                                        'tipoUbicacion',
                                        'rFCRemitenteDestinatario',
                                        'nombreRFC',
                                        'calle',
                                        'numeroExterior',
                                        'numeroInterior',
                                        'colonia',
                                        'localidad',
                                        'referencia',
                                        'municipio',
                                        'estado',
                                        'pais',
                                        'codigoPostal'
                                        )
                                    ->first();

        $uOrigen = array(
            'tipoUbicacion' => $infoOrigen->tipoUbicacion,
            'rFCRemitenteDestinatario' => $infoOrigen->rFCRemitenteDestinatario,
            'nombreRFC' => $infoOrigen->nombreRFC,
            'domicilio' => array(
                'calle' => $infoOrigen->calle,
                'numeroExterior' => $infoOrigen->numeroExterior,
                'numeroInterior' => $infoOrigen->numeroInterior,
                'colonia' => $infoOrigen->colonia,
                'localidad' => $infoOrigen->localidad,
                'referencia' => $infoOrigen->referencia,
                'municipio' => $infoOrigen->municipio,
                'estado' => $infoOrigen->estado,
                'pais' => $infoOrigen->pais,
                'codigoPostal' => $infoOrigen->codigoPostal
            )
        );

        array_push($ubicaciones, $uOrigen);

        $unidadPeso = '';
        foreach($json->ubicaciones as $u){
            $uDestino = array(
                'tipoUbicacion' => $u->tipoUbicacion,
                'rFCRemitenteDestinatario' => $u->rfcRemitenteDestinatario,
                'nombreRFC' => $u->nombreRFC,
                'domicilio' => array(
                    'calle' => $u->direccion->calle,
                    'numeroExterior' => $u->direccion->numeroExterior,
                    'numeroInterior' => $u->direccion->numeroInterior,
                    'colonia' => $u->direccion->colonia,
                    'localidad' => $u->direccion->localidad,
                    'referencia' => $u->direccion->referencia,
                    'municipio' => $u->direccion->municipio,
                    'estado' => $u->direccion->estado,
                    'pais' => $u->direccion->pais,
                    'codigoPostal' => $u->direccion->codigoPostal
                )
            );
        
            foreach($u->mercancias as $m){
                $uMercancias = array(
                    'bienesTransp' => $m->bienesTransp,
                    'cantidad' => $m->cantidad,
                    'claveUnidad' => $m->claveUnidad,
                    'valorMercancia' => $m->valorMercancia,
                    'moneda' => $m->moneda,
                );

                array_push($mercancias, $uMercancias);
            }

            array_push($ubicaciones, $uDestino);
        }

        $mercancia['pesoBrutoTotal'] = $json->pesoBrutoTotal;
        $mercancia['mercancias'] = $mercancias;
        
        $info['rfcTransportista'] = $json->rfcTransportista;
        $info['moneda'] = $json->moneda;
        $info['ubicaciones'] = $ubicaciones;
        $info['mercancia'] = $mercancia;

        return $info;
    } 
}