<?php

namespace App\Controllers;

require_once "app/Models/ServicioTipo.php";
require_once "app/Policies/ServicioTipoPolicy.php";
require_once "app/Requests/SaveServicioTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\ServicioTipo;
use App\Policies\ServicioTipoPolicy;
use App\Requests\SaveServicioTiposRequest;
use App\Route;

class ServicioTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', New ServicioTipo);

        $servicioTipo = New ServicioTipo;
        $servicioTipos = $servicioTipo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/servicio-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $servicioTipo = New ServicioTipo;
        Autorizacion::authorize('create', $servicioTipo);

        require_once "app/Models/Unidad.php";
        $unidad = New \App\Models\Unidad;
        $unidades = $unidad->consultar();

        $contenido = array('modulo' => 'vistas/modulos/servicio-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New ServicioTipo);

        $request = SaveServicioTiposRequest::validated();

        $servicioTipo = New ServicioTipo;
        $respuesta = $servicioTipo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de servicio fue creado correctamente' );
            header("Location:" . Route::names('servicio-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('servicio-tipos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ServicioTipo);

        $servicioTipo = New ServicioTipo;

        if ( $servicioTipo->consultar(null , $id) ) {
            require_once "app/Models/Unidad.php";
            $unidad = New \App\Models\Unidad;
            $unidades = $unidad->consultar();

            $contenido = array('modulo' => 'vistas/modulos/servicio-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ServicioTipo);

        $request = SaveServicioTiposRequest::validated($id);

        $servicioTipo = New ServicioTipo;
        $servicioTipo->id = $id;
        $respuesta = $servicioTipo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipo de Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de servicio fue actualizado correctamente' );
            header("Location:" . Route::names('servicio-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipo de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('servicio-tipos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New ServicioTipo);

        // Sirve para validar el Token
        if ( !SaveServicioTiposRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('servicio-tipos.index'));
            die();

        }

        $servicioTipo = New ServicioTipo;
        $servicioTipo->id = $id;
        $respuesta = $servicioTipo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tipo de Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de servicio fue eliminado correctamente' );

            header("Location:" . Route::names('servicio-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de servicio no se podrá eliminar ***' );
            header("Location:" . Route::names('servicio-tipos.index'));

        }
        
        die();

    }
}
