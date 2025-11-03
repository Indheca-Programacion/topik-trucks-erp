<?php

namespace App\Controllers;

require_once "app/Models/OrdenCompra.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/ConfiguracionOrdenCompra.php";
require_once "app/Models/ConfiguracionCorreoElectronico.php";
require_once "app/Models/Mensaje.php";
require_once "app/Models/Estatus.php";
require_once "app/Models/Divisa.php";
require_once "app/Models/Proveedor.php";
require_once "app/Models/Usuario.php";

require_once "app/Policies/OrdenCompraPolicy.php";
require_once "app/Requests/SaveOrdenCompraRequest.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Controllers/MailController.php";

use App\Models\OrdenCompra;
use App\Models\Requisicion;
use App\Models\ConfiguracionOrdenCompra;
use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Mensaje;
use App\Models\Estatus;
use App\Models\Divisa;
use App\Models\Usuario;

use App\Models\Proveedor;

use App\Policies\OrdenCompraPolicy;
use App\Requests\SaveOrdenCompraRequest;
use App\Route;

class OrdenCompraController
{
    public function index()
    {
        Autorizacion::authorize('view', new OrdenCompra);

        require_once "app/Models/EstatusOrdenCompra.php";
        $estatus = New \App\Models\EstatusOrdenCompra;
        $estatuses = $estatus->consultar();
        
        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();
        
        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/OrdenCompra/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create($id)
    {
        $ordenCompra = new OrdenCompra;
        Autorizacion::authorize('create', $ordenCompra);

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null, $id);
        $requisicion->consultarDetalles();

        require_once "app/Models/EstatusOrdenCompra.php";
        $estatus = New \App\Models\EstatusOrdenCompra;
        $estatuses = $estatus->consultar();

        require_once "app/Models/ConfiguracionOrdenCompra.php";
        $configuracionOrdenCompra = New \App\Models\ConfiguracionOrdenCompra;
        $configuracionOrdenCompra->consultar(null, 1);


        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        // Buscar permiso para Modificar Estatus
        $permitirModificarEstatus = false;
        if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "orden-compra-status", "ver") ) $permitirModificarEstatus = true;

        $servicioStatus = array();
        array_push($servicioStatus, $estatus->consultar(null, $configuracionOrdenCompra->inicialEstatusId));

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();


        require_once "app/Models/EstatusOrdenCompra.php";
        $estatus = New \App\Models\EstatusOrdenCompra;
        $estatuses = $estatus->consultar(null,$configuracionOrdenCompra->inicialEstatusId);

        $contenido = array('modulo' => 'vistas/modulos/OrdenCompra/crear.php');

        include "vistas/modulos/plantilla.php";
    }
    public function store()
    {
        Autorizacion::authorize('create', New OrdenCompra);

        $request = SaveOrdenCompraRequest::validated();

        if ( !isset($request['detalles']) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida de favor intente de nuevo' );
            $requisicionId = $request['requisicionId'];
            header("Location:" . Route::routes('requisiciones.crear-orden-compra', $requisicionId));

            die();
        }
        
        $ordenCompra = New OrdenCompra;
        $respuesta = $ordenCompra->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue creado correctamente' );
            header("Location:" . Route::names('orden-compra.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('requisiciones.crear-orden-compra', $request['requisicionId']));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $ordenCompra = New OrdenCompra;

        if ( $ordenCompra->consultar(null , $id) ) {

            $ordenCompra->consultarDetalles();
            $ordenCompra->consultarObservaciones();
            $ordenCompra->consultarValesDigital();
            $ordenCompra->consultarSoportes();
            $ordenCompra->consultarPolizasContables();


            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);

            require_once "app/Models/EstatusOrdenCompra.php";
            $estatus = New \App\Models\EstatusOrdenCompra;
            $estatuses = $estatus->consultar();

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);

            $requisicion->consultarArchivosSinOC();

            $requisicion->consultarComprobantes();
            $requisicion->consultarOrdenes();
            $requisicion->consultarFacturas();
            $requisicion->consultarCotizaciones();
            $requisicion->consultarVales();
            $requisicion->consultarValesDigital();
            $requisicion->consultarOrdenesCompra();

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisas = $divisa->consultar();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            $usuario->consultarPerfiles();

            require_once "app/Models/ConfiguracionOrdenCompra.php";
            $configuracionOrdenCompra = New \App\Models\ConfiguracionOrdenCompra;
            $configuracionOrdenCompra->consultar(null, 1);
            $configuracionOrdenCompra->consultarPerfiles();
            $configuracionOrdenCompra->consultarFlujo();

            // OBTENER ESTATUS
             // Buscar permiso para Modificar Estatus
            $permitirModificarEstatus = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "requisiciones-status", "ver") ) $permitirModificarEstatus = true;

            $servicioStatus = array();

            array_push($servicioStatus, $ordenCompra->estatus);

            $cambioAutomaticoEstatus = false;


            if ( $permitirModificarEstatus ) {

                // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || $ordenCompra->usuarioIdCreacion == $usuario->id ) {

                    $servicioStatusUsuarioCreacion = $estatus->consultar();

                    foreach ($servicioStatusUsuarioCreacion as $key => $nuevoEstatus) {

                        if ( $nuevoEstatus['requisicionUsuarioCreacion'] ) {
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionOrdenCompra->checkFlujo($ordenCompra->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) array_push($servicioStatus, $nuevoEstatus);
                        }
                    }
                }
                
                // Agregar estatus de acuerdo al Perfil (Pantalla Configuración - Ordenes)


                foreach ($configuracionOrdenCompra->perfiles as $key => $value) {

                    if ( Autorizacion::perfil($usuario, CONST_ADMIN) || in_array($key, $usuario->perfiles) ) {

                        foreach ($value as $key2 => $value2) {

                            $nuevoEstatus = $estatus->consultar(null, $value2['EstatusId']);

                            if ( !$configuracionOrdenCompra->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "modificar") ) continue;
                            
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionOrdenCompra->checkFlujo($ordenCompra->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) {

                                if ( $configuracionOrdenCompra->checkPerfil($value2["perfiles.nombre"], $nuevoEstatus["descripcion"], "automatico") ) {
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
        
         $formularioEditable = false;
         if ( $requisicion->estatus["requisicionAbierta"] ) $formularioEditable = true;


            $contenido = array('modulo' => 'vistas/modulos/OrdenCompra/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla.php";


    }

    public function update($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $request = SaveOrdenCompraRequest::validated($id);

        // Validar comprobante de pago para ciertos estatus
        if ( $request["estatusId"] != $request["actualEstatusId"] && in_array($request["estatusId"], [4, 14]) && (empty($request['comprobanteArchivos']['name']))) {
            $_SESSION[CONST_SESSION_APP]["flash"] = array(
            'clase' => 'bg-danger',
            'titulo' => 'Actualizar Orden de compra',
            'subTitulo' => 'Error',
            'mensaje' => 'Debe cargar al menos un comprobante de pago, de favor intente de nuevo'
            );
            header("Location:" . Route::names('orden-compra.edit', $id));
            die();
        }

        $ordenCompra = New OrdenCompra;
        $ordenCompra->consultar(null , $id); // Para tener la ruta de la foto

        $respuesta = $ordenCompra->actualizar($request);

        $uploadDocumentos = array();
        if ( isset($request['comprobanteArchivos']) ) 
            array_push($uploadDocumentos, [
                'id' => 1,
                'tipoDocumento' => 'Comprobante de Pago',
                'documentos' => $request['comprobanteArchivos']['name']
            ]);

        
        $requisicion = New Requisicion;
        $requisicion->consultar(null, $ordenCompra->requisicionId);

        if ( $uploadDocumentos ) {

            $this->sendMailCambiarEstatus($requisicion);

        }


        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue actualizado correctamente' );
            header("Location:" . Route::names('orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('orden-compra.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new OrdenCompra);

        $ordenCompra = New OrdenCompra;
        
        $ordenCompra->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $ordenCompra->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue eliminado correctamente' );

            header("Location:" . Route::names('orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este proveedor no se podrá eliminar ***' );
            header("Location:" . Route::names('orden-compra.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New OrdenCompra);

        $ordenCompra = New OrdenCompra;
        $ordenCompra->ordenCompraId = $id;
        
        $ordenDeCompraDatos = $ordenCompra->consultarOrdenDeCompra();    

        if ( $ordenDeCompraDatos ) {

            foreach ($ordenDeCompraDatos as $key => $value) {
            
                require_once "app/Models/Requisicion.php";
                $requisicion = New \App\Models\Requisicion;
                $requisicion->consultar(null, $value["requisicionId"]);
                
                require_once "app/Models/Empresa.php";
                $empresa = New \App\Models\Empresa;
                $empresa->consultar(null, $requisicion->servicio["empresaId"]);
                    
                require_once "app/Models/Divisa.php";
                $divisa = New \App\Models\Divisa;
                $divisa->consultar(null, $value["monedaId"]);

                require_once "app/Models/Proveedor.php";
                $proveedor = New \App\Models\Proveedor;
                $proveedor->consultar(null, $value["proveedorId"]);

                require_once "app/Models/Obra.php";
                $obra = New \App\Models\Obra;
                $obra->consultar(null, $requisicion->servicio["obraId"]);

                $maquinaria = New \App\Models\Maquinaria;
                $maquinaria->consultarMaquinariaPorRequisicion($requisicion->id);

                require_once "app/Models/DatosBancarios.php";
                $datosBancarios = New \App\Models\DatosBancarios;
                $datosBancarios->consultar(null, $value["datoBancarioId"]);

                /********************** USUARIOS *****************************/
                require_once "app/Models/Usuario.php";
                $usuarioElabora = New \App\Models\Usuario;
                $usuarioAprueba = New \App\Models\Usuario;
                $usuarioAutoriza = New \App\Models\Usuario;
                /*****************************************************/

                /********************** USUARIO ELABORA *****************************/
                $usuarioElabora->consultar(null, $value["usuarioIdCreacion"]);

                // NOMBRE COMPLETO USUARIO ELABORA
                $nombreCompletoUsuarioElabora = mb_strtoupper($usuarioElabora->nombre . ' ' . $usuarioElabora->apellidoPaterno);
                if ( !is_null($usuarioElabora->apellidoMaterno) ) $nombreCompletoUsuarioElabora .= ' ' . mb_strtoupper($usuarioElabora->apellidoMaterno);

                // FIRMA USUARIO ELABORA
                $firmaUsuarioElabora = $usuarioElabora->firma;
                /*****************************************************/
                
                /********************** USUARIO APRUEBA *****************************/
                $usuarioAprueba->consultar(null,$value["usuarioIdAprobacion"]);

                // NOMBRE COMPLETO USUARIO APRUEBA
                $nombreCompletoUsuarioAprueba = mb_strtoupper($usuarioAprueba->nombre . ' ' . $usuarioAprueba->apellidoPaterno);
                if ( !is_null($usuarioAprueba->apellidoMaterno) ) $nombreCompletoUsuarioAprueba .= ' ' . mb_strtoupper($usuarioAprueba->apellidoMaterno);

                // FIRMA USUARIO APRUEBA
                $firmaUsuarioAprueba = $usuarioAprueba->firma;
                /*****************************************************/

                /********************** USUARIO AUTORIZA *****************************/
                $usuarioAutoriza->consultar(null, $value["usuarioIdAutorizacion"]);

                // NOMBRE COMPLETO USUARIO ELABORA
                $nombreCompletoUsuarioAutoriza = mb_strtoupper($usuarioAutoriza->nombre . ' ' . $usuarioAutoriza->apellidoPaterno);
                if ( !is_null($usuarioAutoriza->apellidoMaterno) ) $nombreCompletoUsuarioAutoriza .= ' ' . mb_strtoupper($usuarioAutoriza->apellidoMaterno);

                // FIRMA USUARIO ELABORA
                $firmaUsuarioAutoriza = $usuarioAutoriza->firma;
                /*****************************************************/

            
                include "reportes/ordencompra.php";
            }

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
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
}
