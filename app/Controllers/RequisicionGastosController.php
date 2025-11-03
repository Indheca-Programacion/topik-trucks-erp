<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionCorreoElectronico.php";
require_once "app/Models/Mensaje.php";
require_once "app/Models/Perfil.php";
require_once "app/Models/RequisicionGasto.php";
require_once "app/Models/Servicio.php";
require_once "app/Models/Usuario.php";
require_once "app/Policies/RequisicionPolicy.php";
require_once "app/Requests/SaveRequisicionesRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Controllers/MailController.php";

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Models\Perfil;
use App\Models\RequisicionGasto;
use App\Models\Servicio;
use App\Models\Usuario;
use App\Policies\RequisicionPolicy;
use App\Requests\SaveRequisicionesRequest;
use App\Requests\Request;
use App\Route;

class RequisicionGastosController
{
    public function index()
    {
        Autorizacion::authorize('view', New RequisicionGasto);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/ServicioEstatus.php";
        $servicioEstatus = New \App\Models\ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar();

        $contenido = array('modulo' => 'vistas/modulos/requisicion-gastos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create($id)
    {
    }

    public function store()
    {    
        Autorizacion::authorize('create', New Requisicion);

        $request = SaveRequisicionesRequest::validated();
        // var_dump($request);
        // die();

        if ( !isset($request['comprobanteArchivos']) && !isset($request['ordenesArchivos']) && !isset($request['detalles']) && !isset($request['facturaArchivos']) && !isset($request['cotizacionArchivos']) && !isset($request['valeArchivos']) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
            //                                                'titulo' => 'Crear Requisicion',
            //                                                'subTitulo' => 'Error',
            //                                                'mensaje' => 'Debe capturar al menos una partida, de favor intente de nuevo' );
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida o subir un documento, de favor intente de nuevo' );
            $servicioId = $request['servicioId'];
            header("Location:" . Route::routes('servicios.crear-requisicion', $servicioId));

            die();            

        }
        // elseif ( !isset($request['detalles']) && ( !isset($request['comprobanteArchivos']) || ( count($request['comprobanteArchivos']['name']) == 1 && $request['comprobanteArchivos']['name'][0] == '' ) ) && ( !isset($request['ordenesArchivos']) || ( count($request['ordenesArchivos']['name']) == 1 && $request['ordenesArchivos']['name'][0] == '' ) ) && ( count($request['facturaArchivos']['name']) == 1 && $request['facturaArchivos']['name'][0] == '' ) && ( count($request['cotizacionArchivos']['name']) == 1 && $request['cotizacionArchivos']['name'][0] == '' ) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
            //                                                'titulo' => 'Crear Requisicion',
            //                                                'subTitulo' => 'Error',
            //                                                'mensaje' => 'Debe capturar al menos una partida o subir un comprobante de pago u órden de compra o una factura o cotización, de favor intente de nuevo' );
            // $servicioId = $request['servicioId'];
            // header("Location:" . Route::routes('servicios.crear-requisicion', $servicioId));

            // die();

        // }

        $requisicion = New Requisicion;
        $respuesta = $requisicion->crear($request);

        if ( $respuesta ) {

            $this->sendMailCreacion($requisicion);

            $uploadDocumentos = array();
            if ( isset($request['comprobanteArchivos']) ) 
                array_push($uploadDocumentos, [
                    'id' => 1,
                    'tipoDocumento' => 'Comprobante de Pago',
                    'documentos' => $request['comprobanteArchivos']['name']
                ]);
            if ( isset($request['ordenesArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 2,
                    'tipoDocumento' => 'Orden de Compra',
                    'documentos' => $request['ordenesArchivos']['name']
                ]);
            if ( isset($request['facturaArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 3,
                    'tipoDocumento' => 'Factura',
                    'documentos' => $request['facturaArchivos']['name']
                ]);
            if ( isset($request['cotizacionArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 4,
                    'tipoDocumento' => 'Cotización',
                    'documentos' => $request['cotizacionArchivos']['name']
                ]);
            if ( isset($request['valeArchivos']) )
                array_push($uploadDocumentos, [
                    'id' => 5,
                    'tipoDocumento' => 'Vale de Almacén',
                    'documentos' => $request['valeArchivos']['name']
                ]);

            if ( $uploadDocumentos ) $this->sendMailUploadDocumento($requisicion, $uploadDocumentos);

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Requisicion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La requisición fue creada correctamente' );
            header("Location:" . Route::names('requisiciones.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            // header("Location:" . Route::names('requisiciones.create'));
            $servicioId = $request['servicioId'];
            header("Location:" . Route::routes('servicios.crear-requisicion', $servicioId));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New RequisicionGasto);

        $requisicion = New RequisicionGasto;

        if ( $requisicion->consultar(null , $id) ) {

            $requisicion->consultarObservaciones();
            $requisicion->consultarDetalles();
            $requisicion->consultarComprobantes();
            // $requisicion->consultarFacturas();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/Gastos.php";
            $gasto = New \App\Models\Gastos;
            $gasto->consultar('requisicionId',$requisicion->id);

            $rutaGasto = Route::names('gastos.edit', $gasto->id);;

            require_once "app/Models/ServicioEstatus.php";
            $servicioEstatus = New \App\Models\ServicioEstatus;
            $servicioStatus = $servicioEstatus->consultar();

            require_once "app/Models/ConfiguracionRequisicion.php";
            $configuracionRequisicion = New \App\Models\ConfiguracionRequisicion;
            $configuracionRequisicion->consultar(null, 1);
            $configuracionRequisicion->consultarPerfiles();
            $configuracionRequisicion->consultarFlujo();

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            $usuario->consultarPerfiles();

            // Buscar permiso para Modificar Estatus
            $permitirModificarEstatus = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-status", "ver") ) $permitirModificarEstatus = true;

            // if ( !$permitirModificarEstatus ) {
                $servicioStatus = array();
                // array_push($servicioStatus, $servicioEstatus->consultar(null, $configuracionRequisicion->inicialServicioEstatusId));
                array_push($servicioStatus, $requisicion->estatus);
            // }

            $cambioAutomaticoEstatus = false;
            if ( $permitirModificarEstatus ) {
                // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || $requisicion->usuarioIdCreacion == $usuario->id ) {
                    $servicioStatusUsuarioCreacion = $servicioEstatus->consultar();
                    foreach ($servicioStatusUsuarioCreacion as $key => $nuevoEstatus) {
                        if ( $nuevoEstatus['requisicionUsuarioCreacion'] ) {
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionRequisicion->checkFlujo($requisicion->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) array_push($servicioStatus, $nuevoEstatus);
                        }
                    }
                }

                // Agregar estatus de acuerdo al Perfil (Pantalla Configuración - Requisiciones)
                foreach ($configuracionRequisicion->perfiles as $key => $value) {
                    if ( Autorizacion::perfil($usuario, CONST_ADMIN) || in_array($key, $usuario->perfiles) ) {
                        foreach ($value as $key2 => $value2) {
                            $nuevoEstatus = $servicioEstatus->consultar(null, $value2['servicioEstatusId']);

                            if ( !$configuracionRequisicion->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "modificar") ) continue;

                            // if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionRequisicion->checkFlujo($requisicion->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) array_push($servicioStatus, $nuevoEstatus);
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionRequisicion->checkFlujo($requisicion->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) {

                                if ( $configuracionRequisicion->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "automatico") ) {
                                    $servicioStatus = array();
                                    array_push($servicioStatus, $nuevoEstatus);
                                    $cambioAutomaticoEstatus = true;
                                    break;
                                }

                                array_push($servicioStatus, $nuevoEstatus);

                            } 
                        }
                    }
                }
            }

            // Buscar permiso para Agregar Observaciones
            $permitirAgregarObservaciones = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requi-observaciones", "ver") ) $permitirAgregarObservaciones = true;

            // Buscar permiso para Subir Archivos
            $permitirSubirArchivos = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-subir", "ver") ) $permitirSubirArchivos = true;
            // Buscar permiso para Eliminar Archivos
            $permitirEliminarArchivos = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-subir", "eliminar") ) $permitirEliminarArchivos = true;

            // Buscar permiso para Agregar Partidas
            $permitirAgregarPartida = false;
            if ( $requisicion->estatus['requisicionAgregarPartidas'] ) $permitirAgregarPartida = true;

            // Buscar permiso para Eliminar Partidas
            $permitirEliminarPartida = false;
            if ( $permitirAgregarPartida && !$configuracionRequisicion->usuarioCreacionEliminarPartidas ) $permitirEliminarPartida = true;
            if ( ( Autorizacion::perfil($usuario, CONST_ADMIN) || $requisicion->usuarioIdCreacion == $usuario->id ) && $permitirAgregarPartida && $configuracionRequisicion->usuarioCreacionEliminarPartidas ) $permitirEliminarPartida = true;

            $formularioEditable = false;
            if ( $requisicion->estatus["requisicionAbierta"] ) $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/requisicion-gastos/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New RequisicionGasto);

        if ( isset($_REQUEST['servicioId']) || isset($_REQUEST['servicioEstatusId']) || isset($_REQUEST['observacion']) ) $request = SaveRequisicionesRequest::validated($id);
        else $request = Request::value($id);

        // var_dump($request);
        // die();

        // if ( !isset($request['servicioEstatusId']) && !isset($request['detalles']) && ( count($request['facturaArchivos']['name']) == 1 && $request['facturaArchivos']['name'][0] == '' ) && ( count($request['cotizacionArchivos']['name']) == 1 && $request['cotizacionArchivos']['name'][0] == '' ) && !isset($request['partidasEliminadas']) ) {
        if ( !isset($request['servicioEstatusId']) && !isset($request['detalles']) && !isset($request['facturaArchivos']) && !isset($request['cotizacionArchivos']) && !isset($request['valeArchivos']) && !isset($request['partidasEliminadas']) ) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
            //                                                'titulo' => 'Actualizar Requisicion',
            //                                                'subTitulo' => 'Error',
            //                                                'mensaje' => 'Debe capturar al menos una partida o subir una factura o cotización, de favor intente de nuevo' );
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida o subir un documento, de favor intente de nuevo' );
            header("Location:" . Route::names('requisiciones.edit', $id));

            die();

        }

        $mensaje = 'La requisición fue actualizada correctamente';

        $requisicion = New RequisicionGasto;
        $requisicion->id = $id;
        $respuesta = $requisicion->actualizar($request);

        if ($respuesta) {

            if ( !is_null($requisicion->servicioEstatusId) && $requisicion->servicioEstatusId != $request['actualServicioEstatusId'] ) {

                $requisicion->consultar(null , $id);

            }

            $uploadDocumentos = array();
            if ( isset($request['comprobanteArchivos']) ) 
                array_push($uploadDocumentos, [
                    'id' => 1,
                    'tipoDocumento' => 'Comprobante de Pago',
                    'documentos' => $request['comprobanteArchivos']['name']
                ]);

            if ( $uploadDocumentos ) {

                if ( is_null($requisicion->folio) ) $requisicion->consultar(null , $id);

            }

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'OK',
                                                           // 'mensaje' => 'La requisición fue actualizada correctamente' );
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('requisicion-gastos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('requisicion-gastos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Requisicion);;

        // Sirve para validar el Token
        if ( !SaveRequisicionesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('requisiciones.index'));
            die();

        }

        $requisicion = New Requisicion;
        $requisicion->id = $id;
        // $requisicion->empresaId = empresaId();
        $respuesta = $requisicion->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Requisicion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La requisición fue eliminada correctamente' );
            header("Location:" . Route::names('requisiciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta requisición no se podrá eliminar ***' );
            header("Location:" . Route::names('requisiciones.index'));

        }
        
        die();

    }

    public function download($id, $tipo)
    {
        Autorizacion::authorize('view', New Requisicion);

        $requisicion = New Requisicion;

        $respuesta = array();

        if ( $requisicion->consultar(null , $id) ) {

            $requisicion->consultarComprobantes();
            $requisicion->consultarOrdenes();
            $requisicion->consultarFacturas();
            $requisicion->consultarCotizaciones();
            $requisicion->consultarVales();

            if ( $tipo == 1 ) $archivos = $requisicion->comprobantesPago;
            elseif ( $tipo == 2 ) $archivos = $requisicion->ordenesCompra;
            elseif ( $tipo == 3 ) $archivos = $requisicion->facturas;
            elseif ( $tipo == 4 ) $archivos = $requisicion->cotizaciones;
            elseif ( $tipo == 5 ) $archivos = $requisicion->valesAlmacen;

            $respuesta = array( 'codigo' => ( count($archivos) > 0 ) ? 200 : 204,
                                'error' => false,
                                'cantidad' => count($archivos),
                                'archivos' => $archivos );

        } else {
            $respuesta = array( 'codigo' => 500,
                                'error' => true,
                                'errorMessage' => 'No se logró consultar la Requisición' );
        }

        echo json_encode($respuesta);
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New RequisicionGasto);

        $requisicion = New RequisicionGasto;

        if ( $requisicion->consultar(null , $id) ) {

            $requisicion->consultarDetalles();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar(null,$requisicion->empresa);

            require_once "app/Models/Gastos.php";
            $gasto = New \App\Models\Gastos;
            $gasto->consultar('requisicionId',$requisicion->id);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null,$gasto->obra);

            require_once "app/Models/ServicioEstatus.php";
            $servicioEstatus = New \App\Models\ServicioEstatus;
            $servicioStatus = $servicioEstatus->consultar();

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $gasto->encargado);

            $usuarioNombre = $usuario->nombre;
            $solicito = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $solicito .= ' ' . $usuario->apellidoMaterno;
            $solicitoFirma = $usuario->firma;
            unset($usuario);

            $responsableFirma = null;
            $revisoFirma = null;
            
            $usuario = New \App\Models\Usuario;
            $almacen = 'VB ALMACEN';
            $reviso = '';
            if ( !is_null($requisicion->usuarioIdAlmacen) ){
                $usuario->consultar(null, $requisicion->usuarioIdAlmacen);
                $usuario->consultarPerfiles();
                
                if (in_arrayi('comprobaciones', $usuario->perfiles)) {
                    $almacen = 'VB COMPROBACIONES';
                }
                
                $reviso = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
                if ( !is_null($usuario->apellidoMaterno) ) $reviso .= ' ' . $usuario->apellidoMaterno;
                $revisoFirma = $usuario->firma;
                unset($usuario);
            }


            $responsable = '';
            if ( !is_null($requisicion->usuarioIdResponsable) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $requisicion->usuarioIdResponsable);

                $usuarioNombre = $usuario->nombre;
                $responsable = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
                if ( !is_null($usuario->apellidoMaterno) ) $responsable .= ' ' . $usuario->apellidoMaterno;
                $responsableFirma = $usuario->firma;
                unset($usuario);
            }


            if ( $requisicion->empresa == 2) {
                include "reportes/requisicion_gastos_indheca.php";
            } else {
                include "reportes/requisicion_gastos.php";
            }

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function sendMailCreacion(Requisicion $requisicion)
    {
        $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
        if ( $configuracionCorreoElectronico->consultar(null , 1) ) {

            $configuracionCorreoElectronico->consultarPerfilesCrear();    
            if ( $configuracionCorreoElectronico->perfilesCrear ) {

                $perfil = New Perfil;
                $perfil->consultarUsuarios($configuracionCorreoElectronico->perfilesCrear);

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

                $folio = mb_strtoupper($requisicion->folio);
                $liga = Route::names('requisiciones.edit', $requisicion->id);
                $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>

                        <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>

                            <center>

                                <h3 style='font-weight: 100; color: #999'>NUEVA REQUISICION</h3>

                                <hr style='border: 1px solid #ccc; width: 80%'>
                                
                                <br>

                                <a style='text-decoration: none' href='{$liga}' target='_blank'>
                                    <div style='line-height: 60px; background: #0aa; width: 60%; color: white'>Ha sido creada la requisición {$folio}</div>

                                </a>

                                <h5 style='font-weight: 100; color: #999'>Haga click para ver el detalle de la misma</h5>

                                <hr style='border: 1px solid #ccc; width: 80%'>

                                <h5 style='font-weight: 100; color: #999'>Este correo ha sido enviado para informar al personal autorizado de la creación de una nueva requisición, si no solicitó esta información favor de ignorar y eliminar este correo.</h5>

                            </center>

                        </div>
                            
                    </div>";

                $datos = [ "mensajeTipoId" => 3,
                           "mensajeEstatusId" => 1,
                           "asunto" => "Nueva requisición {$folio}",
                           "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                           "mensaje" => "Ha sido creada la requisición {$folio}, entre a la aplicación para ver el detalle de la misma.",
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
