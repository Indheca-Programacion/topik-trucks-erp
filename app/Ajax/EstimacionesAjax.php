<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Generadores.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Generadores;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class EstimacionesAjax
{

	/*=============================================
	TABLA DE ESTATUS
	=============================================*/
	public function mostrarTabla()
	{
		$generador = new Generadores;
        $generadores = $generador->consultarSinFirma();
        $columnas = array();

        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "obra" ]);
		array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "observaciones" ]);
        array_push($columnas, [ "data" => "user_crecion" ]);
        array_push($columnas, [ "data" => "mes" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $registros = array();

        $token = createToken();
        setlocale(LC_TIME, 'es_ES.UTF-8');
        foreach ($generadores as $key => $value) {
            $rutaEdit = Route::names('estimaciones.edit', $value['id']);
            $folio = fString($value["id"]);
            list($year, $month, $day) = explode('-', $value["mes"]);
            $monthName = fNombreMes($month);
			// Determinar el estatus de la firma
			if (is_null($value["firmado"]) && is_null($value["estimacionFirma"])) {
				$estatus = "Pendiente de firma";
			} elseif (is_null($value["firmado"])) {
				$estatus = "Pendiente de firma de generador";
			} elseif (is_null($value["estimacionFirma"])) {
				$estatus = "Pendiente firma de estimación";
			} else {
				$estatus = "Firmado";
			}
            array_push($registros,[
                "consecutivo" => ($key + 1),
                "folio" => "GEN-".$value["folio"],
                "obra" => mb_strtoupper(fString($value["obra"])),
                "ubicacion" => mb_strtoupper(fString($value["ubicacion"])),
                "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                "user_crecion" => mb_strtoupper(fString($value["nombreCompleto"])),
                "mes" => $monthName." ".$year,
				"estatus" => $estatus,
                "fecha_creacion" => fFechaLarga($value["fechaCreacion"]),
                "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-info'>Procesar</i></a>"
            ]
            );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

	/*=============================================
	AGREGAR ESTATUS
	=============================================*/	
	public $token;
	public $descripcion;

	public function agregar(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "estatus", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear nuevos Estatus.";

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
		if ( !Validacion::validar("descripcion", $this->descripcion, ['max', '30']) ) {

            $respuesta["error"] = true;
			$respuesta["errorMessage"] = "La descripcion debe ser máximo de 30 caracteres.";

        }

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$estatus = New Estatus;

		$datos["descripcion"] = $this->descripcion;
		$datos["nombreCorto"] = '';

		// Validar campo (Descripcion, tamaño)

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		if ( $estatus->consultar("descripcion", $this->descripcion) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta descripcion ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $estatus->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "El estatus fue creado correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $estatus->consultar("descripcion", $this->descripcion);

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
	MANDAR A CORREGIR ESTIMACION
	=============================================*/
	public $id;
	public function mandarCorregir(){
		$respuesta["error"] = false;

		// Validar Autorizacion
		$usuario = New Usuario;
		if ( usuarioAutenticado() ) {

			$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
			
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "estimaciones", "corregir") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a mandar a corregir esta estimación.";

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

		// Validar Id
		if ( !isset($this->id) || !Validacion::validar("id", $this->id, ['required']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "No fue proporcionado un Id.";
		
		} elseif ( !Validacion::validar("id", $this->id, ['integer']) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "El Id proporcionado no es válido.";

		}

		if ( $respuesta["error"] ) {

			echo json_encode($respuesta);
			return;

		}

		$generador = New Generadores;

		if ( !$generador->consultar("id", $this->id) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "No se encontró la estimación a corregir.";

			echo json_encode($respuesta);
			return;

		}

		$generador->id = $this->id;
		$generador->observaciones = $this->observaciones;
		if ( !$generador->actualizarObservacion()) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Hubo un error al intentar mandar a corregir la estimación, intente de nuevo.";

			echo json_encode($respuesta);
			return;

		}

		echo json_encode($respuesta);
		return;
	}

}

$estimacionesAjax = new EstimacionesAjax();
if (isset($_POST["accion"])){
	if ( $_POST["accion"] == "mandarCorregir"){
		/*=============================================
		MANDAR A CORREGIR ESTIMACION
		=============================================*/	
		$estimacionesAjax->token = $_POST["_token"];
		$estimacionesAjax->id = $_POST["generadorId"];
		$estimacionesAjax->observaciones = $_POST["observacion"];
		$estimacionesAjax->mandarCorregir();
	}
}elseif ( isset($_POST["nombreEstatus"]) ) {

	/*=============================================
	AGREGAR ESTATUS
	=============================================*/	
	$estimacionesAjax->token = $_POST["_token"];
	$estimacionesAjax->descripcion = $_POST["nombreEstatus"];
	$estimacionesAjax->agregar();

} else {

	/*=============================================
	TABLA DE ESTIMACIONES
	=============================================*/
	$estimacionesAjax->mostrarTabla();

}