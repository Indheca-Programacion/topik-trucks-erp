<?php

namespace App\Controllers;

require_once "app/Models/MantenimientoTipo.php";
require_once "app/Policies/MantenimientoTipoPolicy.php";
require_once "app/Requests/SaveMantenimientoTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\MantenimientoTipo;
use App\Policies\MantenimientoTipoPolicy;
use App\Requests\SaveMantenimientoTiposRequest;
use App\Route;

class MantenimientoTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', New MantenimientoTipo);

        $mantenimientoTipo = New MantenimientoTipo;
        $mantenimientoTipos = $mantenimientoTipo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/mantenimiento-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $mantenimientoTipo = New MantenimientoTipo;
        Autorizacion::authorize('create', $mantenimientoTipo);

        $contenido = array('modulo' => 'vistas/modulos/mantenimiento-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New MantenimientoTipo);

        $request = SaveMantenimientoTiposRequest::validated();

        $mantenimientoTipo = New MantenimientoTipo;
        $respuesta = $mantenimientoTipo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Mantenimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de mantenimiento fue creado correctamente' );
            header("Location:" . Route::names('mantenimiento-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('mantenimiento-tipos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New MantenimientoTipo);

        $mantenimientoTipo = New MantenimientoTipo;

        if ( $mantenimientoTipo->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/mantenimiento-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New MantenimientoTipo);

        $request = SaveMantenimientoTiposRequest::validated($id);

        $mantenimientoTipo = New MantenimientoTipo;
        $mantenimientoTipo->id = $id;
        $respuesta = $mantenimientoTipo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipo de Mantenimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de mantenimiento fue actualizado correctamente' );
            header("Location:" . Route::names('mantenimiento-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipo de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('mantenimiento-tipos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New MantenimientoTipo);

        // Sirve para validar el Token
        if ( !SaveMantenimientoTiposRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('mantenimiento-tipos.index'));
            die();

        }

        $mantenimientoTipo = New MantenimientoTipo;
        $mantenimientoTipo->id = $id;
        $respuesta = $mantenimientoTipo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tipo de Mantenimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de mantenimiento fue eliminado correctamente' );

            header("Location:" . Route::names('mantenimiento-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de mantenimiento no se podrá eliminar ***' );
            header("Location:" . Route::names('mantenimiento-tipos.index'));

        }
        
        die();

    }
}
