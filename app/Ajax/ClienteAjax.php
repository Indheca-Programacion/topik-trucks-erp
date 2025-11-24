<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Cliente.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveClienteRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Cliente;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveClienteRequest;

class ClienteAjax
{

	/*=============================================
	TABLA DE CLIENTES
	=============================================*/
	public function mostrarTabla()
	{
		$color = New Color;
        $colores = $color->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($colores as $key => $value) {
			
        	$rutaEdit = Route::names('colores.edit', $value['id']);
        	$rutaDestroy = Route::names('colores.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "descripcion" => fString($value["descripcion"]),
        							  "nombreCorto" => fString($value["nombreCorto"]),
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

	/*=============================================
	AGREGAR CLIENTE
	=============================================*/	
	public $token;
	public $descripcion;

	public function crear(){

        try {
            $request = SaveClienteRequest::validated();

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }

            $cliente = New Cliente;

            if(!$cliente->crear($request)) throw new \Exception("Error al crear el cliente, intente de nuevo.");

            $cliente->consultar(null, $cliente->id);
            $clienteOption = [
                "id" => $cliente->id,
                "nombre" => $cliente->nombreCompleto
            ];

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['respuestaMessage'] = "Cliente creado con exito";
            $respuesta['cliente'] = $clienteOption;

        }catch (\Exception $e) {
            $respuesta = [
                'codigo' => 500, // Código de error para problemas del servidor
                'error' => true,
                'errorMessage' => $e->getMessage(), // El mensaje del error
                'errorCode' => $e->getCode() // Código específico de la excepción, si existe
            ];
        }

        echo json_encode($respuesta);

	}

}

$clienteAjax = new ClienteAjax();

if ( isset($_POST["nombreCompleto"]) ) {

    /*=============================================
    AGREGAR CLIENTE
    =============================================*/ 
    $clienteAjax->token = $_POST["_token"];
    $clienteAjax->crear();

} else {

    /*=============================================
    TABLA DE CLIENTES
    =============================================*/
    $clienteAjax->mostrarTabla();

}