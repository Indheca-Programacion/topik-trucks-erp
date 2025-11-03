<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Gastos.php";
require_once "../Models/GastoDetalles.php";
require_once "../Models/GastoArchivo.php";
require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/Mensaje.php";
require_once "../Models/Perfil.php";
require_once "../Controllers/MailController.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveGastoDetallesRequest.php";
require_once "../../vendor/autoload.php";

use ZipArchive;
use App\Route;
use App\Models\Usuario;
use App\Models\Gastos;
use App\Models\GastoDetalles;
use App\Models\GastoArchivo;
use App\Models\Mensaje;
use App\Models\Perfil;
use App\Models\ConfiguracionCorreoElectronico;
use App\Controllers\Autorizacion;
use App\Controllers\MailController;
use App\Controllers\Validacion;
use App\Requests\SaveGastoDetallesRequest;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
use iio\libmergepdf\Driver\TcpdiDriver;

ini_set('display_errors', 1);

class GastosAjax
{

	/*=============================================
	TABLA DE GASTOS
	=============================================*/
	public function mostrarTabla()
	{
		$gasto = New Gastos;

        require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null,usuarioAutenticado()["id"]);
		$usuario->consultarPerfiles();

		if (Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::perfil($usuario, 'pagos') ) {
            $gastos = $gasto->consultar();
		}else{
            $gastos = $gasto->consultarPorUsuario($usuario->id);
        }

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio", "title" => "Folio" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fecha_envio" ]);
        array_push($columnas, [ "data" => "encargado" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "tipoGasto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($gastos as $key => $value) {
        	$rutaEdit = Route::names('gastos.edit', $value['id']);
        	$rutaDestroy = Route::names('gastos.destroy', $value['id']);
            $rutaPrint = Route::names('gastos.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
                                      "folio" => "GCC-".$value["id"],
                                      "obra" => mb_strtoupper(fString($value["obra.nombreCorto"])),
                                      "estatus" => mb_strtoupper(fString($value["estatus"])),
        							  "fecha_envio" => fFechaLarga($value["fecha_envio"]).' '.fHora($value["fecha_envio"]),
        							  "encargado" => mb_strtoupper(fString($value["nombreCompleto"])),
        							  "empresa" => mb_strtoupper(fString($value["empresa.nombreCorto"])),
        							  "tipoGasto" => mb_strtoupper(fString($value["tipoGasto"])),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>
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
	AGREGA PARTIDAS
	=============================================*/
	public function agregarPartidas()
	{
		try {
			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "crear") ) throw new \Exception("No está autorizado a crear nuevos Indirectos.");

			$request = SaveGastoDetallesRequest::validated();

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

            $GastoDetalles = New GastoDetalles;

            // Crear el nuevo registro
            if ( !$GastoDetalles->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $GastoDetalles,
                'respuestaMessage' => "La partida fue agregada correctamente."
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
	/*=============================================
	OBTIENE PARTIDAS
	=============================================*/
    public function obtenerPartidas()
    {
        $gastoDetalle = New GastoDetalles;
        $gastoDetalles = $gastoDetalle->consultarPorGasto($this->gastoId);
        $gasto = New Gastos;
        $gasto->consultar(null,$this->gastoId);

        $usuario = new Usuario;
        $usuario->consultar(null, usuarioAutenticado()["id"]);
        $usuario->consultarPerfiles();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "fecha" ]);
        array_push($columnas, [ "data" => "tipoGasto" ]);
        array_push($columnas, [ "data" => "costo" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "factura" ]);
        array_push($columnas, [ "data" => "observaciones" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "acciones" ]);

        $registros = array();
        foreach ($gastoDetalles as $key => $value) {
            $id = $value["id"];
            $token = token();
            $rutaDestroy = Route::names('gasto-detalles.destroy', $value['id']);
            $folio = mb_strtoupper(fString($value["observaciones"]));
            $observaciones = mb_strtoupper(fString($value["observaciones_detalles"]));
            $cancelar = ( $gasto->cerrada == 1 &&  $usuario->checkPerfil("pagos") && $value["cancelada"] == 0 ) ? "<button type='button' id='cancelarPartida' data-swal-template='#cancelarPartida' detalle='{$value['id']}' folio='{$folio}' class='btn btn-danger btn-xs cancelarPartida'><i class='fas fa-thumbs-down fa-fw'></i></button>" : "" ;
            $eliminar = ($gasto->cerrada == 0 && $gasto->encargado == usuarioAutenticado()["id"]  ) ? "<form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <input type='hidden' name='gastoId' value='{$this->gastoId}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>" : ''  ; 
            $observacion = ( $gasto->encargado == usuarioAutenticado()["id"] && $value["observaciones_detalles"] ) ? "<button type='button' class='btn btn-xs btn-warning observacion' folio='{$observaciones}'>
									                         <i class='fas fa-info'></i>
									                      </button>" : ""; 
            array_push($registros,[ "consecutivo" => ($key + 1),
                                   "fecha" => fFechaLarga($value["fecha"]),
                                   "tipoGasto" => mb_strtoupper(fString($value["tipoGasto"])),
                                   "costo" => '$ '.$value["costo"],
                                   "cantidad" => $value["cantidad"],
                                   "cancelada" => $value["cancelada"],
                                   "unidad" => mb_strtoupper(fString($value["unidad"])),
                                   "factura" => mb_strtoupper(fString($value["factura"])),
                                   "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                                   "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                                   "acciones" => "<button type='button' folio='{$id}' data-toggle='modal' data-target='#modalVerArchivos' class='btn btn-info btn-xs btn-mostrar-modal'><i class='fas fa-file'></i></button>
                                   <button type='button' folio='{$id}' id='btn-subirArchivo' class='btn btn-success btn-xs btn-subirArchivo'><i class='fas fa-file-upload'></i></button>
                                   ".$eliminar.$cancelar.$observacion
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
	OBTIENE ARCHIVOS DE  PARTIDAS
	=============================================*/
    public function obtenerArchivos(){

        $gasto = New GastoDetalles;

        $respuesta["archivos"] = array();

        // Consultar los archivos
        $respuesta["archivos"] = $gasto->consultarArchivos($this->gastoDetalleId);

        echo json_encode($respuesta);

    }
    /*=============================================
	CERRAR GASTOS DE CAJA CHICA
	=============================================*/
    public function cerrarGasto(){
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "actualizar") ) throw new \Exception("No está autorizado a cerrar gasto.");

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->cerrarGasto() ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $gastoDetalle = New GastoDetalles;
            $gastoDetalle->gastoId = $this->gastoId;
            $gastoDetalle->habilitarPartidas();
            
            $this->sendMailCerrarGasto($gasto);

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "La partida fue agregada correctamente."
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
    /*=============================================
	CREA REQUISICION
	=============================================*/
    public function crearRequisicion(){
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "requisicion-gastos", "crear") ) throw new \Exception("No está autorizado a crear reqquisiciones.");
            
            $respuesta["error"] = false;
            
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
                $partidas["partida"][] =$key+1;
                $partidas["cantidad"][]=$value["cantidad"];
                $partidas["unidad"][]=$value["unidad"];
                $partidas["numeroParte"][]=$value["numeroParte"];
                $partidas["concepto"][]=$value["observaciones"];

            }

            require_once "../../app/Models/RequisicionGasto.php";
            $requisicion = New \App\Models\RequisicionGasto;

            // Generacion de folio
            $folio = "GACC-".strtoupper($empresa->nomenclaturaOT);

            $datosReq = [
                "numero" => 1,
                "folio" => $folio,
                "servicioEstatusId" => 18,
                "partidas" => $partidas
            ];

            if ( !$requisicion->crear($datosReq) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
            
            $gasto = new Gastos;
            $gasto->id = intval($this->gastoId);
            $datosGasto = [
                "requisicionId" => $requisicion->id
            ];

            $gasto->actualizarRequisicionId($datosGasto);

            $respuesta = [
                'error' => false,
                'respuesta' => $requisicion,
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
    /*=============================================
    ELIMINAR ARCHIVO
    =============================================*/
    public $archivoId;

    public function eliminarArchivo()
    {
        $respuesta["error"] = false;

        // Validar Autorizacion
        $usuario = New Usuario;

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

        $gastoArchivo = New GastoArchivo;

        $respuesta["respuesta"] = false;

        // Validar campo (que exista en la BD)
        $gastoArchivo->id = $this->archivoId;
        $gastoArchivo->gastoDetalleId = $this->gastoDetalleId;
        if ( !$gastoArchivo->consultar() ) {

            $respuesta["error"] = true;
            $respuesta["errorMessage"] = "El archivo no existe.";

        } else {

            // Eliminar el archivo
            if ( $gastoArchivo->eliminar() ) {

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
    AÑADIR ARCHIVO
    =============================================*/
    public function addArchivo()
    {
        try {
            $gastoDetalles = New GastoDetalles;
            $gastoDetalles->id = $this->gastoDetalleId;

            $response = $gastoDetalles->insertarArchivos($_FILES["archivos"]);

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Se añadio correctamente el documento."
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

    public function descargarTodo()
    {
        $gastos = new Gastos();
        $archivos = $gastos->consultarArchivos($this->gastoId);

        // $merger = new Merger(new TcpdiDriver);
        $zip = new ZipArchive();
        $zip->open('archivos.zip', ZipArchive::CREATE);
        $zipFilename = 'archivos.zip';

        // Verificar si existe un archivo ZIP existente
        if (file_exists($zipFilename)) {
            unlink($zipFilename); // Eliminar el archivo ZIP existente
        }
        
        foreach ($archivos as $key => $file) {
            $zip->addFile('../../'.$file["ruta"],$file["titulo"]);
        }

        $zip->close();
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . $zipFilename . '"');
        header('Content-Length: ' . filesize($zipFilename));
        // Enviar el archivo
        readfile($zipFilename);
        unlink($zipFilename);
        exit();
        
    }
    
    public function sendMailCerrarGasto(Gastos $gastos)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $configuracionCorreoElectronico->consultarPerfilesCerrarGasto();  

            if ( $configuracionCorreoElectronico->perfilesCerrarGasto ) {

                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->perfilesCerrarGasto);

                $arrayDestinatarios = array();
                foreach ($perfil->usuarios as $key => $value) {
                    
                    if ( in_array($value["usuarioId"], array_column($arrayDestinatarios, "usuarioId")) ) continue;

                    $destinatario = [
                        "usuarioId" => $value["usuarioId"],
                        "correo" => $value["correo"]
                    ];

                    array_push($arrayDestinatarios, $destinatario);
                }

                $mensaje = New Mensaje;

                $liga = Route::names('gastos.edit', $gastos->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>GASTO </h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creada el gasto</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de un nuevo gasto, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Nuevo gasto ",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "Se ha cerrado el gasto , entre a la aplicación para ver el detalle de la misma.",
                           "liga" => $liga,
                           "destinatarios" => $arrayDestinatarios
                ];

                if ( $mensaje->crear($datos) ) {
                    $mensaje->consultar(null , $mensaje->id);
                    $mensaje->mensajeHTML = $mensajeHTML;

                    $enviar = MailController::send($mensaje);
                    if ( $enviar["error"] ) $mensaje->noEnviado([ "error" => $enviar["errorMessage"] ]);
                    else $mensaje->enviado();
                }

            }

        }        
    }

    public function cancelarPartida(){
        try {
            $gastoDetalles = New GastoDetalles;
            $gastoDetalles->id = $this->gastoDetalleId;
            $gastoDetalles->observaciones_detalles = $this->observacion;
            
            $response = $gastoDetalles->agregarObservacion();

            $gasto = new Gastos();
            $gasto->id = $this->gastoId;
            $gasto->abrirGasto();

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Se ha cancelado con excito."
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

    public function consultarFiltros(){
        $arrayFiltros = array();

        if ( $this->empresaId > 0 ) array_push($arrayFiltros, [ "campo" => "EM.id", "operador" => "=", "valor" => $this->empresaId ]);
        if ( $this->obraId > 0 ) array_push($arrayFiltros, [ "campo" => "O.obraId", "operador" => "=", "valor" => $this->obraId ]);
        if ( $this->usuarioId > 0 ) array_push($arrayFiltros, [ "campo" => "E.id", "operador" => "=", "valor" => $this->usuarioId ]);
        if ( $this->tipogastoId > 0 ) array_push($arrayFiltros, [ "campo" => "G.tipoGasto", "operador" => "=", "valor" => $this->tipogastoId ]);

        $gasto = New Gastos;
        $gastos = $gasto->consultarFiltros($arrayFiltros);
        // echo $requisiciones;
        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "estatus" ]);
        array_push($columnas, [ "data" => "fecha_envio" ]);
        array_push($columnas, [ "data" => "encargado" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "tipoGasto" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($gastos as $key => $value) {
        	$rutaEdit = Route::names('gastos.edit', $value['id']);
        	$rutaDestroy = Route::names('gastos.destroy', $value['id']);
            $rutaPrint = Route::names('gastos.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['id']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
                                      "obra" => mb_strtoupper(fString($value["obra.nombreCorto"])),
                                      "estatus" => mb_strtoupper(fString($value["estatus"])),
        							  "fecha_envio" => fFechaLarga($value["fecha_envio"]).' '.fHora($value["fecha_envio"]),
        							  "encargado" => mb_strtoupper(fString($value["nombreCompleto"])),
        							  "empresa" => mb_strtoupper(fString($value["empresa.nombreCorto"])),
        							  "tipoGasto" => mb_strtoupper(fString($value["tipoGasto"])),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>
                                                     <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

	public $token;
    public $gastoDetalleId;

    public function descargarFacturas()
    {
        $gastos = new Gastos;
        $archivos = $gastos->consultarArchivos($this->gastoId);

        if (empty($archivos)) {
            $respuesta = [
                'codigo' => 404,
                'error' => true,
                'errorMessage' => 'No hay archivos disponibles para descargar.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        $ruta = $this->crearFormatoGCC($this->gastoId);

        $pdfFiles = [];

        if (file_exists($ruta)) {
            $pdfFiles[] = escapeshellarg($ruta);
        } else {
            $respuesta = [
                'codigo' => 404,
                'error' => true,
                'errorMessage' => 'No se pudo generar el formato de gastos.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        // Unir los archivos PDF usando pdfunite
        foreach ($archivos as $file) {
            if ($file["formato"] == "application/pdf") { // Solo facturas
            $filePath = '../../' . $file["ruta"];
            if (file_exists($filePath)) {
                $pdfFiles[] = escapeshellarg($filePath);
            }
            }
        }

        if (empty($pdfFiles)) {
            $respuesta = [
            'codigo' => 404,
            'error' => true,
            'errorMessage' => 'No hay facturas PDF disponibles para unir.'
            ];
            echo json_encode($respuesta);
            exit;
        }

        $outputPdf = '/tmp/facturas_unidas.pdf';
        if (file_exists($outputPdf)) {
            unlink($outputPdf);
        }

        $cmd = 'qpdf --empty --pages ' . implode(' ', $pdfFiles) .  " -- " . escapeshellarg($outputPdf);
        $salida = shell_exec($cmd);

        if (file_exists($outputPdf)) {
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $outputPdf . '"');
            header('Content-Length: ' . filesize($outputPdf));
            readfile($outputPdf);
            unlink($outputPdf);
            exit;
        } else {
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => 'No se pudo crear el archivo ZIP.'
            ];
            echo json_encode($respuesta);
            exit;
        }
    }

    function crearFormatoGCC($id){
        $gastos = New Gastos;

        if ( $gastos->consultar(null , $id) ) {
            
            require_once "../../app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuarioAutorizo = "";
            $firmaAutorizo = null;
            if(!is_null($gastos->usuarioIdAutorizacion)){
                $usuario->consultar(null, $gastos->usuarioIdAutorizacion);
                $usuarioAutorizo = $usuario->nombreCompleto;
                $firmaAutorizo = $usuario->firma;
            }
            $usuario->consultar(null, $gastos->encargado);
            
            require_once "../../app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null, $gastos->obra);

            require_once "../../app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $gastos->empresa);

            require_once "../../app/Models/GastoDetalles.php";
            $gastosDetalles = New \App\Models\GastoDetalles;

            $detallesGastos = $gastosDetalles->consultarPorGasto($gastos->id);

            if($gastos->tipoGasto == 1){
                include "../../reportes/gastos-deducibles-conjunto.php";
                return $_SERVER['DOCUMENT_ROOT'] ."reportes/tmp/GastosDeducibles.pdf";
            }else{
                include "../../reportes/gastos-no-deducibles-conjunto.php";
                return $_SERVER['DOCUMENT_ROOT'] ."reportes/tmp/GastosNoDeducibles.pdf";
            }

        }

    }

    public function cambiarEstatus()
    {
        try {
            $gasto = new Gastos();
            $gasto->id = $this->gastoId;
            $gasto->cerrada = $this->nuevoEstatus;

            if ( !$gasto->cambiarEstatus() ) throw new \Exception("Hubo un error al intentar cambiar el estatus del gasto, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto,
                'respuestaMessage' => "El estatus del gasto fue cambiado correctamente."
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

    public function revisarGasto()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "actualizar") ) throw new \Exception("No está autorizado a revisar gastos.");

            $respuesta["error"] = false;
            
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

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->revisarGasto() ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "El gasto fue revisado correctamente."
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

    public function autorizarGasto()
    {
        try {
            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "gastos", "actualizar") ) throw new \Exception("No está autorizado a autorizar gastos.");

            $respuesta["error"] = false;
            
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

            $gasto = New Gastos;
            $gasto->id = $this->gastoId;

            if ( !$gasto->autorizarGasto() ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $gasto->id,
                'respuestaMessage' => "El gasto fue autorizado correctamente."
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
}

$gastos = new GastosAjax();

if ( isset($_POST["accion"]) ) {

    if ( $_POST["accion"] == "verArchivos" ){
        /*=============================================
        OBTIENE ARCHIVOS
        =============================================*/
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->obtenerArchivos();
    
    } elseif ( $_POST["accion"] == "crearRequisicion") {
        /*=============================================
        OBTIENE ARCHIVOS
        =============================================*/
        $gastos->gastoId = $_POST["gastoId"];
        $gastos->token = $_POST["_token"];
        $gastos->crearRequisicion();
    } elseif ($_POST["accion"] == "agregarPartida" ){
        /*=============================================
        AGREGAR DETALLES DE GASTOS
        =============================================*/	
        $gastos->agregarPartidas();
    } elseif ($_POST["accion"] == "cerrarGasto"){
        /*=============================================
        CERRAR GASTO 
        =============================================*/	
        $gastos->gastoId = $_POST['gastoId'];
        $gastos->cerrarGasto();
    } elseif ( $_POST["accion"] == 'eliminarArchivo' && isset($_POST["archivoId"]) ) {

        /*=============================================
        ELIMINAR ARCHIVO
        =============================================*/
        $gastos->token = $_POST["_token"];
        $gastos->archivoId = $_POST["archivoId"];
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->eliminarArchivo();
    
    } elseif ( $_POST["accion"] == 'subir-archivo'){
        /*=============================================
        SUBIR ARCHIVO
        =============================================*/
        $gastos->token = $_POST["_token"];
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->addArchivo();
    } elseif ( $_POST["accion"] == 'cancelarPartida'){
        /*=============================================
        CANCELAR PARTIDA
        =============================================*/
        $gastos->token = $_POST["_token"];
        $gastos->gastoDetalleId = $_POST["gastoDetalleId"];
        $gastos->gastoId = $_POST["gastoId"];
        $gastos->observacion = $_POST["observacion"];
        $gastos->cancelarPartida();
    } elseif ( $_POST["accion"] == 'cambiarEstatus') {
        $gastos->gastoId = $_POST["gastoId"];
        $gastos->nuevoEstatus = $_POST["nuevoEstatus"];
        $gastos->cambiarEstatus();
    } elseif ( $_POST["accion"] == "revisarGasto" ) {
        /*=============================================
        REVISAR GASTO
        =============================================*/
        $gastos->gastoId = $_POST["gastoId"];
        $gastos->token = $_POST["_token"];
        $gastos->revisarGasto();
    } elseif ( $_POST["accion"] == "autorizarGasto" ) {
        /*=============================================
        AUTORIZAR GASTO
        =============================================*/
        $gastos->gastoId = $_POST["gastoId"];
        $gastos->token = $_POST["_token"];
        $gastos->autorizarGasto();

    } else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => "Realizó una petición desconocida."
        ];

        echo json_encode($respuesta);

    }
    
} else if ( isset($_GET["empresaId"]) ) {
    /*=============================================
    DESCARGA ARCHIVO
    =============================================*/
    $gastos->empresaId = $_GET["empresaId"];
    $gastos->obraId = $_GET["obraId"];
    $gastos->usuarioId = $_GET["usuarioId"];
    $gastos->tipogastoId = $_GET["tipogastoId"];
    $gastos->consultarFiltros();
} elseif ( isset($_GET["accion"]) && $_GET["accion"] == "descargarFacturas" ) {

    /*=============================================
    DESCARGA LAS FACTURAS DE LOS GASTOS
    =============================================*/
    $gastos->gastoId = $_GET["gastoId"];
    $gastos -> descargarFacturas();
}  else if ( isset($_GET["gastoId"]) ){
    /*=============================================
    DESCARGA TODOS LOS ARCHIVOS EN CONJUNTO
    =============================================*/
    $gastos->gastoId = $_GET["gastoId"];
    $gastos->descargarTodo();
} elseif ( isset($_GET["gasto"]) ) {
    /*=============================================
    OBTIENE LOS DETALLES DE GASTOS
    =============================================*/
    $gastos->gastoId = $_GET["gasto"];
    $gastos->obtenerPartidas();

} else {

	/*=============================================
    OBTIENE LA TABLA DE GASTOS
	=============================================*/
	$gastos -> mostrarTabla();

}
