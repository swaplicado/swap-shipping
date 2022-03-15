<?php

namespace App\SXml;

use App\Models\LocalOrigins;

class transformJson
{
    public static function transfom($data){
        if(is_null($data)){
            return ['json'=>null, 'msg'=>"No se recibió ningun dato", 'code'=>500];
        }

        $objData = (object) $data;
        
        $jobject = json_encode($objData);
        $json = json_decode($jobject);
        
        if(!property_exists($json, "idOrigen")){
            return ['json'=>null, 'msg'=>"No se encontró idOrigen", 'code'=>500];
        }
        if(!property_exists($objData, "rfcTransportista")){
            return ['json'=>null, 'msg'=>"No se encontró rfcTransportista", 'code'=>500];
        }
        if(!property_exists($objData, "moneda")){
            return ['json'=>null, 'msg'=>"No se encontró moneda", 'code'=>500];
        }
        
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
        
        if(is_null($infoOrigen)){
            return ['json'=>null, 'msg'=>"No se encontró información de ubicación origen", 'code'=>500];
        }

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

        if(!property_exists($json, "ubicaciones")){
            return ['json'=>null, 'msg'=>"No se encontró ubicaciones", 'code'=>500];
        }

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
            
            if(!property_exists($u, "mercancias")){
                return ['json'=>null, 'msg'=>"No se encontró mercancias para el rfcRemitenteDestinatario : ".$u->rfcRemitenteDestinatario, 'code'=>500];
            }

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

        if(!property_exists($json, "pesoBrutoTotal")){
            return ['json'=>null, 'msg'=>"No se encontró pesoBrutoTotal", 'code'=>500];
        }

        $mercancia['pesoBrutoTotal'] = $json->pesoBrutoTotal;
        $mercancia['mercancias'] = $mercancias;
        
        if(!property_exists($json, "rfcTransportista")){
            return ['json'=>null, 'msg'=>"No se encontró rfcTransportista", 'code'=>500];
        }
        if(!property_exists($json, "moneda")){
            return ['json'=>null, 'msg'=>"No se encontró moneda", 'code'=>500];
        }

        $info['rfcTransportista'] = $json->rfcTransportista;
        $info['moneda'] = $json->moneda;
        $info['ubicaciones'] = $ubicaciones;
        $info['mercancia'] = $mercancia;

        return ['json'=>$info, 'msg'=>"ok", 'code'=>200];
    } 
}