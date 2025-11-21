<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Vendedor.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Vendedor;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class VendedorAjax
{

	/*=============================================
	TABLA DE VENDEDOR
	=============================================*/
	public function mostrarTabla()
	{
		$vendedor = New Vendedor;
        $vendedores = $vendedor->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "correo" ]);
        array_push($columnas, [ "data" => "telefono" ]);
        array_push($columnas, [ "data" => "zona" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($vendedores as $key => $value) {
        	$rutaEdit = Route::names('vendedores.edit', $value['id']);
        	$rutaDestroy = Route::names('vendedores.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "nombre" => fString($value["nombreCompleto"]),
                                      "correo" => fString($value["correo"]),
                                      "telefono" => fString($value["telefono"]),
                                      "zona" => fString($value["zona"]),
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

/*=============================================
TABLA DE VENDEDOR
=============================================*/
$activar = new VendedorAjax();
$activar -> mostrarTabla();
