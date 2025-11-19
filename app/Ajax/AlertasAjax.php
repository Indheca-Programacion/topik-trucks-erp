<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Alerta.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Alerta;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class AlertasAjax
{

	/*=============================================
	TABLA DE COLORES
	=============================================*/
	public function mostrarTabla()
	{
		$alerta = New Alerta;
        $alertas = $alerta->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "fechaCreacion" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($alertas as $key => $value) {
        	$rutaEdit = Route::names('alertas.edit', $value['id']);
        	$rutaDestroy = Route::names('alertas.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "ubicacion" => fString($value["ubicacion.descripcion"]),
        							  "obra" => mb_strtoupper(fString($value["obra.descripcion"])),
        							  "fechaCreacion" => fFechaLarga($value["fechaCreacion"]),
        							  "creo" => mb_strtoupper(fString($value["nombreCompleto"])),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

}

$alertaAjax = new AlertasAjax();

/*=============================================
TABLA DE COLORES
=============================================*/
$alertaAjax->mostrarTabla();