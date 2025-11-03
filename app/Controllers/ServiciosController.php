<?php

namespace App\Controllers;

require_once "app/Models/Usuario.php";
require_once "app/Models/Servicio.php";
require_once "app/Policies/ServicioPolicy.php";
require_once "app/Requests/SaveServiciosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Usuario;
use App\Models\Servicio;
use App\Policies\ServicioPolicy;
use App\Requests\SaveServiciosRequest;
// use App\Requests\Request;
use App\Route;

class ServiciosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Servicio);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/ServicioCentro.php";
        $servicioCentro = New \App\Models\ServicioCentro;
        $servicioCentros = $servicioCentro->consultar();

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/MantenimientoTipo.php";
        $mantenimientoTipo = New \App\Models\MantenimientoTipo;
        $mantenimientosTipo = $mantenimientoTipo->consultar();

        require_once "app/Models/ServicioTipo.php";
        $servicioTipo = New \App\Models\ServicioTipo;
        $serviciosTipo = $servicioTipo->consultar();

        require_once "app/Models/ServicioEstatus.php";
        $servicioEstatus = New \App\Models\ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar();

        $contenido = array('modulo' => 'vistas/modulos/servicios/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $servicio = New Servicio;
        Autorizacion::authorize('create', $servicio);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/ServicioCentro.php";
        $servicioCentro = New \App\Models\ServicioCentro;
        $servicioCentros = $servicioCentro->consultar();

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/MantenimientoTipo.php";
        $mantenimientoTipo = New \App\Models\MantenimientoTipo;
        $mantenimientoTipos = $mantenimientoTipo->consultar();

        require_once "app/Models/ServicioTipo.php";
        $servicioTipo = New \App\Models\ServicioTipo;
        $servicioTipos = $servicioTipo->consultar();

        require_once "app/Models/ServicioEstatus.php";
        $servicioEstatus = New \App\Models\ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar();

        require_once "app/Models/SolicitudTipo.php";
        $solicitudTipo = New \App\Models\SolicitudTipo;
        $solicitudTipos = $solicitudTipo->consultar();

        require_once "app/Models/Ubicacion.php";
        $ubicacion = new \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Obra.php";
        $obra = new \App\Models\Obra;
        $obras = $obra->consultarAbiertas();

        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        // Buscar permiso para Modificar Fechas (Solicitud)
        $permitirModificarFechas = false;
        if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicios-mod-fechas", "ver") ) $permitirModificarFechas = true;

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/servicios/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New Servicio);

        $request = SaveServiciosRequest::validated();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, $request['empresaId']);
        $request['empresas.nomenclaturaOT'] = $empresa->nomenclaturaOT;

        require_once "app/Models/ServicioCentro.php";
        $servicioCentro = New \App\Models\ServicioCentro;
        $servicioCentro->consultar(null, $request['servicioCentroId']);
        $request['servicio_centros.nomenclaturaOT'] = $servicioCentro->nomenclaturaOT;

        $servicio = New Servicio;
        $respuesta = $servicio->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El servicio fue creado correctamente' );
            header("Location:" . Route::names('servicios.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('servicios.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Servicio);

        $servicio = New Servicio;

        if ( $servicio->consultar(null , $id) ) {

            // $servicio->consultarImagenes();
            $servicio->consultarRequisiciones();
            $servicio->consultarActividades();

            require_once "app/Models/Obra.php";
            $obra = new \App\Models\Obra;
            $obras = $obra->consultarAbiertas();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/ServicioCentro.php";
            $servicioCentro = New \App\Models\ServicioCentro;
            $servicioCentros = $servicioCentro->consultar();

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultar();

            require_once "app/Models/MantenimientoTipo.php";
            $mantenimientoTipo = New \App\Models\MantenimientoTipo;
            $mantenimientoTipos = $mantenimientoTipo->consultar();

            require_once "app/Models/ServicioTipo.php";
            $servicioTipo = New \App\Models\ServicioTipo;
            $servicioTipos = $servicioTipo->consultar();

            require_once "app/Models/ServicioEstatus.php";
            $servicioEstatus = New \App\Models\ServicioEstatus;
            $servicioStatus = $servicioEstatus->consultar();

            require_once "app/Models/SolicitudTipo.php";
            $solicitudTipo = New \App\Models\SolicitudTipo;
            $solicitudTipos = $solicitudTipo->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = new \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            // Buscar permiso para Modificar Fechas (Finalización Real)
            $permitirModificarFechas = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicios-mod-fechas", "ver") ) $permitirModificarFechas = true;

            // $permitirCerrar = false;
            // if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicios-cerrar", "ver") ) $permitirCerrar = true;

            // Buscar permiso para Solicitar Finalizar Servicios
            $permitirSolicitarFinalizar = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicios-solicitar", "ver") ) $permitirSolicitarFinalizar = true;

            // Buscar permiso para Finalizar Servicios
            $permitirFinalizar = false;
            if ( ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicios-finalizar", "ver") ) && $servicio->servicioEstatusId == 8 ) $permitirFinalizar = true;

            // Buscar permiso para Cancelar Servicios
            $permitirCancelar = false;
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || ( Autorizacion::permiso($usuario, "servicios-cancelar", "ver") && $servicio->usuarioIdCreacion == usuarioAutenticado()["id"] ) ) $permitirCancelar = true;

            unset($usuario);

            $formularioEditable = false;
            if ( $servicio->estatus["servicioAbierto"] ) $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/servicios/editar.php');

            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Servicio);

        $request = SaveServiciosRequest::validated($id);

        $mensaje = 'El servicio fue actualizado correctamente';
        if ( isset($request['servicioEstatusId']) ) {
            // if ( $request['servicioEstatusId'] == 3 || $request['servicioEstatusId'] == 4 ) $request['fechaFinalizacion'] = fFechaLarga(date("Y-m-d"));
            if ( $request['servicioEstatusId'] == 3 && !isset($request['fechaFinalizacion']) ) $request['fechaFinalizacion'] = fFechaLarga(date("Y-m-d"));
            if ( $request['servicioEstatusId'] == 4 ) $request['fechaFinalizacion'] = fFechaLarga(date("Y-m-d"));

            if ( $request['servicioEstatusId'] == 3 ) $mensaje = 'El servicio fue finalizado correctamente';
            else if ( $request['servicioEstatusId'] == 4 ) $mensaje = 'El servicio fue cancelado correctamente';
            else if ( $request['servicioEstatusId'] == 8 ) $mensaje = 'El servicio fue solicitado a finalizar correctamente';
        }

        $actualizarProgramacion = false;
        
        $servicio = New Servicio;
        $servicio->id = $id;
        if ( isset($request['servicioEstatusId']) && $request['servicioEstatusId'] == 3 ) {
            if ( isset($request["horoOdometro"]) ) $actualizarProgramacion = true;
            $respuesta = $servicio->finalizar($request);
        }
        if ( isset($request['servicioEstatusId']) && $request['servicioEstatusId'] == 3 ) $respuesta = $servicio->finalizar($request);
        else $respuesta = $servicio->actualizar($request);

        if ($respuesta) {
            if ( $actualizarProgramacion ) {
                $servicio->consultar(null , $id);

                $query = "SELECT P.*
                    FROM    programaciones P
                    WHERE   P.maquinariaId = {$servicio->maquinariaId}
                    AND     P.servicioTipoId = {$servicio->servicioTipoId}";

                $registroProgramacion = \App\Conexion::queryUnique(CONST_BD_APP, $query, $error);
                if ( $registroProgramacion ) {
                    require_once "app/Models/Programacion.php";
                    $programacion = New \App\Models\Programacion;

                    if ( $programacion->consultar(null , $registroProgramacion['id']) ) 
                        $programacion->actualizar([
                            'horoOdometroUltimo' => $servicio->horoOdometro
                        ]);
                }
            }
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Servicio',
                                                           'subTitulo' => 'OK',
                                                           // 'mensaje' => 'El servicio fue actualizado correctamente' );
                                                           'mensaje' => $mensaje );
            header("Location:" . Route::names('servicios.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('servicios.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Servicio);

        // Sirve para validar el Token
        if ( !SaveServiciosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('servicios.index'));
            die();

        }

        $servicio = New Servicio;
        $servicio->id = $id;
        // $servicio->empresaId = empresaId();
        $respuesta = $servicio->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El servicio fue eliminado correctamente' );
            header("Location:" . Route::names('servicios.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este servicio no se podrá eliminar ***' );
            header("Location:" . Route::names('servicios.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Servicio);

        $servicio = New Servicio;

        if ( $servicio->consultar(null , $id) ) {

            $servicio->consultarRequisiciones();
            $imagenes = $servicio->consultarImagenes($id);

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $servicio->empresaId);

            require_once "app/Models/ServicioCentro.php";
            $servicioCentro = New \App\Models\ServicioCentro;
            $servicioCentro->consultar(null, $servicio->servicioCentroId);

            require_once "app/Models/MantenimientoTipo.php";
            $mantenimientoTipo = New \App\Models\MantenimientoTipo;
            $mantenimientoTipo->consultar(null, $servicio->mantenimientoTipoId);

            require_once "app/Models/ServicioTipo.php";
            $servicioTipo = New \App\Models\ServicioTipo;
            $servicioTipo->consultar(null, $servicio->servicioTipoId);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $servicio->usuarioIdCreacion);

            $responsable = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $responsable .= ' ' . $usuario->apellidoMaterno;
            $responsableFirma = $usuario->firma;
            unset($usuario);

            $revision = '';
            $revisionFirma = null;
            if ( $servicio->estatus['id'] == 3 ) {
                $usuario = New \App\Models\Usuario;
                $usuario->consultar(null, $servicio->usuarioIdActualizacion);

                $revision = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
                if ( !is_null($usuario->apellidoMaterno) ) $revision .= ' ' . $usuario->apellidoMaterno;
                $revisionFirma = $usuario->firma;
                unset($usuario);
            }

            include "reportes/servicio.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }
}
