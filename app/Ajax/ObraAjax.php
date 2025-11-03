<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Obra.php";
require_once "../Requests/SaveObrasRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Obra;
use App\Requests\SaveObrasRequest;
use App\Controllers\Autorizacion;
// use App\Controllers\Validacion;

class ObraAjax
{
	/*=============================================
	TABLA DE OBRAS
	=============================================*/
	public function mostrarTabla()
	{
		$obra = New Obra;
        $obras = $obra->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($obras as $key => $value) {
        	$rutaEdit = Route::names('obras.edit', $value['id']);
        	$rutaDestroy = Route::names('obras.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [
        		"consecutivo" => ($key + 1),
        		"empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
				"descripcion" => mb_strtoupper(fString($value["descripcion"])),
				"nombreCorto" => mb_strtoupper(fString($value["nombreCorto"])),
                "periodos" => $value["periodos"],
                "fechaInicio" => ( is_null($value["fechaInicio"]) ? '' : fFechaLarga($value["fechaInicio"]) ),
				"creo" => mb_strtoupper(fString($value["nombreCompleto"])),
				"acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
								<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									<input type='hidden' name='_method' value='DELETE'>
									<input type='hidden' name='_token' value='{$token}'>
									<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
										<i class='far fa-times-circle'></i>
									</button>
								</form>"
			] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
}

    $obraAjax = New ObraAjax();

	/*=============================================
	TABLA DE Obra
	=============================================*/
	$obraAjax->mostrarTabla();