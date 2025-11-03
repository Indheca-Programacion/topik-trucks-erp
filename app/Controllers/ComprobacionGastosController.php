<?php

namespace App\Controllers;

require_once "app/Models/ComprobacionGasto.php";
require_once "app/Models/Usuario.php";
require_once "app/Requests/SaveComprobacionGastoRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\ComprobacionGasto;
use App\Models\Usuario;
use App\Requests\SaveComprobacionGastoRequest;
use App\Route;

class ComprobacionGastosController
{
    public function index()
    {
        Autorizacion::authorize('view', New ComprobacionGasto);

        $contenido = array('modulo' => 'vistas/modulos/comprobacion-gasto/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $comprobacionGasto = new ComprobacionGasto;
        Autorizacion::authorize('create', $comprobacionGasto);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/ServicioEstatus.php";
        $servicioEstatus = New \App\Models\ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar(null, 9);

        $servicioStatus = array();
        array_push($servicioStatus, ["id" => 9, "descripcion" => "Por atender"]);

        $permitirEliminarArchivos = false;
        $permitirSubirArchivos = false;
        $permitirAgregarPartida = true;
        $permitirEliminarPartida = false;

        $formularioEditable = true;

        $contenido = array('modulo' => 'vistas/modulos/comprobacion-gasto/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New ComprobacionGasto);

        $request = SaveComprobacionGastoRequest::validated();

        if ( !isset($request['comprobanteArchivos']) && !isset($request['detalles']) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida o subir un documento, de favor intente de nuevo' );
            header("Location:" . Route::routes('comprobacion-gastos.create'));

            die();            

        }

        

        $comprobacionGasto = New ComprobacionGasto;
        $respuesta = $comprobacionGasto->crear($request);

        if ( $respuesta ) {

            // $this->sendMailCreacion($comprobacionGasto->id);

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Requisicion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La requisición fue creada correctamente' );
            header("Location:" . Route::names('comprobacion-gastos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
        
            header("Location:" . Route::routes('comprobacion-gastos.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ComprobacionGasto);

        $comprobacionGasto = New ComprobacionGasto;

        if ( $comprobacionGasto->consultar(null , $id) ) {

            $comprobacionGasto->consultarObservaciones();
            $comprobacionGasto->consultarDetalles();
            $comprobacionGasto->consultarComprobantes();
            $comprobacionGasto->consultarSoportes();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultarActivos();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultar();

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

            $servicioStatus = array();
            array_push($servicioStatus, $comprobacionGasto->estatus);

            $cambioAutomaticoEstatus = false;
            if ( $permitirModificarEstatus ) {
                // Agregar estatus si es el Usuario que creó la Requisición (Pantalla Servicios - Estatus)
                if ( Autorizacion::perfil($usuario, CONST_ADMIN) || $comprobacionGasto->usuarioIdCreacion == $usuario->id ) {
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
                            if ( !in_array($nuevoEstatus, $servicioStatus) && $configuracionRequisicion->checkFlujo($comprobacionGasto->estatus["descripcion"], $nuevoEstatus["descripcion"]) ) {

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
            if ( $comprobacionGasto->estatus['requisicionAgregarPartidas'] ) $permitirAgregarPartida = true;

            // Buscar permiso para Eliminar Partidas
            $permitirEliminarPartida = false;
            if ( $permitirAgregarPartida && !$configuracionRequisicion->usuarioCreacionEliminarPartidas ) $permitirEliminarPartida = true;
            if ( ( Autorizacion::perfil($usuario, CONST_ADMIN) || $comprobacionGasto->usuarioIdCreacion == $usuario->id ) && $permitirAgregarPartida && $configuracionRequisicion->usuarioCreacionEliminarPartidas ) $permitirEliminarPartida = true;

            $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/comprobacion-gasto/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ComprobacionGasto);

        if ( isset($_REQUEST['servicioId']) || isset($_REQUEST['servicioEstatusId']) || isset($_REQUEST['observacion']) ) $request = SaveComprobacionGastoRequest::validated($id);
        else $request = Request::value($id);

        if ( !isset($request['servicioEstatusId']) && !isset($request['detalles'])  && !isset($request['soporteArchivos']) && !isset($request['comprobanteArchivos']) && !isset($request['partidasEliminadas']) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida o subir un documento, de favor intente de nuevo' );
            header("Location:" . Route::names('requisiciones.edit', $id));

            die();

        }

        $mensaje = 'La comprobación de gastos fue actualizada correctamente';

        $comprobacion = New ComprobacionGasto;
        $comprobacion->id = $id;
        $respuesta = $comprobacion->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Comprobacion de Gastos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('comprobacion-gastos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Requisicion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('comprobacion-gastos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New ComprobacionGasto);

        // Sirve para validar el Token
        if ( !SaveComprobacionGastoRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Comprobacion de Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('comprobacion-gastos.index'));
            die();

        }

        $comprobacion = New ComprobacionGasto;
        $comprobacion->id = $id;
        $respuesta = $comprobacion->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Comprobacion de Gastos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La comprobación de gastos fue eliminada correctamente' );
            header("Location:" . Route::names('comprobacion-gastos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Comprobacion de Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta comprobación de gastos no se podrá eliminar ***' );
            header("Location:" . Route::names('comprobacion-gastos.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New ComprobacionGasto);

        $comprobacion = New ComprobacionGasto;

        if ( $comprobacion->consultar(null , $id) ) {

            $comprobacion->consultarDetalles();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $comprobacion->empresaId);

            require_once "app/Models/MantenimientoTipo.php";
            $mantenimientoTipo = New \App\Models\MantenimientoTipo;
            $mantenimientoTipo->consultar(null, 7);

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinaria->consultar(null, $comprobacion->maquinariaId);

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null,$comprobacion->obraId ?? $maquinaria->obraId);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $comprobacion->usuarioIdCreacion);

            require_once "app/Models/Servicio.php";
            $servicio = New \App\Models\Servicio;
            $servicio->consultar(null, 0);

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
            if ( !is_null($comprobacion->usuarioIdAlmacen) ){
                $usuario->consultar(null, $comprobacion->usuarioIdAlmacen);
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
            if ( !is_null($comprobacion->usuarioIdResponsable) ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $comprobacion->usuarioIdResponsable);

                $usuarioNombre = $usuario->nombre;
                $responsable = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
                if ( !is_null($usuario->apellidoMaterno) ) $responsable .= ' ' . $usuario->apellidoMaterno;
                $responsableFirma = $usuario->firma;
                unset($usuario);
            }

            include "reportes/requisicionComprobacion.php";


        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
