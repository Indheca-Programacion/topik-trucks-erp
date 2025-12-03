<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Presupuesto.php";
require_once "../Models/ServicioPartida.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveServicioPartidaRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Presupuesto;
use App\Models\ServicioPartida;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveServicioPartidaRequest;

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
                $rutaEdit = Route::names('presupuestos.edit', $value['id']);
                $rutaDestroy = Route::names('presupuestos.destroy', $value['id']);

                array_push( $registros, [ "consecutivo" => ($key + 1),
                                        "maquinaria" => fString($value["maquinaria"]),
                                        "cliente" => fString($value["cliente"]),
                                        "fuente" => fString($value["fuente"]),
                                        "folio_servicios" => fString($value["folio_servicios"]),
                                        "fecha_creacion" => fFechaLarga($value["fechaCreacion"]),
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

    /*=============================================
    Agregar Partida al Servicio
    =============================================*/
    public function agregar()
    {
        try {
            
            $request = SaveServicioPartidaRequest::validated();
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

            require_once "../Models/ServicioPartida.php";
            $partida = New \App\Models\ServicioPartida;
            $respuesta = $partida->crear($request);

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

    /*=============================================
    Eliminar Partida del Servicio
    ==============================================*/
    public function eliminar()
    {
        try {
            
            $request = SaveServicioPartidaRequest::validated();
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

            require_once "../Models/ServicioPartida.php";
            $partida = New \App\Models\ServicioPartida;
            $partida->id = $request['id'];
            $respuesta = $partida->eliminar();

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

$presupuestoAjax = new PresupuestoAjax();

if ( isset($_POST["accion"]) ) {

	if($_POST["accion"] == "agregarPartida" ){

		/*=============================================
		Agregar Partida al Servicio
		=============================================*/ 

		$presupuestoAjax->agregar();
	}
    else if($_POST["accion"] == "eliminarPartida" ){

		/*=============================================
		Eliminar Partida del Servicio
		=============================================*/ 

		$presupuestoAjax->eliminar();
	}
    else {
        /*=============================================
        Accion desconocida
        =============================================*/ 
        $respuesta = [
            'codigo' => 400,
            'error' => true,
            'errorMessage' => 'Acción desconocida'
        ];
        echo json_encode($respuesta);
    }
} 
else if ( isset($_GET["accion"]) ) {

    /*=============================================
    MOSTRAR TABLA DE PUESTOS DEL USUARIO
    =============================================*/ 
    $presupuestoAjax->id_usuario = $_GET["id_usuario"];
    $presupuestoAjax->mostrarTablaPuestoUsuario();
} 
else {

    /*=============================================
    TABLA DE PRESUPUESTOS
    =============================================*/
    $presupuestoAjax->mostrarTabla();

}