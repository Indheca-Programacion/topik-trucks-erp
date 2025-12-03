<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Empresa.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveEmpresasSesionRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Empresa;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveEmpresasSesionRequest;

class EmpresaAjax
{

	/*=============================================
	TABLA DE EMPRESAS
	=============================================*/
	public function mostrarTabla()
	{
		$empresa = New Empresa;
        $empresas = $empresa->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "razonSocial" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "rfc" ]);
        array_push($columnas, [ "data" => "municipio" ]);
        array_push($columnas, [ "data" => "estado" ]);
        array_push($columnas, [ "data" => "pais" ]);
        array_push($columnas, [ "data" => "nomenclaturaOT" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($empresas as $key => $value) {
        	$rutaEdit = Route::names('empresas.edit', $value['id']);
        	$rutaDestroy = Route::names('empresas.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['razonSocial']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "razonSocial" => fString($value["razonSocial"]),
        							  "nombreCorto" => fString($value["nombreCorto"]),
        							  "rfc" => fString($value["rfc"]),
        							  "municipio" => fString($value["municipio"]),
        							  "estado" => fString($value["estado"]),
        							  "pais" => fString($value["pais"]),
        							  "nomenclaturaOT" => fString($value["nomenclaturaOT"]),
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
	AGREGAR MARCA
	=============================================*/	

	public $token;
	public $nombre;

	public function guardarSesion(){

		try {
			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

				$usuario = New Usuario;
				$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            // if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "inventarios", "crear") ) throw new \Exception("No está autorizado a crear inventarios.");

			$request = SaveEmpresasSesionRequest::validated();

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

			$empresa = New Empresa;
            if ( !$empresa->crearSesion($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['respuestaMessage'] = 'Se guardo con exito la sesión';

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);


	}

	public function actualizarSesion(){

		try {
			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

				$usuario = New Usuario;
				$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            // if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "inventarios", "crear") ) throw new \Exception("No está autorizado a crear inventarios.");

			$request = SaveEmpresasSesionRequest::validated();

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

			$empresa = New Empresa;
            if ( !$empresa->actualizarSesion($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['message'] = 'Se actualizo con exito la sesión';

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }
		echo json_encode($respuesta);
	}

}


try {

	$empresaAjax = new EmpresaAjax();

    if ( isset($_POST["accion"]) ) {
		if ( $_POST["accion"] == "guardarSesion" ) {
            $empresaAjax->guardarSesion();
		}else if ( $_POST["accion"] == "actualizarSesion" ) {
            $empresaAjax->actualizarSesion();
		} else {
			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => 'Acción no encontrada'
			];
		}
    } else if ( isset($_GET["accion"]) ) {
		if ( $_GET["accion"] == "guardar" ) {
		} else {
		}
    }else{
		$empresaAjax -> mostrarTabla();
	}
} catch (\Error $e) {
    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];
    echo json_encode($respuesta);
}