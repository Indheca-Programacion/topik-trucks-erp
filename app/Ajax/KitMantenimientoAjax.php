<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/KitMantenimiento.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\KitMantenimiento;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class KitMantenimientoAjax
{

	/*=============================================
	TABLA DE kits DE MANTENIMIENTO
	=============================================*/
	public function mostrarTabla()
	{
		$kitMantenimiento = New KitMantenimiento;
        $kitsMantenimiento = $kitMantenimiento->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "tipoMantenimiento" ]);
        array_push($columnas, [ "data" => "tipoMaquinaria" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "proveedor" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($kitsMantenimiento as $key => $value) {
        	$rutaEdit = Route::names('kit-mantenimiento.edit', $value['id']);
        	$rutaDestroy = Route::names('kit-mantenimiento.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['tipoMantenimiento']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "tipoMantenimiento" => mb_strtoupper($value["tipoMantenimiento"]),
        							  "tipoMaquinaria" => mb_strtoupper($value["tipoMaquinaria"]),
        							  "modelo" => mb_strtoupper($value["modelo"]),
        							  "proveedor" => mb_strtoupper($value["proveedor"]??'NO ESPECIFICADO'),
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

    public function editar()
    {
        try {
            $kitMantenimiento = New KitMantenimiento;

            $datos = [
                "id" => $_POST["id"],
                "cantidad" => $_POST["cantidad"],
                "unidad" => $_POST["unidad"],
                "concepto" => $_POST["concepto"],
                "numero_parte" => $_POST["numeroParte"]
            ];

            $kitMantenimiento->editar($datos);

            $respuesta = array();
            $respuesta["codigo"] = 200;
            $respuesta["error"] = false;
            $respuesta["mensaje"] = "El kit de mantenimiento ha sido actualizado con éxito.";

        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);
    }

    public function agregar()
    {
        try {
            $kitMantenimiento = New KitMantenimiento;

            $datos = [
                "kitId" => $_POST["kitMantenimientoId"],
                "maquinariaId" => $_POST["maquinariaId"]
            ];

            $kitMantenimiento->agregarAlaMaquinaria($datos);

            $respuesta = array();
            $respuesta["codigo"] = 200;
            $respuesta["error"] = false;
            $respuesta["mensaje"] = "El kit de mantenimiento ha sido agregado con éxito.";

        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);

    }

    public function mostrarKitsMaquinarias()
    {
        require_once "../Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinaria->id = $_GET["maquinariaId"];
        $kitsMantenimiento = $maquinaria->consultarKits();

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['registros'] = $maquinaria->kits;

        echo json_encode($respuesta);
    }

}

try {
    
    $kitMantenimientoAjax = New KitMantenimientoAjax;

    if ( isset($_POST["accion"]) ) {

        if ( $_POST["accion"] == "editar_detalle" ) {

        	$kitMantenimientoAjax->editar();

        } elseif($_POST["accion"] == "agregarKitMantenimiento") {
            $kitMantenimientoAjax->agregar();
        } else {

        	$respuesta = array();
        	$respuesta["codigo"] = 400;
        	$respuesta["error"] = true;
        	$respuesta["errorMessage"] = "No se encontró la acción solicitada.";
        	echo json_encode($respuesta);
        	exit;

        }

    } else {

        if ( isset($_GET["maquinariaId"])){
            /*=============================================
            TABLA DE kits DE MANTENIMIENTO ASIGNADOS A LA MAQUINARIA
            =============================================*/
            $kitMantenimientoAjax->mostrarKitsMaquinarias();
        }else{
            /*=============================================
            TABLA DE kits DE MANTENIMIENTO
            =============================================*/
            $kitMantenimientoAjax->mostrarTabla();
        }

    }
} catch ( \Exception $e ) {
    $respuesta = array();
    $respuesta["codigo"] = 400;
    $respuesta["error"] = true;
    $respuesta["errorMessage"] = $e->getMessage();
    echo json_encode($respuesta);
    exit;
}