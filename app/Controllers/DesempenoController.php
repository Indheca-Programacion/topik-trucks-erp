<?php

namespace App\Controllers;

require_once "app/Models/Generadores.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Empresa.php";

use App\Models\Generadores;
use \App\Models\Empresa;
use App\Route;

class DesempenoController
{

    public function print($id)
    {
        Autorizacion::authorize('view', New Generadores);

        $generador = New Generadores;
        
        if ( $generador->consultar(null , $id) ) {
            
            require_once "app/Models/GeneradorDetalles.php";
            $generadorDetalle = New \App\Models\GeneradorDetalles;
            
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
                    } else if ($dayOfWeek == 6) {
                        $weekend +=1;
                    }
                }

                $hod = (($week*8)+($weekend*4));

                $rendimiento = $hod != 0 ? ($value["lcc"]/$hod) * 100 : 0;
                $aprovechamiento = $hod != 0 ? (($value["hmr"] - $value["rr"]) /$hod)*100 : 0;
                array_push( $arrayDesempeño, [ 
                    "id" => $value["id"],
                    "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                    "totalDias" => $totalDias,
                    "hod" => $hod,
                    "hmr" => $value["hmr"],
                    "rr" => $value["rr"],
                    "lcc" => $value["lcc"],
                    "rendimiento" => number_format($rendimiento,2),
                    "aprovechamiento" => number_format($aprovechamiento,2),
                    "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                ] );
            }

            // echo '<pre>'; print_r($datos); echo '</pre>';

            include "reportes/desempeno.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
