<?php

namespace App\Controllers;

require_once "app/Models/Generadores.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Empresa.php";

use App\Models\Generadores;
use \App\Models\Empresa;
use App\Route;

class EstimacionesController
{
    public function index()
    {
        Autorizacion::authorize('view', New Generadores);

        $contenido = array('modulo' => 'vistas/modulos/estimaciones/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Generadores);
        $generador = New Generadores;

        if ( $generador->consultar(null , $id) ) {

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            $empresa = New Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/GeneradorDetalles.php";
            $generadorDetalle = New \App\Models\GeneradorDetalles;
            $generadorDetalles = $generadorDetalle->consultarDetalles($id);

            $estimaciones = $generadorDetalle->consultarEstimaciones($id);

            $arrayEstimaciones = array();

            foreach ($estimaciones as $key => $value) {
                $laborados = json_decode($value["laborados"]);
                $fallas = json_decode($value["fallas"]);
                $paros = json_decode($value["paros"]);
                $clima = json_decode($value["clima"]);
                $totalDias = count($laborados) +  count($paros) + count($clima);
                //
                $fechaIngresada = $generador->mes;
                $partesFecha = explode('-', $fechaIngresada);
                $año = $partesFecha[0];
                $mes = $partesFecha[1];
                $totalDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $año);
                
                $division = $totalDiasMes != 0 ? count($laborados) / $totalDiasMes : 0;
                $pu = (floatval($value["costo"])/30) * $totalDias;

                $importe = number_format($pu+$value["operacion"]+$value["comb"]+$value["mantto"]+$value["flete"]+$value["ajuste"],2);

                array_push(  $arrayEstimaciones,[
                        "id" => $value["id"],
                        "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                        "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                        "totalDias" => $totalDias,
                        "costo" => $value["costo"],
                        "pu" => number_format($pu,2),
                        "operacion" => $value["operacion"],
                        "comb" => $value["comb"],
                        "mantto" => $value["mantto"],
                        "flete" => $value["flete"],
                        "ajuste" => $value["ajuste"],
                        "importe" => $importe
                    ]
                );
            }

            $desempeno = $generadorDetalle->consultarDesempeno($id);

            $arrayDesempeño = array();

            foreach ($desempeno as $key => $value) {
                $laborados = json_decode($value["laborados"]);
                $fallas = json_decode($value["fallas"]);
                $paros = json_decode($value["paros"]);
                $clima = json_decode($value["clima"]);
                $totalDias = count($laborados) + count($fallas) + count($paros) + count($clima);

                $week=0;
                $weekend=0;

                foreach ($laborados as $dia) {
                    $date = $dia.'-'.substr($generador->mes,2);
                    $timestamp = strtotime($date);
                    $dayOfWeek = date('N', $timestamp);

                    if ($dayOfWeek >= 1 && $dayOfWeek <= 5) {
                        $week +=1;
                    }else{
                        $weekend +=1;
                    }
                }

                $hod = (($week*8)+($weekend*4));

                $rendimiento = $hod != 0 ? ($value["lcc"]/$hod) * 100 : 0;
                $aprovechamiento = $hod != 0 ? (($value["hmr"] - $value["rr"]) /$hod)*100 : 0;
                array_push( $arrayDesempeño, [ 
                    "id" => $value["id"],
                    "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                    "totalDias" => count($laborados),
                    "hod" => $hod,
                    "hmr" => $value["hmr"],
                    "rr" => $value["rr"],
                    "lcc" => $value["lcc"],
                    "rendimiento" => number_format($rendimiento,2),
                    "aprovechamiento" => number_format($aprovechamiento,2),
                    "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                ] );
            }

            $contenido = array('modulo' => 'vistas/modulos/estimaciones/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Generadores);

        $generador = New Generadores;
        
        if ( $generador->consultar(null , $id) ) {
            
            require_once "app/Models/GeneradorDetalles.php";
            $generadorDetalle = New \App\Models\GeneradorDetalles;
            
            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $generador->usuarioIdCreacion);

            $estimaciones = $generadorDetalle->consultarEstimaciones($id);

            $datos = array();
            foreach ($estimaciones as $registro) {
                $empresaId = $registro['empresaId'];

                // Si la empresaId aún no existe en el resultado, la inicializamos como un array vacío
                if (!isset($datos[$empresaId])) {
                    $datos[$empresaId] = [];
                    $empresa = New Empresa;
                    $empresa->consultar(null,$registro["empresaId"]);
                    $datos[$empresaId]["ruta"] = $empresa->imagen;
                    unset($empresa);
                }

                // Agregamos el registro al array correspondiente
                $datos[$empresaId]["registros"][] = $registro;
            }

            $usuarioNombre = $usuario->nombre;
            $elaboro = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $elaboro .= ' ' . $usuario->apellidoMaterno;
            $elaboroFirma = $usuario->firma;
            $elaboro = mb_strtoupper($elaboro);
            unset($usuario);

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $generador->estimacionFirma);

            $usuarioNombre = $usuario->nombre;
            $autorizo = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $autorizo .= ' ' . $usuario->apellidoMaterno;
            $estimacionFirma = $usuario->firma;
            $autorizo = mb_strtoupper($autorizo);
            unset($usuario);

            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $generador->estimacionSupervisorFirma);

            $usuarioNombre = $usuario->nombre;
            $superviso = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $superviso .= ' ' . $usuario->apellidoMaterno;
            $supervisoFirma = $usuario->firma;
            $superviso = mb_strtoupper($superviso);
            unset($usuario);

            include "reportes/generador-estimaciones.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
