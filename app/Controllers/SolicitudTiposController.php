<?php

namespace App\Controllers;

require_once "app/Models/SolicitudTipo.php";
require_once "app/Policies/SolicitudTipoPolicy.php";
require_once "app/Requests/SaveSolicitudTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\SolicitudTipo;
use App\Policies\SolicitudTipoPolicy;
use App\Requests\SaveSolicitudTiposRequest;
use App\Route;

class SolicitudTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', New SolicitudTipo);

        $solicitudTipo = New SolicitudTipo;
        $solicitudTipos = $solicitudTipo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/solicitud-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $solicitudTipo = New SolicitudTipo;
        Autorizacion::authorize('create', $solicitudTipo);

        $contenido = array('modulo' => 'vistas/modulos/solicitud-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New SolicitudTipo);

        $request = SaveSolicitudTiposRequest::validated();

        $solicitudTipo = New SolicitudTipo;
        $respuesta = $solicitudTipo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Solicitud',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de solicitud fue creado correctamente' );
            header("Location:" . Route::names('solicitud-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Solicitud',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('solicitud-tipos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New SolicitudTipo);

        $solicitudTipo = New SolicitudTipo;

        if ( $solicitudTipo->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/solicitud-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New SolicitudTipo);

        $request = SaveSolicitudTiposRequest::validated($id);

        $solicitudTipo = New SolicitudTipo;
        $solicitudTipo->id = $id;
        $respuesta = $solicitudTipo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipo de Solicitud',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de solicitud fue actualizado correctamente' );
            header("Location:" . Route::names('solicitud-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipo de Solicitud',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('solicitud-tipos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New SolicitudTipo);

        // Sirve para validar el Token
        if ( !SaveSolicitudTiposRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Solicitud',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('solicitud-tipos.index'));
            die();

        }

        $solicitudTipo = New SolicitudTipo;
        $solicitudTipo->id = $id;
        $respuesta = $solicitudTipo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tipo de Solicitud',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de solicitud fue eliminado correctamente' );

            header("Location:" . Route::names('solicitud-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Solicitud',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de solicitud no se podrá eliminar ***' );
            header("Location:" . Route::names('solicitud-tipos.index'));

        }
        
        die();

    }
}
