<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/RequisicionGasto.php";
require_once "../Models/RequisicionArchivoGasto.php";
require_once "../Models/Gastos.php";
require_once "../Models/GastoDetalles.php";
require_once "../Models/Empresa.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\RequisicionGasto;
use App\Models\RequisicionArchivoGasto;
use App\Models\Gastos;
use App\Models\GastoDetalles;
use App\Models\Empresa;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class RequisicionGastoAjax
{

	/*=============================================
	TABLA DE REQUISICIONES DE GASTO
	=============================================*/
	public function mostrarTabla()
	{
		$requisicionGasto = New RequisicionGasto;
        $requisicionGastos = $requisicionGasto->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fechaRequisicion" ]);
        array_push($columnas, [ "data" => "solicito" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($requisicionGastos as $key => $value) {
        	$rutaEdit = Route::names('requisicion-gastos.edit', $value['id']);
            $rutaPrint = Route::names('requisicion-gastos.print', $value['id']);

        	$folio = mb_strtoupper(fString($value['folio']));
            $solicito = mb_strtoupper(fString($value['nombreCompleto']));
        	array_push( $registros, [ "consecutivo" => ($key + 1),
                                    "empresa" => fString($value["empresas.nombreCorto"]),
                                    "folio" => fString($value["folio"]),
                                    "estatus" => fString($value["servicio_estatus.descripcion"]),
                                    "colorTexto" => mb_strtoupper(fString($value["servicio_estatus.colorTexto"])),
                                    "colorFondo" => mb_strtoupper(fString($value["servicio_estatus.colorFondo"])),
                                    "fechaRequisicion" => fFechaLarga($value["fechaCreacion"]),
                                    "solicito" => fString($solicito),
                                    "acciones" =>  "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                                    <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

	/*=============================================
	CREAR REQUISICION
	=============================================*/	
	public $token;
	public $descripcion;

	public function crear(){

		try {
			$respuesta["error"] = false;

			// Validar Autorizacion
			$usuario = New Usuario;
			if ( usuarioAutenticado() ) {

				$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
				
				if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisicion-gastos", "crear") ) {

					$respuesta["error"] = true;
					$respuesta["errorMessage"] = "No está autorizado a crear Requisiciones.";

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

			$gastos = New Gastos;
            $gastos->consultar(null,$this->gastoId);

            // SE OBTIENEN LOS DATOS DE EMPRESSA
            require_once "../../app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null,$gastos->empresa);

            // SE OBTIENEN LOS DATOS DE LOS DETALLES DEL GASTO
            $gastoDetalle = New GastoDetalles;
            $gastoDetalles = $gastoDetalle->consultarPorGasto($this->gastoId);
            
            //Se crean los datos para ingreas a las partidas
            $partidas = [];
            foreach ($gastoDetalles as $key => $value) {
                $partidas["costo"][]=$value["costo"];
                $partidas["cantidad"][]=$value["cantidad"];
                $partidas["unidad"][]=$value["unidad"];
                $partidas["numeroParte"][]=$value["numeroParte"];
                $partidas["concepto"][]=$value["observaciones"];

            }

            $requisicionGasto = New RequisicionGasto;

            // Generacion de folio
            $folio = "GACC-".strtoupper($empresa->nomenclaturaOT);

            $datosReq = [
                "empresa" => $empresa->id,
                "gasto" => $this->gastoId,
                "folio" => $folio,
                "estatus" => 18,
                "partidas" => $partidas
            ];

            if ( !$requisicionGasto->crear($datosReq) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
            
            $gasto = new Gastos;
            $gasto->id = intval($this->gastoId);
            $datosGasto["requisicionId"] = $requisicionGasto->id;

            $gasto->actualizarRequisicionId($datosGasto);

            $respuesta = [
                'error' => false,
                'respuesta' => $requisicionGasto,
                'respuestaMessage' => "La requisicion fue creada correctamente."
            ];

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);

	}

    public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;
        if ( usuarioAutenticado() ) {

            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisiciones-subir", "eliminar") ) {

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

        // Validar existencia del campo requisicionId
        if ( !Validacion::validar("requisicionId", $this->requisicionId, ['exists', CONST_BD_APP.'.requisicion_gastos', 'id']) ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "La requisición no existe.";

        }

        if ( $respuesta["error"] ) {

            echo json_encode($respuesta);
            return;

        }

        $requisicionArchivo = New RequisicionArchivoGasto;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $requisicionArchivo->id = $this->archivoId;
        $requisicionArchivo->requisicionId = $this->requisicionId;
        if ( !$requisicionArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $requisicionArchivo->eliminar() ) {

                $respuesta["respuestaMessage"] = "El archivo fue eliminado correctamente.";
                $respuesta["respuesta"] = true;
                
            } else {

                $respuesta["error"] = true;
                $respuesta["errorMessage"] = "Hubo un error al intentar eliminar el archivo, intente de nuevo.";

            }

        }

        echo json_encode($respuesta);

    }

}

$requisicionGastoAjax = new RequisicionGastoAjax();

if ( isset($_POST["accion"]) ) {

    if ( $_POST["accion"] == "crearRequisicion" ) {

        /*=============================================
        CREAR REQUISICION
        =============================================*/
        $requisicionGastoAjax->token = $_POST["_token"];
        $requisicionGastoAjax->gastoId = $_POST["gastoId"];
        $requisicionGastoAjax->crear();

    } elseif ( $_POST["accion"] == "eliminarArchivo" && isset($_POST["archivoId"]) ) {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $requisicionGastoAjax->token = $_POST["_token"];
        $requisicionGastoAjax->archivoId = $_POST["archivoId"];
        $requisicionGastoAjax->requisicionId = $_POST["requisicionId"];
        $requisicionGastoAjax->eliminarArchivo();

    } else {

        $respuesta["error"] = true;
        $respuesta["errorMessage"] = "Realizó una petición desconocida.";

        echo json_encode($respuesta);

    }

} else {
    /*=============================================
    TABLA DE REQUISICIONES
    =============================================*/
    $requisicionGastoAjax->mostrarTabla();

}