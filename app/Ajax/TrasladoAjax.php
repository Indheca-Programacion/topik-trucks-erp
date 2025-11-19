<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Traslado.php";
require_once "../Models/TrasladoDetalle.php";
require_once "../Requests/SaveTrasladoDetallesRequest.php";
require_once "../Controllers/Autorizacion.php";

use ZipArchive;
use App\Route;
use App\Models\Usuario;
use App\Models\Traslado;
use App\Models\TrasladoDetalle;
use App\Requests\SaveTrasladoDetallesRequest;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class TrasladoAjax
{

	/*=============================================
	TABLA DE TRASLADOS
	=============================================*/
	public function mostrarTabla()
	{
		$traslado = New Traslado;
        $traslados = $traslado->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "operador" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "ruta" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($traslados as $key => $value) {
        	$rutaEdit = Route::names('traslados.edit', $value['id']);
        	$rutaDestroy = Route::names('traslados.destroy', $value['id']);
			$rutaPrint = Route::names('traslados.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));
			if ($value['estatus'] == 0) {
				$estatus = 'Por Atender';
			} elseif ($value['estatus'] == 1) {
				$estatus = 'Revisado';
			} elseif ($value['estatus'] == 2) {
				$estatus = 'Completado';
			} else {
				$estatus = 'Desconocido';
			}

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "folio" => mb_strtoupper(fString($value["servicio.folio"])),
        							  "operador" => mb_strtoupper(fString($value["operador"])),
        							  "estatus" => $estatus,
                                      "ruta" => mb_strtoupper(fString($value["ruta"])),
                                      "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                                      "creo" => mb_strtoupper(fString($value["creo"])),
        							  "acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
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
	/*=============================================
	AGREGAR FACTURA
	=============================================*/
	public function agregar()
	{
		try {
			$trasladoDetalle = new TrasladoDetalle;
			$request = SaveTrasladoDetallesRequest::validated();
			
			$trasladoDetalle->crear($request);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta["respuestaMessage"] = "El gasto fue creado correctamente";

		} catch (\Exception $th) {
			$respuesta = array();
			$respuesta['codigo'] = 500;
			$respuesta['error'] = true;
			$respuesta['mensaje'] = $th->getMessage();

		}
		echo json_encode($respuesta);
	}
	/*=============================================
	OBTENER DETALLES DE TRASLADO
	=============================================*/

	public function getDetalles()
	{
		$trasladoDetalle = new TrasladoDetalle;
		$trasladoDetalle->traslado = $_POST['traslado'];
		$detalles = $trasladoDetalle->consultarPorTraslado();

		$columnas = array();
		array_push($columnas, [ "data" => "gasto" ]);
		array_push($columnas, [ "data" => "proveedor" ]);
		array_push($columnas, [ "data" => "folio" ]);
		array_push($columnas, [ "data" => "total" ]);
		array_push($columnas, [ "data" => "descripcion" ]);
		array_push($columnas, [ "data" => "acciones" ]);
		
		$token = createToken();
		
		$registros = array();
		foreach ($detalles as $key => $value) {
			$rutaDestroy = Route::names('traslados.destroy', $value['id']);
			$folio = mb_strtoupper(fString($value['descripcion']));
			$id = $value['id'];
			if ( $value["gasto"] == 1 ) {
				$gasto = "DEDUCIBLE";
			} else {
				$gasto = "NO DEDUCIBLE";
			}
			array_push( $registros, [ "consecutivo" => ($key + 1),
									  "gasto" => mb_strtoupper(fString($gasto)),
									  "proveedor" => mb_strtoupper(fString($value["proveedor"])),
									  "folio" => mb_strtoupper(fString($value["folio"])),
									  "total" => number_format($value["total"],2),
									  "descripcion" => mb_strtoupper(fString($value["descripcion"])),
									  "acciones" => "<button type='button' folio='{$id}' data-toggle='modal' data-target='#modalVerArchivos' class='btn btn-info btn-xs btn-mostrar-modal'><i class='fas fa-file'></i></button>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' id='{$id}' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button> "] );
		}

		$respuesta = array();
		$respuesta['codigo'] = 200;
		$respuesta['error'] = false;
		$respuesta['datos']['columnas'] = $columnas;
		$respuesta['datos']['registros'] = $registros;

		echo json_encode($respuesta);
	}

	/*=============================================
	ELIMINAR GASTO
	=============================================*/
	public function eliminar()
	{
		$trasladoDetalle = new TrasladoDetalle;
		$trasladoDetalle->id = $_POST['id'];
		$trasladoDetalle->eliminar();

		$respuesta = array();
		$respuesta['codigo'] = 200;
		$respuesta['error'] = false;
		$respuesta["respuestaMessage"] = "El gasto fue eliminado correctamente";

		echo json_encode($respuesta);
	}

	/*=============================================
	OBTENER ARCHIVOS
	=============================================*/
	public function getArchivos()
	{
		$trasladoDetalle = new TrasladoDetalle;
		$trasladoDetalle->id = $_POST['id'];
		$archivos = $trasladoDetalle->consultarArchivos();

		$respuesta = array();
		$respuesta['codigo'] = 200;
		$respuesta['error'] = false;
		$respuesta['datos'] = $archivos;

		echo json_encode($respuesta);
	}

	/*=============================================
	DESCARGAR FACTURA
	=============================================*/
	public function descargarFactura()
	{
		$trasladoDetalle = new TrasladoDetalle;
		$trasladoDetalle->traslado = $_POST['traslado'];
		$archivo = $trasladoDetalle->consultarArchivoPorTraslado();

		$traslado = new Traslado;
		$traslado->consultar(null, $trasladoDetalle->traslado);

		$zip = new ZipArchive();
		$zipFileName = tempnam(sys_get_temp_dir(), 'factura_') . '.zip';
		$zip->open($zipFileName, ZipArchive::CREATE);
	
		$directory = 'No deducible';
		$zip->addEmptyDir($directory);
		foreach ($archivo as $file) {
			$filePath = $file['ruta']; // Assuming 'ruta' contains the file path
			$fileName = basename($filePath);
			if ($file['gasto'] == 1) {
				$zip->addFile('../../'.$file["ruta"],$file["titulo"]);
			}else {
				$zip->addFile('../../'.$file["ruta"], $directory.'/'. $file["titulo"]);
			}
		}
		$zip->close();

		// Establecer encabezados para la descarga
		header('Content-Type: application/zip');
		header('Content-Disposition: attachment; filename="' . $zipFileName . '"');
		header('Content-Length: ' . filesize($zipFileName));

		// Enviar el archivo ZIP al navegador
		readfile($zipFileName);

		// Eliminar el archivo ZIP temporal (opcional)
		// unlink($zipFileName);
		exit();
		
	}

}

$TrasladoAjax = new TrasladoAjax();

if ( isset($_POST["accion"]) ) {

	if ( $_POST["accion"] == "addGasto" ) {

		/*=============================================
		AÑADIR 
		=============================================*/
		$TrasladoAjax->agregar();

	} elseif ( $_POST["accion"] == "getDetalles" ) {
		
		/*=============================================
		OBTENER DETALLES DE TRASLADO
		=============================================*/
		$TrasladoAjax->getDetalles();

	} else if ( $_POST["accion"] == "deleteGasto" ) {
		
		/*=============================================
		ELIMINAR GASTO
		=============================================*/
		$TrasladoAjax->eliminar();

	} else if ( $_POST["accion"] == "getArchivos") {
		
		/*=============================================
		OBTENER ARCHIVOS
		=============================================*/
		$TrasladoAjax->getArchivos();
	} else if ( $_POST["accion"] == "descargarTodo") {
		
		/*=============================================
		DESCARGAR FACTURA
		=============================================*/
		$TrasladoAjax->descargarFactura();
	} else {

		echo 'peticion erronea';

	}

} else {

	/*=============================================
	TABLA DE TRASLADOS
	=============================================*/
	$TrasladoAjax->mostrarTabla();

}
