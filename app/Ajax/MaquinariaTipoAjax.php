<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/MaquinariaTipo.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveChecklistTareaRequest.php";
require_once "../Models/ChecklistTarea.php";

use App\Route;
use App\Models\Usuario;
use App\Models\MaquinariaTipo;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveChecklistTareaRequest;
use App\Models\ChecklistTarea;

class MaquinariaTipoAjax
{

	/*=============================================
	TABLA DE TIPOS DE MAQUINARIA
	=============================================*/
	public function mostrarTabla()
	{
		$maquinariaTipo = New MaquinariaTipo;
        $maquinariaTipos = $maquinariaTipo->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "nombreCorto" ]);
        array_push($columnas, [ "data" => "orden" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($maquinariaTipos as $key => $value) {
        	$rutaEdit = Route::names('maquinaria-tipos.edit', $value['id']);
        	$rutaDestroy = Route::names('maquinaria-tipos.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcion']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "descripcion" => fString($value["descripcion"]),
        							  "nombreCorto" => fString($value["nombreCorto"]),
        							  "orden" => $value["orden"],
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
	AGREGAR TIPO DE MAQUINARIA
	=============================================*/	
	public $token;
	public $descripcion;

	public function agregar(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "maquinaria-tipos", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear nuevos Tipos de Maquinaria.";

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

		$maquinariaTipo = New MaquinariaTipo;

		$datos["descripcion"] = $this->descripcion;
		$datos["nombreCorto"] = '';
		$datos["orden"] = 0;

		// Validar campo (Descripcion, tamaño)

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		if ( $maquinariaTipo->consultar("descripcion", $this->descripcion) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta descripcion ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $maquinariaTipo->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "El tipo de maquinaria fue creada correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $maquinariaTipo->consultar("descripcion", $this->descripcion);

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
	AGREGAR CHECKLIST
	=============================================*/
	public function agregarChecklist(){
		try {

			$request = SaveChecklistTareaRequest::validated();
			
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

			$checklistTarea = new ChecklistTarea;

			// Crear el nuevo registro
			
			if ( !$checklistTarea->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['respuestaMessage'] = 'Se creó con exito la tarea';

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);
	}

	/*=============================================	
	ELIMINAR CHECKLIST
	=============================================*/
	public function eliminarChecklist(){
		try {

			$checklistTarea = new ChecklistTarea;
			$checklistTarea->id = $_POST["id"];

			if ( !$checklistTarea->eliminar() ) throw new \Exception("Hubo un error al intentar eliminar el registro, intente de nuevo.");

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['respuestaMessage'] = 'Se eliminó con exito la tarea';

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

$maquinariaTipoAjax = new MaquinariaTipoAjax();

if ( isset($_POST["nombreMaquinariaTipo"]) ) {

	/*=============================================
	AGREGAR TIPO DE MAQUINARIA
	=============================================*/	
	$maquinariaTipoAjax->token = $_POST["_token"];
	$maquinariaTipoAjax->descripcion = $_POST["nombreMaquinariaTipo"];
	$maquinariaTipoAjax->agregar();
	}elseif ( isset($_POST["accion"]) ) {
		if ($_POST["accion"] == "addChecklist") {
			/*=============================================
			Agregar tarea
			=============================================*/
			$maquinariaTipoAjax->agregarChecklist();
		}else if ($_POST["accion"] == "deleteChecklist") {
			/*=============================================
			Eliminar tarea
			=============================================*/			
			$maquinariaTipoAjax->eliminarChecklist();
		}
} else {

	/*=============================================
	TABLA DE TIPOS DE MAQUINARIA
	=============================================*/
	$maquinariaTipoAjax->mostrarTabla();

}