<?php

namespace App\Controllers;

require_once "app/Models/Marca.php";
require_once "app/Policies/MarcaPolicy.php";
require_once "app/Requests/SaveMarcasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Marca;
use App\Policies\MarcaPolicy;
use App\Requests\SaveMarcasRequest;
use App\Route;

class MarcasController
{
    public function index()
    {
        Autorizacion::authorize('view', new Marca);

        $marca = New Marca;
        $marcas = $marca->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/marcas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $marca = new Marca;
        Autorizacion::authorize('create', $marca);

        $contenido = array('modulo' => 'vistas/modulos/marcas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Marca);

        $request = SaveMarcasRequest::validated();

        $marca = New Marca;
        $respuesta = $marca->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Marca',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La marca fue creada correctamente' );
            header("Location:" . Route::names('marcas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Marca',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('marcas.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Marca);

        $marca = New Marca;

        if ( $marca->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/marcas/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Marca);

        $request = SaveMarcasRequest::validated($id);

        $marca = New Marca;
        $marca->id = $id;
        $respuesta = $marca->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Marca',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La marca fue actualizada correctamente' );
            header("Location:" . Route::names('marcas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Marca',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('marcas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Marca);

        // Sirve para validar el Token
        if ( !SaveMarcasRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Marca',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('marcas.index'));
            die();

        }

        $marca = New Marca;
        $marca->id = $id;
        $respuesta = $marca->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Marca',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La marca fue eliminada correctamente' );

            header("Location:" . Route::names('marcas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Marca',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta marca no se podrá eliminar ***' );
            header("Location:" . Route::names('marcas.index'));

        }
        
        die();

    }
}
