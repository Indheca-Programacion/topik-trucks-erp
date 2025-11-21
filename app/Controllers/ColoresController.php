<?php

namespace App\Controllers;

require_once "app/Models/Color.php";
require_once "app/Policies/ColorPolicy.php";
require_once "app/Requests/SaveColoresRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Color;
use App\Policies\ColorPolicy;
use App\Requests\SaveColoresRequest;
use App\Route;

class ColoresController
{
    public function index()
    {
        Autorizacion::authorize('view', new Color);

        $color = New Color;
        $colores = $color->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/colores/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $color = new Color;
        Autorizacion::authorize('create', $color);

        $contenido = array('modulo' => 'vistas/modulos/colores/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Color);

        $request = SaveColoresRequest::validated();

        $color = New Color;
        $respuesta = $color->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Color',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El color fue creado correctamente' );
            header("Location:" . Route::names('colores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Color',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('colores.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Color);

        $color = New Color;

        if ( $color->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/colores/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Color);

        $request = SaveColoresRequest::validated($id);

        $color = New Color;
        $color->id = $id;
        $respuesta = $color->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Color',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El color fue actualizado correctamente' );
            header("Location:" . Route::names('colores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Color',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('colores.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Color);

        // Sirve para validar el Token
        if ( !SaveColoresRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Color',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('colores.index'));
            die();

        }

        $color = New Color;
        $color->id = $id;
        $respuesta = $color->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Color',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El color fue eliminado correctamente' );

            header("Location:" . Route::names('colores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Color',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este color no se podrá eliminar ***' );
            header("Location:" . Route::names('colores.index'));

        }
        
        die();

    }
}
