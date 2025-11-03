<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/ChecklistMaquinaria.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\ChecklistMaquinaria;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ChecklistMaquinariaAjax
{

	/*=============================================
	TABLA DE ChecklistMaquinaria
	=============================================*/
	public function mostrarTabla()
	{
        
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        
		$ChecklistMaquinaria = New ChecklistMaquinaria;
        
        if (
            Autorizacion::perfil($usuario, CONST_ADMIN) ||
            Autorizacion::perfil($usuario, 'Jefe de mantto prev') ||
            Autorizacion::perfil($usuario, 'jefe de mantto corr')
        ) {
            $ChecklistMaquinarias = $ChecklistMaquinaria->consultar();
        } else {
            $ChecklistMaquinarias = $ChecklistMaquinaria->consultarOperadores();
        }

        //Validar si es administrador o jefe de mantenimiento y buscar todos los checklist

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo", "title" => "#" ]);
        array_push($columnas, [ "data" => "numeroEconomico", "title" => "Numero Economico" ]);
        array_push($columnas, [ "data" => "descripcion", "title" => "Maquinaria" ]);
        array_push($columnas, [ "data" => "fechaCreacion", "title" => "Fecha de Creacion" ]);
        array_push($columnas, [ "data" => "horometroFinal", "title" => "Horometro Final" ]);
        array_push($columnas, [ "data" => "ubicacion", "title" => "Ubicacion" ]);
        array_push($columnas, [ "data" => "obra", "title" => "Obra" ]);
        array_push($columnas, [ "data" => "estatus", "title" => "Estatus" ]);
        array_push($columnas, [ "data" => "usuarioCreacion", "title" => "Creó" ]);
        array_push($columnas, [ "data" => "acciones", "title" => "Acciones", "orderable" => false, "searchable" => false ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($ChecklistMaquinarias as $key => $value) {
			
        	$rutaEdit = Route::names('checklist-maquinarias.edit', $value['id']);
        	$rutaDestroy = Route::names('checklist-maquinarias.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['descripcionMaquinaria']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                                      "descripcion" => mb_strtoupper(fString($value["descripcionMaquinaria"])),
                                      "fechaCreacion" => fFechaLarga($value["fecha"]),
                                      "horometroFinal" => mb_strtoupper(fString($value["horometroFinal"])),
                                      "ubicacion" => mb_strtoupper(fString($value["ubicacion"])),
                                      "obra" => mb_strtoupper(fString($value["obra"])),
                                      "estatus" => mb_strtoupper(fString($value["estatus"])),
                                      "usuarioCreacion" => mb_strtoupper(fString($value["usuarioCreacion"])),
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
	AGREGAR ChecklistMaquinaria
	=============================================*/	
	public $token;
	public $descripcion;

	public function guardarChecklistMaquinaria(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "checklist-maquinaria", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear checklist.";

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

        if ( $respuesta["error"] ) {

        	echo json_encode($respuesta);
        	return;

        }

		$ChecklistMaquinaria = New ChecklistMaquinaria;
        $ChecklistMaquinaria->consultar(null, $this->checklistMaquinaria);
		$respuesta["respuesta"] = false;
        $datos= json_decode($_POST["data"]);

        foreach ($datos as $key => $value) {
            
            foreach ($value as $key2 => $value2) {
                
                if ( $key2 == "observaciones" ) {
                    $datos= [
                        "sectionId" => $key,
                        "observaciones" => $value2,
                        "checklist_maquinaria" => $this->checklistMaquinaria
                    ];
                    $response = $ChecklistMaquinaria->guardarObservaciones($datos);
                } else {
                    $datos= [
                        "tareaId" => $key2,
                        "respuesta" => $value2,
                        "checklist_maquinaria" => $this->checklistMaquinaria
                    ];

                    $response = $ChecklistMaquinaria->guardar($datos);
                }
                

            }

        }

        $ChecklistMaquinaria->cambiarEstatus();

        // Crear el nuevo registro
        if ( $response ) {

            $respuesta["respuestaMessage"] = "El checklist fue guardado correctamente.";
            $respuesta["error"] = false;
            
        } else {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Hubo un error al intentar grabar el registro, intente de nuevo.";

        }

		echo json_encode($respuesta);

	}

    public function agregarSeccion(){

		$respuesta["error"] = false;

		// Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
			if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "maquinaria-tipos", "crear") ) {

	            $respuesta["error"] = true;
				$respuesta["errorMessage"] = "No está autorizado a crear nuevas secciones.";

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

        require_once "../Models/ChecklistSection.php";
		$ChecklistSection = New \App\Models\ChecklistSection;

		$datos["descripcion"] = $this->descripcion;

		// Validar campo (Descripcion, tamaño)

		$respuesta["respuesta"] = false;

		// Validar campo (que no exista en la BD)
		if ( $ChecklistSection->consultar("descripcion", $this->descripcion) ) {

			$respuesta["error"] = true;
			$respuesta["errorMessage"] = "Esta descripcion ya ha sido registrada.";

		} else {

			// Crear el nuevo registro
	        if ( $ChecklistSection->crear($datos) ) {

	        	$respuesta["respuestaMessage"] = "El modelo fue creado correctamente.";

				// Si lo pudo crear, consultar el registro para obtener el Id en el Ajax
	        	$respuesta["respuesta"] = $ChecklistSection->consultar("descripcion", $this->descripcion);

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

    public function subirImagen(){
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "checklist-maquinaria", "crear") ) throw new \Exception("No está autorizado a agregar imagenes.");


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

            $ChecklistMaquinaria = new ChecklistMaquinaria;
            $response = $ChecklistMaquinaria->guardarImagenes($_POST["checklistMaquinaria"]);

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Las imagenes fueron agregadas correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    public function autorizarChecklist(){

        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            $auth = $_POST["auth"] == "indheca" ? "auth-indheca-cl" : "auth-cliente-cl";
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, $auth, 'ver') ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a autorizar checklist.";

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

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $ChecklistMaquinaria = New ChecklistMaquinaria;
        if ( !$ChecklistMaquinaria->autorizar($this->checklistMaquinaria, $_POST["auth"]) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "Hubo un error al intentar autorizar el checklist, intente de nuevo.";

        } else {

            $respuesta["respuestaMessage"] = "El checklist fue autorizado correctamente.";
            $respuesta["error"] = false;

        }

        echo json_encode($respuesta);

    }

}

$ChecklistMaquinariaAjax = new ChecklistMaquinariaAjax();

if ( isset($_POST["accion"]) ) {
    if ( $_POST["accion"] == "guardarChecklistMaquinaria" ) {

        /*=============================================
        Guardar ChecklistMaquinaria
        =============================================*/
        $ChecklistMaquinariaAjax->token = $_POST["_token"];
        $ChecklistMaquinariaAjax->checklistMaquinaria = $_POST["checklistMaquinaria"];
        $ChecklistMaquinariaAjax->guardarChecklistMaquinaria();

    } elseif ( $_POST["accion"] == "agregarSeccion"  ) {
        /*=============================================
        Agregar Seccion
        =============================================*/
        $ChecklistMaquinariaAjax->token = $_POST["_token"];
        $ChecklistMaquinariaAjax->descripcion = $_POST["descripcion"];
        $ChecklistMaquinariaAjax->agregarSeccion();

    } elseif ( $_POST["accion"] == "subirImagen" ) {
        /*=============================================
        Subir Imagen
        =============================================*/
        
        $ChecklistMaquinariaAjax->checklistMaquinaria = $_POST["checklistMaquinaria"];
        $ChecklistMaquinariaAjax->subirImagen();

    } elseif ( $_POST["accion"] == "autorizarCheckList" ) {
        /*=============================================
        Autorizar Checklist
        =============================================*/
        $ChecklistMaquinariaAjax->token = $_POST["_token"];
        $ChecklistMaquinariaAjax->checklistMaquinaria = $_POST["checklistMaquinaria"];
        $ChecklistMaquinariaAjax->autorizarChecklist();
    }else {

		echo 'peticion erronea';

	}

} else {

    /*=============================================
    TABLA DE CHECKLIST MAQUINARIA
    =============================================*/
    $ChecklistMaquinariaAjax->mostrarTabla();

}