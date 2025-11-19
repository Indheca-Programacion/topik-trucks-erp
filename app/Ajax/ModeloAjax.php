<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Modelo.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Modelo;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ModeloAjax
{

	/*=============================================
	TABLA DE MODELOS
	=============================================*/
	public function mostrarTabla()
	{
		$modelo = New Modelo;
        $modelos = $modelo->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($modelos as $key => $value) {
        	$rutaEdit = Route::names('modelos.edit', $value['id']);
        	$rutaDestroy = Route::names('modelos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "marca" => fString($value["marcas.descripcion"]),
        							  "descripcion" => fString($value["descripcion"]),
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
	AGREGAR MODELO
	=============================================*/	
	public $token;
	public $marcaId;
	public $descripcion;

	public function agregar(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "modelos", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear nuevos Modelos.";

	        }
        
        } else {

    	    $respuesta["error"] = true;
			$respuesta["errorMessage"] = "Usuario no Autenticado, intente de nuevo.";

        }

		// Validar Token
		if ( !isset($this->token) || !Validacion::validar("_token", $this->token, ['required']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "No fue proporcionado un Token.";
        
        } elseif ( !Validacion::validar("_token", $this->token, ['token']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "El Token proporcionado no es válido.";

        }

		// Validar campo marcaId (que exista en la BD)
		if ( !Validacion::validar("marcaId", $this->marcaId, ['exists', CONST_BD_APP.'.marcas', 'id']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La marca seleccionada no existe.";

        }

        // Validar Tamaño del campo
		if ( !Validacion::validar("descripcion", $this->descripcion, ['max', '60']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La descripcion debe ser máximo de 60 caracteres.";

        }

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$modelo = New Modelo;

		$datos["descripcion"] = $this->descripcion;
		$datos["marcaId"] = $this->marcaId;

		// Validar campo (Descripcion, tamaño)

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		if ( $modelo->consultar("descripcion", $this->descripcion) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta descripcion ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $modelo->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "El modelo fue creado correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $modelo->consultar("descripcion", $this->descripcion);

	        	if ( !$respuesta["respuesta"] ) {

	        		$respuesta["error"] = true;
					$respuesta["errorMessage"] = "De favor refresque la pantalla para ver el nuevo registro.";

	        	}
	        	
	        } else {

	        	$respuesta["error"] = true;
				$respuesta["errorMessage"] = "Hubo un error al intentar grabar el registro, intente de nuevo.";

	        }

		}

		echo json_encode($respuesta);

	}

	/*=============================================
	CONSULTAR MODELOS DE LA MARCA
	=============================================*/	
	public function consultar()
	{
        $modelos = \App\Conexion::queryAll(CONST_BD_APP, "SELECT MD.*, M.descripcion AS 'marcas.descripcion' FROM modelos MD INNER JOIN marcas M ON MD.marcaId = M.id WHERE MD.marcaId = $this->marcaId ORDER BY M.descripcion, MD.descripcion", $error);

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['modelos'] = $modelos;

        echo json_encode($respuesta);
	}

}

$modeloAjax = new ModeloAjax();

if ( isset($_GET["marcaId"]) ) {
	
	/*=============================================
	CONSULTAR MODELOS DE LA MARCA
	=============================================*/	
	$modeloAjax->marcaId = $_GET["marcaId"];
	$modeloAjax->consultar();

} elseif ( isset($_POST["nombreModelo"]) ) {

	/*=============================================
	AGREGAR MODELO
	=============================================*/	
	$modeloAjax->token = $_POST["_token"];
	$modeloAjax->marcaId = $_POST["marcaId"];
	$modeloAjax->descripcion = $_POST["nombreModelo"];
	$modeloAjax->agregar();

} else {

	/*=============================================
	TABLA DE MODELOS
	=============================================*/
	$modeloAjax->mostrarTabla();

}