<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/InventarioSalida.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\InventarioSalida;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class InventarioPendientesAjax
{

	/*=============================================
	TABLA De inventarios pendientes
	=============================================*/
	public function mostrarTabla()
	{
		$inventarioPendientes = New InventarioSalida;
        $inventarioPendientes = $inventarioPendientes->consultarSalidasPendientes();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "almacen" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "observaciones" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($inventarioPendientes as $key => $value) {
        	$rutaDestroy = Route::names('inventarios-pendientes.destroy', $value['id']);
            $rutaPrint = Route::names('inventario-salidas.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "folio" => fString($value["id"]),
        							  "almacen" => fString($value["almacen.descripcion"]),
        							  "fecha" => fFechaLarga($value["fechaCreacion"]),
        							  "observaciones" => mb_strtoupper($value["observaciones"]),
        							  "acciones" => "<button type='button' class='btn btn-xs btn-success autorizar' data-id='{$value['id']}' 
                                      data-token='{$token}' data-folio='{$folio}' title='Autorizar'><i class='fas fa-check'></i></button>
                                      <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>
                                                     " ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    /*=============================================
    Agregar autorizacion de inventario pendiente
    =============================================*/
    public $token;
    public $id;

    public function agregar()
    {
        if ( !Validacion::validaToken($this->token) ) {

            $respuesta = array();
            $respuesta['codigo'] = 400;
            $respuesta['error'] = true;
            $respuesta['mensaje'] = $error;

            echo json_encode($respuesta);
            return;

        }

        $inventario = New InventarioSalida;
        $inventario->id = $this->id;

        $respuestaInventario = $inventario->actualizarStatus();

        if ( $respuestaInventario ) {

            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['mensaje'] = 'El inventario pendiente fue autorizado correctamente.';

        } else {

            $respuesta = array();
            $respuesta['codigo'] = 500;
            $respuesta['error'] = true;
            $respuesta['mensaje'] = 'Hubo un error al procesar la autorización, de favor intente de nuevo.';

        }

        echo json_encode($respuesta);
    }
}

$inventarioPendientesAjax = New InventarioPendientesAjax;

if ( isset($_POST["accion"]) ) {

	/*=============================================
	autorizar 
	=============================================*/
	$inventarioPendientesAjax->token = $_POST["_token"];
    $inventarioPendientesAjax->id = $_POST["id"];
	$inventarioPendientesAjax->agregar();

} else {

	/*=============================================
	TABLA DE inventarios pendientes
	=============================================*/
	$inventarioPendientesAjax->mostrarTabla();

}