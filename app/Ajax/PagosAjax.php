<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/Pago.php";
require_once "../Models/Requisicion.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/Mensaje.php";
require_once "../Controllers/MailController.php";

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Route;
use App\Models\Usuario;
use App\Models\Pago;
use App\Models\Requisicion;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;
use App\Controllers\MailController;

class PagoAjax
{

	/*=============================================
	TABLA DE PAGOS
	=============================================*/
	public function mostrarTabla()
	{
		$pago = New Pago;
        $pagos = $pago->consultar();

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "nombre" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();

        $registros = array();
        foreach ($perfiles as $key => $value) {
        	$rutaEdit = Route::names('perfiles.edit', $value['id']);
        	$rutaDestroy = Route::names('perfiles.destroy', $value['id']);
        	$folio = mb_strtoupper(fString($value['nombre']));

			if ( mb_strtolower($value["nombre"]) != mb_strtolower(CONST_ADMIN) ) {

        	array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "nombre" => fString($value["nombre"]),
        							  "descripcion" => fString($value["descripcion"]),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>" ] );

        	} else {

        		array_push( $registros, [ "consecutivo" => ($key + 1),
        							  "nombre" => fString($value["nombre"]),
        							  "descripcion" => fString($value["descripcion"]),
        							  "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>" ] );

        	}
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

    public function subirPagosRequisiciones()
    {
        try {
            
            $pago = New Pago;
            $requisicion = New Requisicion;

            $requisiciones = array();
            foreach ($_POST["estatus"] as $key => $value) {
                $datos = array();
                $datos['comprobanteArchivos']['name'][] =  $_FILES["files"]["name"][$key];
                $datos['comprobanteArchivos']['type'][] =  $_FILES["files"]["type"][$key];
                $datos['comprobanteArchivos']['tmp_name'][] =  $_FILES["files"]["tmp_name"][$key];
                $datos['comprobanteArchivos']['error'][] =  $_FILES["files"]["error"][$key];
                $datos['comprobanteArchivos']['size'][] =  $_FILES["files"]["size"][$key];
                $datos['servicioEstatusId'] = $value;
                $datos['actualServicioEstatusId'] = $value;
                $requisicion->id = $_POST["requisicionId"][$key];
                $requisicion->consultar(null, $requisicion->id);
                $datos["tipoRequisicion"] = $requisicion->tipoRequisicion;
                $datos["observacion"] = "Pagado";

                chdir(__DIR__.'/../../');
                $requisicion->actualizar($datos);

                $uploadDocumentos = array();
                if ( isset($datos['comprobanteArchivos']) ) 
                    array_push($uploadDocumentos, [
                        'id' => 1,
                        'tipoDocumento' => 'Comprobante de Pago',
                        'documentos' => $datos['comprobanteArchivos']['name']
                    ]);

                if ( $uploadDocumentos ) $this->sendMailCambiarEstatus($requisicion, $uploadDocumentos);
                
                array_push($requisiciones, [$requisicion->id, $requisicion->folio, $requisicion->estatus["descripcion"]]);
            }
            $respuesta = [
                'codigo' => 200,
                'error' => false,
                'mensaje' => 'Los pagos se han subido correctamente',
                'requisiciones' => $requisiciones
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

    public function subirPagosOrdenesCompra()
    {
        try {
            
            $pago = New Pago;
            require_once "../Models/OrdenCompra.php";
            $ordenCompra = New \App\Models\OrdenCompra;

            $requisicion = New Requisicion;

            $ordenes = array();
            foreach ($_POST["estatus"] as $key => $value) {
                $datos = array();
                $datos['comprobanteArchivos']['name'][] =  $_FILES["files"]["name"][$key];
                $datos['comprobanteArchivos']['type'][] =  $_FILES["files"]["type"][$key];
                $datos['comprobanteArchivos']['tmp_name'][] =  $_FILES["files"]["tmp_name"][$key];
                $datos['comprobanteArchivos']['error'][] =  $_FILES["files"]["error"][$key];
                $datos['comprobanteArchivos']['size'][] =  $_FILES["files"]["size"][$key];
                $datos['actualEstatusActualizarId'] = $value;
                $ordenCompra->id = $_POST["requisicionId"][$key];
                $ordenCompra->consultar(null, $ordenCompra->id);
                $datos['ordenCompraId'] = $ordenCompra->id;
                $datos['condicionPagoId'] = $ordenCompra->condicionPagoId;
                $datos['monedaId'] = $ordenCompra->monedaId;
                $datos['requisicionId'] = $ordenCompra->requisicionId;
                $datos['proveedorId'] = $ordenCompra->proveedorId;
                $datos['estatusId'] = $value;
                $datos['fechaRequerida'] = fFechaLarga($ordenCompra->fechaRequerida);
                $datos['retencionIva'] = $ordenCompra->retencionIva;
                $datos['retencionIsr'] = $ordenCompra->retencionIsr;
                $datos['descuento'] = $ordenCompra->descuento;
                $datos['iva'] = $ordenCompra->iva;
                $datos['datoBancarioId'] = $ordenCompra->datoBancarioId;
                $datos['total'] = $ordenCompra->total;
                $datos['subtotal'] = $ordenCompra->subtotal;
                $datos['folio'] = $ordenCompra->folio;
                $datos['direccion'] = $ordenCompra->direccion;
                $datos['especificaciones'] = $ordenCompra->especificaciones;
                $datos['justificacion'] = $ordenCompra->justificacion;

                $datos["observacion"] = "Pagado";

                chdir(__DIR__.'/../../');
                $ordenCompra->actualizar($datos);

                $uploadDocumentos = array();
                if ( isset($datos['comprobanteArchivos']) ) 
                    array_push($uploadDocumentos, [
                        'id' => 1,
                        'tipoDocumento' => 'Comprobante de Pago',
                        'documentos' => $datos['comprobanteArchivos']['name']
                    ]);

                $requisicion->consultar(null, $ordenCompra->requisicionId);

                if ( $uploadDocumentos ) $this->sendMailCambiarEstatus($requisicion);

                array_push($ordenes, [$ordenCompra->id, $ordenCompra->folio, $ordenCompra->estatus["descripcion"]]);
            }
            $respuesta = [
                'codigo' => 200,
                'error' => false,
                'mensaje' => 'Los pagos se han subido correctamente',
                'requisiciones' => $ordenes // Se que deberia de decir 'ordenes' pero para no cambiar el front-end lo dejo asi
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

    public function sendMailCambiarEstatus(Requisicion $requisicion)
    {        
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $arrayDestinatarios = array();

            // Agregar al usuario que creó la requisición (si el estatus corresponde)
            $configuracionCorreoElectronico->consultarEstatusModificarUsuarioCreacion();
            if ( $configuracionCorreoElectronico->estatusModificarUsuarioCreacion ) {
                if ( in_array($requisicion->servicioEstatusId, $configuracionCorreoElectronico->estatusModificarUsuarioCreacion) ) {

                    $usuario = New Usuario;
                    $usuario->consultar(null, $requisicion->usuarioIdCreacion);

                    $destinatario = [
                        "usuarioId" => $usuario->id,
                        "correo" => $usuario->correo
                    ];

                    array_push($arrayDestinatarios, $destinatario);

                }
            }

            // Agregar a los usuarios de los perfiles seleccionados (si el estatus corresponde)
            $configuracionCorreoElectronico->consultarEstatusModificarPerfiles();
            if ( isset($configuracionCorreoElectronico->estatusModificarPerfiles[$requisicion->servicioEstatusId]) ) {

                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->estatusModificarPerfiles[$requisicion->servicioEstatusId]);

                foreach ($perfil->usuarios as $key => $value) {
                    if ( in_array($value["usuarioId"], array_column($arrayDestinatarios, "usuarioId")) ) continue;

                    $destinatario = [
                        "usuarioId" => $value["usuarioId"],
                        "correo" => $value["correo"]
                    ];

                    array_push($arrayDestinatarios, $destinatario);
                }
            }

            if ( count($arrayDestinatarios) > 0 ) {

                $mensaje = New Mensaje;

                $folio = mb_strtoupper($requisicion->folio);
                $estatusDescripcion = mb_strtoupper($requisicion->estatus["descripcion"]);
                $liga = Route::names('requisiciones.edit', $requisicion->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>REQUISICION ACTUALIZADA</h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>El estatus de la requisición {$folio} ha sido actualizado a '{$estatusDescripcion}'</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado deL cambio de estatus de la requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Estatus actualizado en requisición {$folio}",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "El estatus de la requisición {$folio} ha sido actualizado a '{$estatusDescripcion}', entre a la aplicación para ver el detalle de la misma.",
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

    public function sendMailUploadDocumento(Requisicion $requisicion, $uploadDocumentos)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $configuracionCorreoElectronico->consultarDocumentos();

            $arrayUploadDocumentos = array();

            foreach ($uploadDocumentos as $key => $value) {

                if ( in_array($value['id'], $configuracionCorreoElectronico->documentos->uploadDocumentos) )
                    array_push($arrayUploadDocumentos, $value);

            }

            if ( count($arrayUploadDocumentos) > 0 ) {
                // var_dump($arrayUploadDocumentos);
                // var_dump( array_column($arrayUploadDocumentos, "tipoDocumento") );
                // var_dump( implode(", ", array_column($arrayUploadDocumentos, "tipoDocumento")) );

                $arrayDestinatarios = array();

                // Agregar al usuario que subió el documento (si está seleccionado)
                if ( $configuracionCorreoElectronico->documentos->usuarioUploadDocumento ) {

                    $usuario = New Usuario;
                    $usuario->consultar(null, usuarioAutenticado()['id']);

                    $destinatario = [
                        "usuarioId" => $usuario->id,
                        "correo" => $usuario->correo
                    ];

                    array_push($arrayDestinatarios, $destinatario);

                }

                // Agregar a los usuarios de los perfiles seleccionados
                if ( count($configuracionCorreoElectronico->documentos->perfilesUploadDocumento) > 0 ) {

                    $perfil = New Perfil;
                    $perfil->consultarUsuarios($configuracionCorreoElectronico->documentos->perfilesUploadDocumento);

                    foreach ($perfil->usuarios as $key => $value) {
                        if ( in_array($value["usuarioId"], array_column($arrayDestinatarios, "usuarioId")) ) continue;

                        $destinatario = [
                            "usuarioId" => $value["usuarioId"],
                            "correo" => $value["correo"]
                        ];

                        array_push($arrayDestinatarios, $destinatario);
                    }
                }

                if ( count($arrayDestinatarios) > 0 ) {

                    $mensaje = New Mensaje;
                    $folio = mb_strtoupper($requisicion->folio);
                    $tipoDocumentos = implode(", ", array_column($arrayUploadDocumentos, "tipoDocumento"));
                    $liga = Route::names('requisiciones.edit', $requisicion->id);
                    $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                            <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                                <center>

                                    <h3 style='font-weight: 100; color: #999'>DOCUMENTO CARGADO EN REQUISICION</h3>

                                    <hr style='border: 1px solid #ccc; width: 80%'>
                                    
                                    <br>

                                    <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                        <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Se han cargado documentos en la requisición {$folio} : '{$tipoDocumentos}'</div>
                                    </a>

                                    <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                    <hr style='border: 1px solid #ccc; width: 80%'>

                                    <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado que se han cargado documentos en la requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                                </center>

                            </div>
                                
                        </div>";

                    $datos = [ "mensajeTipoId" => 3,
                               "mensajeEstatusId" => 1,
                               "asunto" => "Documento cargado en requisición {$folio}",
                               "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                               "mensaje" => "Se han cargado documentos en la requisición {$folio} : '{$tipoDocumentos}', entre a la aplicación para ver el detalle de la misma.",
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
    }
}

/*=============================================
TABLA DE PAGOS
=============================================*/
$pago = new PagoAjax();
if ( isset($_POST["accion"]) && $_POST["accion"] == "subirPagos" ) {

    if ( isset($_POST["categoria"]) && $_POST["categoria"] == 1 ) {
        $pago -> subirPagosRequisiciones();
    } else {
        $pago -> subirPagosOrdenesCompra();
    }


}else{
    $pago -> mostrarTabla();

}
