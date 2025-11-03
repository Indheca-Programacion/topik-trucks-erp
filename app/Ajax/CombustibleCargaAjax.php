<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Combustible.php";
require_once "../Models/Maquinaria.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Combustible;
use App\Models\Maquinaria;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class CombustibleCargaAjax
{

	/*=============================================
	TABLA DE CARGAS DE COMBUSTIBLE
	=============================================*/
	public function mostrarTabla()
	{
		$combustible = New Combustible;
        $combustibles = $combustible->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "empleado" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "hora" ]);
        array_push($columnas, [ "data" => "litros" ]);
        array_push($columnas, [ "data" => "observaciones" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($combustibles as $key => $value) {
        	$rutaEdit = Route::names('combustible-cargas.edit', $value['id']);
        	$rutaDestroy = Route::names('combustible-cargas.destroy', $value['id']);
            // $rutaPrint = Route::names('combustible-cargas.print', $value['id']);
            $folio = fFechaLarga($value["fecha"]);

        	array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "empleado" => mb_strtoupper(fString($value['empleados.nombreCompleto'])),
                "fecha" => fFechaLarga($value["fecha"]),
                "hora" => substr($value["hora"], 0, 5),
                "litros" => $value["sumLitros"],
                "observaciones" => mb_strtoupper($value["observaciones"]),
                "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>" ] );

            // "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
            //                      <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $empleadoId;

    public function consultarFiltros()
    {
        $arrayFiltros = array();

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "C.empresaId", "valor" => $this->empresaId ]);
        if ( $this->empleadoId > 0 ) array_push($arrayFiltros, [ "campo" => "C.empleadoId", "valor" => $this->empleadoId ]);

        $combustible = New Combustible;
        $combustibles = $combustible->consultarFiltros($arrayFiltros);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "empleado" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "hora" ]);
        array_push($columnas, [ "data" => "litros" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($combustibles as $key => $value) {
            $rutaEdit = Route::names('combustible-cargas.edit', $value['id']);
            $rutaDestroy = Route::names('combustible-cargas.destroy', $value['id']);
            // $rutaPrint = Route::names('combustible-cargas.print', $value['id']);
            $folio = fFechaLarga($value["fecha"]);

            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "empleado" => mb_strtoupper(fString($value['empleados.nombreCompleto'])),
                "fecha" => fFechaLarga($value["fecha"]),
                "hora" => substr($value["hora"], 0, 5),
                "litros" => $value["sumLitros"],
                "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
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

    /*=============================================
    CONSULTAR MAQUINARIAS
    =============================================*/
    public $token;

    public function consultar()
    {
        $respuesta["error"] = false;

        // Validar Token
        if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

        // Validar existencia del campo empresaId
        if ( !Validacion::validar("empresaId", $this->empresaId, ['exists', CONST_BD_SECURITY.'.empresas', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "La empresa no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $maquinaria = New Maquinaria;
        $maquinarias = $maquinaria->consultarEmpresa($this->empresaId);

        $cantidad = count($maquinarias);
        $respuesta["respuestaMessage"] = "La empresa tiene {$cantidad} maquinarias.";
        $respuesta["respuesta"] = $maquinarias;

        echo json_encode($respuesta);
    }

}

$combustibleCargaAjax = New CombustibleCargaAjax;

if ( isset($_POST["accion"]) && isset($_POST["empresaId"]) ) {

    /*=============================================
    CONSULTAR MAQUINARIAS
    =============================================*/
    
    $combustibleCargaAjax->token = $_POST["_token"];

    if ( $_POST["accion"] == "consultar" ) {

        $combustibleCargaAjax->empresaId = $_POST["empresaId"];
        $combustibleCargaAjax->consultar();

    } else {

        $respuesta["error"] = true;
        $respuesta["errorMessage"] = "Acción no reconocida.";

        echo json_encode($respuesta);

    }

} elseif ( isset($_GET["empresaId"]) && isset($_GET["empleadoId"]) ) {

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    $combustibleCargaAjax->empresaId = $_GET["empresaId"];
    $combustibleCargaAjax->empleadoId = $_GET["empleadoId"];
    $combustibleCargaAjax->consultarFiltros();

} else {

    /*=============================================
    TABLA DE CARGAS DE COMBUSTIBLE
    =============================================*/
    $combustibleCargaAjax->mostrarTabla();

}
