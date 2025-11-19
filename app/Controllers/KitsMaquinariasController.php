<?php

namespace App\Controllers;

require_once "app/Models/KitMantenimiento.php";
require_once "app/Policies/KitMantenimientoPolicy.php";
require_once "app/Requests/SaveKitMantenimientoRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\KitMantenimiento;
use App\Policies\KitMantenimientoPolicy;
use App\Requests\SaveKitMantenimientoRequest;
use App\Route;

class KitsMaquinariasController
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

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new KitMantenimiento);

        $kit = New KitMantenimiento;
        $respuesta = $kit->eliminarKitMaquinaria($id);

        if ($respuesta) {

            // $_SESSION[CONST_SESSION_APP]["flash"] = "La sucursal fue eliminada correctamente";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Kit de maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El kit de maquinaria fue eliminado correctamente' );
            header("Location:" . Route::names('maquinarias.edit', $_POST["id"]) );

        } else {            

            // $_SESSION[CONST_SESSION_APP]["flash"] = "Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta sucursal no se podrá eliminar ***";
            // $_SESSION[CONST_SESSION_APP]["flashAlertClass"] = "alert-danger";
            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Kit de maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este kit no se podrá eliminar ***' );
            header("Location:" . Route::names('maquinarias.edit', $_POST["id"]));

        }
        
        die();

    }
}
