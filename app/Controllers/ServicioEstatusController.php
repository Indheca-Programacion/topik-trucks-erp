<?php

namespace App\Controllers;

require_once "app/Models/ServicioEstatus.php";
require_once "app/Policies/ServicioEstatusPolicy.php";
require_once "app/Requests/SaveServicioEstatusRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\ServicioEstatus;
use App\Policies\ServicioEstatusPolicy;
use App\Requests\SaveServicioEstatusRequest;
use App\Route;

class ServicioEstatusController
{
    public function index()
    {
        Autorizacion::authorize('view', New ServicioEstatus);

        $servicioEstatus = New ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/servicio-estatus/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $servicioEstatus = New ServicioEstatus;
        Autorizacion::authorize('create', $servicioEstatus);

        $contenido = array('modulo' => 'vistas/modulos/servicio-estatus/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New ServicioEstatus);

        $request = SaveServicioEstatusRequest::validated();

        $servicioEstatus = New ServicioEstatus;
        $respuesta = $servicioEstatus->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue creado correctamente' );
            header("Location:" . Route::names('servicio-estatus.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('servicio-estatus.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ServicioEstatus);

        $servicioEstatus = New ServicioEstatus;

        if ( $servicioEstatus->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/servicio-estatus/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ServicioEstatus);

        $request = SaveServicioEstatusRequest::validated($id);

        $servicioEstatus = New ServicioEstatus;
        $servicioEstatus->id = $id;
        $respuesta = $servicioEstatus->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue actualizado correctamente' );
            header("Location:" . Route::names('servicio-estatus.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('servicio-estatus.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New ServicioEstatus);

        // Sirve para validar el Token
        if ( !SaveServicioEstatusRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('servicio-estatus.index'));
            die();

        }

        $servicioEstatus = New ServicioEstatus;
        $servicioEstatus->id = $id;
        $respuesta = $servicioEstatus->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue eliminado correctamente' );

            header("Location:" . Route::names('servicio-estatus.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('servicio-estatus.index'));

        }
        
        die();

    }
}
