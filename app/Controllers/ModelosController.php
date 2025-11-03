<?php

namespace App\Controllers;

require_once "app/Models/Modelo.php";
require_once "app/Policies/ModeloPolicy.php";
require_once "app/Requests/SaveModelosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Modelo;
use App\Policies\ModeloPolicy;
use App\Requests\SaveModelosRequest;
use App\Route;

class ModelosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Modelo);

        $modelo = New Modelo;
        $modelos = $modelo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/modelos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $modelo = new Modelo;
        Autorizacion::authorize('create', $modelo);

        require_once "app/Models/Marca.php";
        $marca = New \App\Models\Marca;
        $marcas = $marca->consultar();

        $contenido = array('modulo' => 'vistas/modulos/modelos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Modelo);

        $request = SaveModelosRequest::validated();

        $modelo = New Modelo;
        $respuesta = $modelo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Modelo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El modelo fue creado correctamente' );
            header("Location:" . Route::names('modelos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Modelo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('modelos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Modelo);

        $modelo = New Modelo;

        if ( $modelo->consultar(null , $id) ) {
            require_once "app/Models/Marca.php";
            $marca = New \App\Models\Marca;
            $marcas = $marca->consultar();
        
            $contenido = array('modulo' => 'vistas/modulos/modelos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Modelo);

        $request = SaveModelosRequest::validated($id);

        $modelo = New Modelo;
        $modelo->id = $id;
        $respuesta = $modelo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Modelo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El modelo fue actualizado correctamente' );
            header("Location:" . Route::names('modelos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Modelo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('modelos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Modelo);

        // Sirve para validar el Token
        if ( !SaveModelosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Modelo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('modelos.index'));
            die();

        }

        $modelo = New Modelo;
        $modelo->id = $id;
        $respuesta = $modelo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Modelo',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El modelo fue eliminado correctamente' );

            header("Location:" . Route::names('modelos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Modelo',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este modelo no se podrá eliminar ***' );
            header("Location:" . Route::names('modelos.index'));

        }
        
        die();

    }
}
