<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Marca.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Marca;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class MarcaAjax
{

	/*=============================================
	TABLA DE MARCAS
	=============================================*/
	public function mostrarTabla()
	{
		$marca = New Marca;
        $marcas = $marca->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($marcas as $key => $value) {
        	$rutaEdit = Route::names('marcas.edit', $value['id']);
        	$rutaDestroy = Route::names('marcas.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
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
	AGREGAR MARCA
	=============================================*/	
	public $token;
	public $descripcion;

	public function agregar(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "marcas", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear nuevas Marcas.";

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

        // Validar Tamaño del campo
		if ( !Validacion::validar("descripcion", $this->descripcion, ['max', '60']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La descripcion debe ser máximo de 60 caracteres.";

        }

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$marca = New Marca;

		$datos["descripcion"] = $this->descripcion;

		// Validar campo (Descripcion, tamaño)

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		if ( $marca->consultar("descripcion", $this->descripcion) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta descripcion ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $marca->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "La marca fue creada correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $marca->consultar("descripcion", $this->descripcion);

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

}

$marcaAjax = new MarcaAjax();

if ( isset($_POST["nombreMarca"]) ) {

	/*=============================================
	AGREGAR MARCA
	=============================================*/
	$marcaAjax->token = $_POST["_token"];
	$marcaAjax->descripcion = $_POST["nombreMarca"];
	$marcaAjax->agregar();

} else {

	/*=============================================
	TABLA DE MARCAS
	=============================================*/
	$marcaAjax->mostrarTabla();

}