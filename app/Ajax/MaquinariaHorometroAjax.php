<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/MaquinariaHorometro.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\MaquinariaHorometro;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class MaquinariaHorometroAjax
{

	/*=============================================
	AGREGAR HOROMETRO
	=============================================*/
	public $token;
	public $maquinariaId;
	public $fecha;
	public $horometroInicial;
	public $kilometrajeInicial;
	public $horometroFinal;
	public $kilometrajeFinal;
	public $archivo;

	public function agregar()
	{

		$respuesta["error"] = false;

		// $respuesta["archivo"] = $this->archivo;
		// $respuesta["tmp_name"] = $this->archivo['tmp_name'];
		// $respuesta["this"] = $this;
		// echo json_encode($respuesta);
  //       return;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "maquinarias", "actualizar") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a actualizar Maquinarias.";

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

		// Validar existencia del campo maquinariaId
		if ( !Validacion::validar("maquinariaId", $this->maquinariaId, ['exists', CONST_BD_APP.'.maquinarias', 'id']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La maquinaria no existe.";

        } elseif ( !Validacion::validar("horometroInicial", $this->horometroInicial, ['integer']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El campo Horómetro Inicial debe ser de tipo Numérico.";

		} elseif ( !Validacion::validar("kilometrajeInicial", $this->kilometrajeInicial, ['integer']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El campo Kilometraje Inicial debe ser de tipo Numérico.";

		} elseif ( !Validacion::validar("horometroFinal", $this->horometroFinal, ['integer']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El campo Horómetro Final debe ser de tipo Numérico.";

		} elseif ( !Validacion::validar("kilometrajeFinal", $this->kilometrajeFinal, ['integer']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El campo Kilometraje Final debe ser de tipo Numérico.";

		} elseif ( !Validacion::validar("fecha", $this->fecha, ['date']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "La fecha del registro no es válida.";

		} elseif ( !Validacion::validar("archivo", $this->archivo['tmp_name'], ['required']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El archivo es obligatorio.";

		} elseif ( !Validacion::validar("archivo", $this->archivo, ['type', 'application/pdf']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El archivo debe ser PDF.";

		} elseif ( !Validacion::validar("archivo", $this->archivo, ['maxSize', 4000000]) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El tamaño del archivo debe ser máximo de 4Mb.";

		} 

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$maquinariaHorometro = New MaquinariaHorometro;

		// $datos["maquinariaId"] = $this->maquinariaId;
		$datos["fecha"] = $this->fecha;
		$datos["horometroInicial"] = $this->horometroInicial;
		$datos["kilometrajeInicial"] = $this->kilometrajeInicial;
		$datos["horometroFinal"] = $this->horometroFinal;
		$datos["kilometrajeFinal"] = $this->kilometrajeFinal;
		$datos["archivo"] = $this->archivo;

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		$maquinariaHorometro->maquinariaId = $this->maquinariaId;
		$maquinariaHorometro->fecha = $this->fecha;
		if ( $maquinariaHorometro->consultar() ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta fecha ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $maquinariaHorometro->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "El registro fue creado correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $maquinariaHorometro->consultar();

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
	ELIMINAR HOROMETRO
	=============================================*/
	public function eliminar()
	{

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "maquinarias", "eliminar") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a eliminar Maquinarias.";

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

		// Validar existencia del campo maquinariaId
		if ( !Validacion::validar("maquinariaId", $this->maquinariaId, ['exists', CONST_BD_APP.'.maquinarias', 'id']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La maquinaria no existe.";

        }

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$maquinariaHorometro = New MaquinariaHorometro;

		// $datos["maquinariaId"] = $this->maquinariaId;
		// $datos["fecha"] = $this->fecha;

		$respuesta["respuesta"] = false;

		// Validar campo (que exista en la BD)
		$maquinariaHorometro->maquinariaId = $this->maquinariaId;
		$maquinariaHorometro->fecha = $this->fecha;
		if ( !$maquinariaHorometro->consultar(true) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta fecha no ha sido registrada.";

		} else {

			// Eliminar el registro
	        if ( $maquinariaHorometro->eliminar() ) {

	        	$respuesta["respuestaMessage"] = "El registro fue eliminado correctamente.";
	        	$respuesta["respuesta"] = true;
	        	
	        } else {

	        	$respuesta["error"] = true;
				$respuesta["errorMessage"] = "Hubo un error al intentar eliminar el registro, intente de nuevo.";

	        }

		}

		echo json_encode($respuesta);

	}

	/*=============================================
	DESCARGAR HOROMETRO
	=============================================*/
	public function descargar()
	{

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "maquinarias", "ver") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a ver Maquinarias.";

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

		// Validar existencia del campo maquinariaId
		if ( !Validacion::validar("maquinariaId", $this->maquinariaId, ['exists', CONST_BD_APP.'.maquinarias', 'id']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La maquinaria no existe.";

        }

		if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$maquinariaHorometro = New MaquinariaHorometro;

		$respuesta["respuesta"] = false;

		// Validar campo (que exista en la BD)
		$maquinariaHorometro->maquinariaId = $this->maquinariaId;
		$maquinariaHorometro->fecha = $this->fecha;
		if ( !$maquinariaHorometro->consultar(true) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta fecha no ha sido registrada.";

		} else {

			$respuesta["respuesta"] = $maquinariaHorometro;

		}

		echo json_encode($respuesta);

	}

}

if ( isset($_POST["accion"]) && isset($_POST["maquinariaId"]) && isset($_POST["fecha"])) {

	$maquinariaHorometroAjax = New MaquinariaHorometroAjax;
	$maquinariaHorometroAjax->token = $_POST["_token"];
	$maquinariaHorometroAjax->maquinariaId = $_POST["maquinariaId"];
	$maquinariaHorometroAjax->fecha = $_POST["fecha"];

	if ( $_POST["accion"] == "agregar" ) {

		/*=============================================
		AGREGAR HOROMETRO
		=============================================*/
		$maquinariaHorometroAjax->horometroInicial = $_POST["horometroInicial"];
		$maquinariaHorometroAjax->kilometrajeInicial = $_POST["kilometrajeInicial"];
		$maquinariaHorometroAjax->horometroFinal = $_POST["horometroFinal"];
		$maquinariaHorometroAjax->kilometrajeFinal = $_POST["kilometrajeFinal"];
		$maquinariaHorometroAjax->archivo = $_FILES['archivo'];
		$maquinariaHorometroAjax->agregar();

	} elseif ( $_POST["accion"] == "eliminar" ) {

		/*=============================================
		ELIMINAR HOROMETRO
		=============================================*/
		$maquinariaHorometroAjax->eliminar();
		
	} elseif ( $_POST["accion"] == "descargar" ) {

		/*=============================================
		DESCARGAR HOROMETRO
		=============================================*/
		$maquinariaHorometroAjax->descargar();
		
	}

}