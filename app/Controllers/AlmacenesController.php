<?php

namespace App\Controllers;

require_once "app/Models/Almacen.php";
require_once "app/Policies/AlmacenPolicy.php";
require_once "app/Requests/SaveAlmacenesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Almacen;
use App\Policies\AlmacenPolicy;
use App\Requests\SaveAlmacenesRequest;
use App\Route;

class AlmacenesController
{
    public function index()
    {
        Autorizacion::authorize('view', new Almacen);

        $almacen = New Almacen;
        $almacenes = $almacen->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/almacenes/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $almacen = new Almacen;
        Autorizacion::authorize('create', $almacen);

        $contenido = array('modulo' => 'vistas/modulos/almacenes/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Almacen);

        $request = SaveAlmacenesRequest::validated();

        $almacen = New Almacen;
        $respuesta = $almacen->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Almacén',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El almacén fue creado correctamente' );
            header("Location:" . Route::names('almacenes.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Almacén',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('almacenes.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Almacen);

        $almacen = New Almacen;

        if ( $almacen->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/almacenes/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Almacen);

        $request = SaveAlmacenesRequest::validated($id);

        $almacen = New Almacen;
        $almacen->id = $id;
        $respuesta = $almacen->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Almacén',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El almacén fue actualizado correctamente' );
            header("Location:" . Route::names('almacenes.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Almacén',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('almacenes.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Almacen);

        // Sirve para validar el Token
        if ( !SaveAlmacenesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Almacén',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('almacenes.index'));
            die();

        }

        $almacen = New Almacen;
        $almacen->id = $id;
        $respuesta = $almacen->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Almacén',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El almacén fue eliminado correctamente' );

            header("Location:" . Route::names('almacenes.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Almacén',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este almacén no se podrá eliminar ***' );
            header("Location:" . Route::names('almacenes.index'));

        }
        
        die();

    }
}
