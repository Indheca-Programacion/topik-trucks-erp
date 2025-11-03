<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/ComprobacionGasto.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\ComprobacionGasto;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ComprobacionGastosAjax
{

	/*=============================================
	TABLA DE COMPROBACION DE GASTOS
	=============================================*/
	public function mostrarTabla()
	{
		$comprobacionGasto = New ComprobacionGasto;
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        if ( Autorizacion::perfil( $usuario, CONST_ADMIN ) || Autorizacion::perfil( $usuario, 'compras') || Autorizacion::perfil( $usuario, 'pagos' ) ) {
            $colcomprobacionGastos = $comprobacionGasto->consultar();
        } else {
            $colcomprobacionGastos = $comprobacionGasto->consultarPorUsuario( );
        }

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "justificacion" ]);
        array_push($columnas, [ "data" => "monto" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($colcomprobacionGastos as $key => $value) {
			
        	$rutaEdit = Route::names('comprobacion-gastos.edit', $value['id']);
        	$rutaDestroy = Route::names('comprobacion-gastos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['folio']));
        	$creo = mb_strtoupper(fString($value['creo']));
        	array_push( $registros, [ "consecutivo" => ($key + 1),
                                        "folio" => $folio,
                                        "estatus" => mb_strtoupper(fString($value['estatus.descripcion'])),
                                        "colorTexto" => $value['estatus.colorTexto'],
                                        "colorFondo" => $value['estatus.colorFondo'],
                                        "justificacion" => mb_strtoupper(fString($value['justificacion'])),
                                        "monto" => '$ '.number_format($value['monto'], 2),
                                        "creo" => $creo,
                                        "fecha" => fFechaLarga($value['fechaCreacion']),
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

$comprobacionGastosAjax = new ComprobacionGastosAjax();

if ( isset($_POST["accion"]) ) {

    if ( $_POST["accion"] == "tablaComprobacionGastos" ) {

        /*=============================================
        TABLA DE COMPROBACION DE GASTOS
        =============================================*/
        $comprobacionGastosAjax->mostrarTabla();

    }

} else {

    /*=============================================
    TABLA DE COMPROBACION DE GASTOS
    =============================================*/
    $comprobacionGastosAjax->mostrarTabla();

}