<?php

namespace App\Controllers;

require_once "app/Models/GeneradorObservaciones.php";
require_once "app/Policies/GeneradorObservacionesPolicy.php";
require_once "app/Requests/SaveGeneradorObservacionesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\GeneradorObservaciones;
use App\Policies\GeneradorObservacionesPolicy;
use App\Requests\SaveGeneradorObservacionesRequest;
use App\Route;

class GeneradorObservacionesController
{
    public function store(){
        
        Autorizacion::authorize('create', new GeneradorObservaciones);
        $observaciones = new GeneradorObservaciones;

        $request = SaveGeneradorObservacionesRequest::validated();

        $respuesta = $observaciones->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Generador',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El generador fue creado correctamente' );
            header("Location:" . Route::names('generadores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Generadores',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('generadores.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new GeneradorObservaciones);

        $generadorObservacion = New GeneradorObservaciones;

        if ( $generadorObservacion->consultar(null , $id) ) {

            require_once "app/Models/GeneradorDetalles.php";
            $generadorDetalle = New \App\Models\GeneradorDetalles;
            $maquinarias = $generadorDetalle->consultarDetalles($generadorObservacion->generadorId); 

            $contenido = array('modulo' => 'vistas/modulos/generador-observaciones/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new GeneradorObservaciones);

        $request = SaveGeneradorObservacionesRequest::validated($id);

        $generadorObservacion = New GeneradorObservaciones;
        $generadorObservacion->id = $id;
        $respuesta = $generadorObservacion->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Observacion',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La observacion fue actualizada correctamente' );
            header("Location:" . Route::names('generadores.edit', $_POST["generador"]));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Observacion',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('generador-observaciones.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new GeneradorObservaciones);

        // Sirve para validar el Token
        if ( !SaveGeneradorObservacionesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('generadores.edit',$_POST["generador"]));
            die();

        }

        $generador = New GeneradorObservaciones;
        $generador->id = $id;
        $respuesta = $generador->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Generadores',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El generador fue eliminado correctamente' );

            header("Location:" . Route::names('generadores.edit',$_POST["generador"]));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('generadores.edit',$_POST["generador"]));

        }
        
        die();

    }

}
