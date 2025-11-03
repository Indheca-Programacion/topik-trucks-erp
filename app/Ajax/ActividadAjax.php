<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Actividad.php";
require_once "../Models/Servicio.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Actividad;
use App\Models\Servicio;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ActividadAjax
{

	/*=============================================
	TABLA DE ACTIVIDAD SEMANAL
	=============================================*/
	public function mostrarTabla()
	{
		$actividad = New Actividad;
        $actividades = $actividad->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "empleado" ]);
        array_push($columnas, [ "data" => "fechaInicial" ]);
        array_push($columnas, [ "data" => "fechaFinal" ]);
        array_push($columnas, [ "data" => "horasTrabajadas" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($actividades as $key => $value) {
        	$rutaEdit = Route::names('actividad-semanal.edit', $value['id']);
        	// $rutaDestroy = Route::names('actividad-semanal.destroy', $value['id']);
            $rutaPrint = Route::names('actividad-semanal.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['folio']));

        	array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "folio" => $value["folio"],
                "empleado" => mb_strtoupper(fString($value['empleados.nombreCompleto'])),
                "fechaInicial" => fFechaLarga($value["fechaInicial"]),
                "fechaFinal" => fFechaLarga($value["fechaFinal"]),
                "horasTrabajadas" => $value["sumHorasTrabajadas"],
                "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                 <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        // "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
        //                         <form method='POST' action='{$rutaDestroy}' style='display: inline'>
        //                             <input type='hidden' name='_method' value='DELETE'>
        //                             <input type='hidden' name='_token' value='{$token}'>
        //                             <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
        //                                 <i class='far fa-times-circle'></i>
        //                             </button>
        //                         </form>"

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

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "A.empresaId", "valor" => $this->empresaId ]);
        if ( $this->empleadoId > 0 ) array_push($arrayFiltros, [ "campo" => "A.empleadoId", "valor" => $this->empleadoId ]);

        $actividad = New Actividad;
        $actividades = $actividad->consultarFiltros($arrayFiltros);

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "empleado" ]);
        array_push($columnas, [ "data" => "fechaInicial" ]);
        array_push($columnas, [ "data" => "fechaFinal" ]);
        array_push($columnas, [ "data" => "horasTrabajadas" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $token = createToken();
        
        $registros = array();
        foreach ($actividades as $key => $value) {
            $rutaEdit = Route::names('actividad-semanal.edit', $value['id']);
            $rutaPrint = Route::names('actividad-semanal.print', $value['id']);
            $folio = mb_strtoupper(fString($value['folio']));

            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "folio" => $value["folio"],
                "empleado" => mb_strtoupper(fString($value['empleados.nombreCompleto'])),
                "fechaInicial" => fFechaLarga($value["fechaInicial"]),
                "fechaFinal" => fFechaLarga($value["fechaFinal"]),
                "horasTrabajadas" => $value["sumHorasTrabajadas"],
                "acciones" =>   "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                 <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    /*=============================================
    CONSULTAR ORDENES DE TRABAJO
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

        $servicio = New Servicio;
        $servicios = $servicio->consultarAbiertos($this->empresaId);

        $cantidad = count($servicios);
        // $respuesta["respuestaMessage"] = "La empresa tiene {$cantidad} ordenes de trabajo abiertas.";
        $respuesta["respuestaMessage"] = "La empresa tiene {$cantidad} ordenes de trabajo.";
        $respuesta["respuesta"] = $servicios;

        echo json_encode($respuesta);
    }

}

$actividadAjax = New ActividadAjax;

if ( isset($_POST["accion"]) && isset($_POST["empresaId"]) ) {

    /*=============================================
    CONSULTAR ORDENES DE TRABAJO
    =============================================*/
    
    $actividadAjax->token = $_POST["_token"];

    if ( $_POST["accion"] == "consultar" ) {

        $actividadAjax->empresaId = $_POST["empresaId"];
        $actividadAjax->consultar();

    } else {

        $respuesta["error"] = true;
        $respuesta["errorMessage"] = "Acción no reconocida.";

        echo json_encode($respuesta);

    }

} elseif ( isset($_GET["empresaId"]) && isset($_GET["empleadoId"]) ) {

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    $actividadAjax->empresaId = $_GET["empresaId"];
    $actividadAjax->empleadoId = $_GET["empleadoId"];
    $actividadAjax->consultarFiltros();

} else {

    /*=============================================
    TABLA DE ACTIVIDAD SEMANAL
    =============================================*/
    $actividadAjax->mostrarTabla();

}
