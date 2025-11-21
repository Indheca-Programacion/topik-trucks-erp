<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Presupuesto.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Presupuesto;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class PresupuestoAjax
{

	/*=============================================
	TABLA DE PRESUPUESTOS
	=============================================*/
	public function mostrarTabla()
	{

		try {
			
			if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

			$usuario = New Usuario;
			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "presupuesto", "ver") ) throw new \Exception("No está autorizado a agregar presupuestos");

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

            $presupuesto = New Presupuesto;
            $presupuestos = $presupuesto->consultar();

            $columnas = array();
            array_push($columnas, [ "data" => "consecutivo" ]);
            array_push($columnas, [ "data" => "maquinaria" ]);
            array_push($columnas, [ "data" => "cliente" ]);
            array_push($columnas, [ "data" => "fuente" ]);
            array_push($columnas, [ "data" => "folio_servicios" ]);
            array_push($columnas, [ "data" => "fecha_creacion" ]);
            array_push($columnas, [ "data" => "creo" ]);
            array_push($columnas, [ "data" => "acciones" ]);
            
            $token = createToken();
            
            $registros = array();

            
            foreach ($presupuestos as $key => $value) {
                

                $id_presupuesto = $value['id'];
                $rutaEdit = Route::names('presupuesto.edit', $value['id']);
                $rutaDestroy = Route::names('presupuesto.destroy', $value['id']);

                array_push( $registros, [ "consecutivo" => ($key + 1),
                                        "maquinaria" => fString($value["maquinaria"]),
                                        "cliente" => fString($value["cliente"]),
                                        "fuente" => fString($value["fuente"]),
                                        "folio_servicios" => fString($value["folio_servicios"]),
                                        "fecha_creacion" => fString($value["fecha_creacion"]),
                                        "creo" => fString($value["creo"]),
                                        "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                                        <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                                            <input type='hidden' name='_method' value='DELETE'>
                                                            <input type='hidden' name='_token' value='{$token}'>
                                                                <button type='button' class='btn btn-xs btn-danger eliminar'>
                                                                    <i class='far fa-times-circle'></i>
                                                                </button>
                                                        </form>" ] );
            }

            
            $respuesta = array();
            $respuesta['codigo'] = 200;
            $respuesta['error'] = false;
            $respuesta['datos']['columnas'] = $columnas;
            $respuesta['datos']['registros'] = $registros;


		} catch (\Exception $e) {
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

$puestoAjax = new PuestoAjax();

if ( isset($_POST["accion"]) ) {

	if($_POST["accion"] == "asignarPuesto" ){

		/*=============================================
		ASIGNAR PUESTO
		=============================================*/ 
		$puestoAjax->token = $_POST["_token"];
		$puestoAjax->id_puesto = $_POST["id_puesto"];
		$puestoAjax->id_usuario = $_POST["id_usuario"];
		$puestoAjax->id_zona = $_POST["id_zona"];

		$puestoAjax->agregar();
	}else if($_POST["accion"] == "eliminarPuestoAsignado" ){

		/*=============================================
		DESIGNAR PUESTO
		=============================================*/ 
		$puestoAjax->token = $_POST["_token"];
		$puestoAjax->id_puesto_usuario = $_POST["id_puesto_usuario"];

		$puestoAjax->eliminarPuesto();
	}

} 
else if ( isset($_GET["accion"]) ) {

    /*=============================================
    MOSTRAR TABLA DE PUESTOS DEL USUARIO
    =============================================*/ 
    $puestoAjax->id_usuario = $_GET["id_usuario"];
    $puestoAjax->mostrarTablaPuestoUsuario();

} 
else {

    /*=============================================
    TABLA DE PUESTOS
    =============================================*/
    $puestoAjax->mostrarTabla();

}