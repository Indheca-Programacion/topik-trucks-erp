<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Resguardo.php";
require_once "../Models/ResguardoArchivo.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveResguardoRequest.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Resguardo;
use App\Models\ResguardoArchivo;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Requests\SaveResguardoRequest;

class ResguardoAjax
{

	/*=============================================
	TABLA DE RESGUARDOS
	=============================================*/
	public function mostrarTabla()
	{
		$resguardo = New Resguardo;
        $resguardos = $resguardo->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "asignado" ]);
        array_push($columnas, [ "data" => "entrego" ]);
        array_push($columnas, [ "data" => "fechaEntregado" ]);
        array_push($columnas, [ "data" => "observacion" ]);
        array_push($columnas, [ "data" => "almacen" ]);
        array_push($columnas, [ "data" => "salidaId" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();

        $registros = array();
        foreach ($resguardos as $key => $value) {
        	$rutaEdit = Route::names('resguardos.edit', $value['id']);
        	$rutaDestroy = Route::names('resguardos.destroy', $value['id']);
            $rutaPrint = Route::names('resguardos.print', $value['id']);
			$folio = mb_strtoupper(fString($value["id"]));
            
        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "asignado" => mb_strtoupper(fString($value["nombreRecibio"])),
        							  "entrego" => mb_strtoupper(fString($value["nombreEntrego"])),
        							  "fechaEntregado" => fFechaLargaHora($value["fechaEntrego"]),
        							  "observacion" => mb_strtoupper(fString($value["observaciones"])),
        							  "almacen" => mb_strtoupper(fString($value["nombreAlmacen"])),
        							  "salidaId" => mb_strtoupper(fString($value["salidaId"])),

        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                                     <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
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

	public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "tarea-observaciones", "eliminar") ) {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "No está autorizado a eliminar Archivos.";

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

        $resguardoArchivo = New ResguardoArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $resguardoArchivo->id = $this->archivoId;
        $resguardoArchivo->resguardo = $this->resguardoId;
        
        if ( !$resguardoArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $resguardoArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);

    }

    /*=============================================
	TABLA PARTIDAS DE LOS RESGUARDOS
	=============================================*/

    public $resguardoId;

	public function mostrarTablaPartidaResguardos()
	{
		$resguardo = New Resguardo;
        $resguardo->id = $this->resguardoId;
        $resguardos = $resguardo->partidasResguardo();


		$columnas = array();
        array_push($columnas, [ "data" => "id" ]);
        array_push($columnas, [ "data" => "concepto" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "partida" ]);
        

        $registros = array();
        foreach ($resguardos as $key => $value) {

        	array_push( $registros, [ "id" =>  mb_strtoupper(fString($value["id"])),
        							  "concepto" => mb_strtoupper(fString($value["concepto"])),
        							  "cantidad" => mb_strtoupper(fString($value["cantidad"])),
        							  "unidad" => mb_strtoupper(fString($value["unidad"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "partida" => mb_strtoupper(fString($value["partidaId"]))
                                        ]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}
    
    /*=============================================
	OBTENER TRANSFERIR RESGUARDO
	=============================================*/
	public function obtenerTransferirResguardo(){

		$resguardo = New Resguardo;
		$resguardo->id = $this->resguardoId;

        $transferencias = $resguardo->consultarTransferenciasDeResguardo();

		$columnas = array();
        array_push($columnas, [ "data" => "usuarioRecibio" ]);
        array_push($columnas, [ "data" => "usuarioEntrego" ]);
        array_push($columnas, [ "data" => "fechaEntrego" ]);
        array_push($columnas, [ "data" => "resguardoOriginal" ]);
        array_push($columnas, [ "data" => "resguardoNuevo" ]);
        array_push($columnas, [ "data" => "concepto" ]);
        array_push($columnas, [ "data" => "cantidad" ]);

        $token = createToken();

        $registros = array();
        foreach ($transferencias as $key => $value) {
            $resguardoNuevoId = $value["resguardoNuevoId"];
        	$rutaResguardoNuevo = Route::names('resguardos.edit', $value['resguardoNuevoId']);

        	array_push( $registros, [ 
        							  "usuarioRecibio" => mb_strtoupper(fString($value["nombreUsuarioRecibio"])),
        							  "usuarioEntrego" => mb_strtoupper(fString($value["nombreUsuarioEntrego"])),
        							  "fechaEntrego" => fFechaLargaHora($value["fechaEntrego"]),
        							  "resguardoOriginal" => mb_strtoupper(fString($value["resguardoOriginalId"])),
        							  "resguardoNuevo" => 
                                      "<a href='{$rutaResguardoNuevo}'>{$resguardoNuevoId}</a>",
        							  "concepto" => mb_strtoupper(fString($value["concepto"])),
        							  "cantidad" => mb_strtoupper(fString($value["cantidad"])),
                                    ]);
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);

    }
    /*=============================================
	CREAR TRANSFERIR RESGUARDO
	=============================================*/
	public function crearTransferirResguardo()
	{
		try {
			$request = SaveResguardoRequest::validated();
			
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

			$resguardo = new Resguardo;

			$directorio ='../../vistas/img/almacenes/transferencia-resguardo/';
			$request["firma"]= guardarFirma($request["firma"],$directorio);

            //CREAR NUEVO RESGUARDO
			if (!$resguardo->crearResguardoPorTransferencia($request)) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
            
            $request["resguardoNuevoId"]  = $resguardo->resguardoNuevoId;
            // CREAR TRASNFERENCIA MANDANDO ID DE EL RESGUARDO Y DEL USUARIO
			if ( !$resguardo->crearTransferencia($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			foreach ($this->detalles as $detalle) {
				$detalle["salidaResguardoId"] = $resguardo->resguardoNuevoId;
				$detalle["partidaId"] = $detalle["partida"];

                // INSERTAR DETALLES DE RESGUARDO
				$resguardo->insertarDetalles($detalle);
                // INSERTAR DETALLES DE TRANSFERENCIA
				$resguardo->insertarDetallesTransferencia($detalle);
                
                // MODIFICAR PARTIDA POR ID
				$resguardo->modificarPartidaResguardo($detalle);

			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['respuestaMessage'] = 'Se creó con exito el resguardo';

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

/*=============================================
TABLA DE PERFILES
=============================================*/
$resguardo = new ResguardoAjax();

if (isset($_POST["accion"])){
	if ( $_POST["accion"] == "eliminarArchivo" && isset($_POST["archivoId"]) ) {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $resguardo->token = $_POST["_token"];
        $resguardo->archivoId = $_POST["archivoId"];
        $resguardo->resguardoId = $_POST["resguardoId"];
        $resguardo->eliminarArchivo();

    }else if ( $_POST["accion"] == "transferirResguardo" ){

        /*=============================================
        Transferir Resguardo
        =============================================*/
		$detalles = json_decode($_POST["detalles"], true);
		$resguardo->detalles = $detalles;

        $resguardo->crearTransferirResguardo();

    }
}else if (isset($_GET["accion"])){
	if ( $_GET["accion"] == "obtenerTransferenciaResguardo") {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $resguardo->resguardoId = $_GET["resguardoId"];
        $resguardo->obtenerTransferirResguardo();
    }
}
else if (isset($_GET["resguardoId"])){

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $resguardo->resguardoId = $_GET["resguardoId"];
        $resguardo->mostrarTablaPartidaResguardos();

}
else {
	$resguardo->mostrarTabla();
}