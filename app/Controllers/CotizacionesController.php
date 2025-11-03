<?php

namespace App\Controllers;

require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Cotizacion.php";
require_once "app/Models/Usuario.php";
require_once "app/Policies/CotizacionPolicy.php";
require_once "app/Requests/SaveCotizacionesRequest.php";
require_once "app/Models/Proveedor.php";
require_once "app/Models/Mensaje.php";
require_once "app/Models/ConfiguracionCorreoElectronico.php";
require_once "app/Controllers/MailController.php";

use App\Models\Cotizacion;
use App\Models\Usuario;
use App\Models\Proveedor;
use App\Models\Mensaje;
use App\Models\ConfiguracionCorreoElectronico;
use App\Controllers\MailController;
use App\Policies\CotizacionPolicy;
use App\Requests\SaveCotizacionesRequest;
use App\Route;


class CotizacionesController
{
    public function index()
    {

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/cotizaciones/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function create($id)
    {
        $cotizacion = new Cotizacion;
        Autorizacion::authorize('create', $cotizacion);

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null, $id);
        $requisicion->consultarDetalles();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null , $id);
        $requisicion->consultarDetalles();

        $requerimientos = $requisicion->detalles;

        // var_dump($requerimientos); die();

        $contenido = array('modulo' => 'vistas/modulos/cotizaciones/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store($id)
    {
        Autorizacion::authorize('create', New Cotizacion);
        
        $request = SaveCotizacionesRequest::validated();

        
        $cotizacion = New Cotizacion;
        $request["requisicionId"] = $id;
        $respuesta = $cotizacion->crear($request);
        
        // ENVIAR CORREO AL PROVEEDOR
        $correoProveedor = null;
        if (isset($request["vendedorId"]) && $request["vendedorId"]!="") {
            require_once "app/Models/Vendedor.php";
            $vendedor = New \App\Models\Vendedor;
            $vendedor->consultar(null, $request["vendedorId"]);
            $correoProveedor = $vendedor->correo;
        }else{
            $proveedor = New Proveedor;
            $proveedor->consultar(null, $request["proveedorId"]);
            $correoProveedor = $proveedor->correo;
        }


        if (!is_null($correoProveedor)) {
            $userSendMessageArray = [
                "usuarioId" => 1,
                "correo" => $correoProveedor
            ];

            $liga = Route::rutaServidorProveedor();
            $mensajeHTML = "<body style='margin:0; padding:0; background:#f0f6fc; font-family:sans-serif;'>
                                <div style='width:100%; padding-top:40px; padding-bottom:40px; background:#f0f6fc;'></div>
                                <div style='max-width:600px; margin:auto; background:white; padding:24px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.05);'>
                                    <div style='text-align:center'>
                                        <h2 style='color:#2563eb; font-weight:normal;'>¡Solicitud de Cotización Recibida!</h2>
                                        <hr style='border:1px solid #bcd0ee; width:80%;'>

                                        <div style='background:#2563eb; color:white; padding:16px 0; margin:24px 0; width:80%; border-radius:5px; display:inline-block;'>
                                            Has recibido una solicitud de cotización. Por favor ingresa a la página para subir tu propuesta.
                                        </div>

                                        <p style='color:#3b4a5a; font-size:15px; margin:20px 0 30px;'>
                                            Ingresa al portal para cargar tu cotización y participar en el proceso de selección. Si tienes dudas, contacta al equipo encargado.
                                        </p>

                                        <a href='{$liga}' style='display:inline-block; background:#1e40af; color:white; text-decoration:none; padding:12px 32px; border-radius:5px; font-size:16px; font-weight:500; margin-bottom:20px;'>
                                            Subir mi cotización
                                        </a>

                                        <hr style='border:1px solid #bcd0ee; width:80%;'>

                                        <p style='color:#7b8ca7; font-size:13px;'>
                                            Este mensaje ha sido enviado automáticamente. Si no eres el destinatario, puedes ignorar este correo.
                                        </p>
                                    </div>
                                </div>
                            </body>";

            $datosCorreo = [ 
                "mensajeHTML" => $mensajeHTML,
                "asunto" => "Solicitud de Cotización",
                "mensaje" => "Mensaje de solicitud de cotización"
            ];

            $respuestaEnvioCorreo = enviarCorreo($userSendMessageArray,$datosCorreo);

            // ENVIAR NOTIFICACION AL PROVEEDOR
        }

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Cotizacion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue creado correctamente' );
            header("Location:" . Route::names('requisiciones.edit', $id));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Cotizacion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('requisiciones.edit', $id));

        }
        
        die();

    }

    public function edit($id)
    {

        $cotizacion = New Cotizacion;

        if ( $cotizacion->consultar(null , $id) ) {

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $cotizacion->requisicionId);
            $requisicion->consultarDetalles();
            $cotizacion->consultarDetalles();
            $requerimientos = $requisicion->detalles;
            if ( count($requisicion->detalles) > count($cotizacion->detalles) ) $requerimientos = $cotizacion->detalles;
            $requisicion->consultarCotizacionesProveedor(\usuarioAutenticadoProveedor()["id"]);
            // $soportes = $requisicion->consultarSoportesProveedor(usuarioAutenticadoProveedor()["id"]);

            $contenido = array('modulo' => 'vistas/modulos/cotizaciones/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla_proveedores.php";


    }

    public function update($id)
    {

        $request = SaveCotizacionesRequest::validated($id);

        $cotizacion = New Cotizacion;
        $cotizacion->id = $id;
        $respuesta = $cotizacion->insertarArchivos($_POST["requisicionId"], $_FILES["cotizacionArchivos"]);

        $respuesta = $cotizacion->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Cotizacion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La cotizacion fue actualizado correctamente' );
            header("Location:" . Route::names('cotizaciones.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Cotizacion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('cotizaciones.edit', $id));

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
        $cotizacion = New Cotizacion;
        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;

        if ( $cotizacion->consultar(null , $id) ) {
            $requisicion->consultar(null,$cotizacion->requisicionId);
            $requisicion->consultarDetalles();
            $cotizacion->consultarDetalles();

            if ( count($requisicion->detalles) > count($cotizacion->detalles) ) {
                $requisicion->detalles = $cotizacion->detalles;
                $requisicion->folio = $requisicion->folio . ' - A';
            }

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $requisicion->servicio['empresaId']);

            require_once "app/Models/MantenimientoTipo.php";
            $mantenimientoTipo = New \App\Models\MantenimientoTipo;
            $mantenimientoTipo->consultar(null, $requisicion->servicio['mantenimientoTipoId']);

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinaria->consultar(null, $requisicion->servicio['maquinariaId']);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null,$requisicion->servicio['obraId'] ?? $maquinaria->obraId);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $requisicion->usuarioIdCreacion);

            require_once "app/Models/Servicio.php";
            $servicio = New \App\Models\Servicio;
            $servicio->consultar(null, $requisicion->servicioId);

            require_once "app/Models/ServicioCentro.php";
            $servicioCentro = New \App\Models\ServicioCentro;
            $servicioCentro->consultar(null, $servicio->servicioCentroId);

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


            if ( $requisicion->servicio['empresaId'] == 2) {
                include "reportes/requisicion_indheca.php";
            } else {
                include "reportes/requisicion.php";
            }


        }
    }

}

?>