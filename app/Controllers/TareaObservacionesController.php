<?php

namespace App\Controllers;

require_once "app/Models/TareaObservaciones.php";
require_once "app/Models/Tarea.php";
require_once "app/Policies/TareaObservacionesPolicy.php";
require_once "app/Requests/SaveTareaObservacionesRequest.php";
require_once "app/Requests/SaveTareaRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\TareaObservaciones;
use App\Models\Tarea;
use App\Policies\TareaObservacionesPolicy;
use App\Requests\SaveTareaObservacionesRequest;
use App\Requests\SaveTareaRequest;
use App\Route;

class TareaObservacionesController
{

    public function store()
    {

        Autorizacion::authorize('create', New TareaObservaciones);

        $request = SaveTareaObservacionesRequest::validated();

        $observacion = New TareaObservaciones;

        $datos = SaveTareaRequest::validated();
        $datos["id"] = $request["fk_tarea"];

        $tarea = new Tarea;
        $res = $tarea->actualizarEstatus($datos);

        $respuesta = $observacion->crear($request);
        
        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tarea',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La tarea fue creada correctamente' );
            header("Location:" . Route::names('tareas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('tareas.edit',$request["fk_tarea"]));

        }
        
        die();

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new TareaObservaciones);

        $request = SaveTareaObservacionesRequest::validated($id);

        $tarea = New TareaObservaciones;
        $tarea->id = $id;
        
        $respuesta = $tarea->actualizar($request);

        var_dump($request);
        die();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Observaciones',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue actualizada correctamente' );
            header("Location:" . Route::names('tareas.edit',$_POST["id"]));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('tareas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Tarea);

        // Sirve para validar el Token
        if ( !SaveTareaRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('tareas.index'));
            die();

        }

        $tarea = New Tarea;
        $tarea->id = $id;
        $respuesta = $tarea->eliminar();

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue eliminada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tarea',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La tarea fue eliminada correctamente' );
            header("Location:" . Route::names('tareas.index'));

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tarea',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***' );
            header("Location:" . Route::names('tareas.index'));

        }
        
        die();

    }
}
