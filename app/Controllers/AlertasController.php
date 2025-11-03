<?php

namespace App\Controllers;

require_once "app/Models/Alerta.php";
require_once "app/Policies/AlertaPolicy.php";
require_once "app/Requests/SaveAlertasRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Alerta;
use App\Policies\AlertaPolicy;
use App\Requests\SaveAlertasRequest;
use App\Route;

class AlertasController
{
    public function index()
    {
        Autorizacion::authorize('view', New Alerta);

        $contenido = array('modulo' => 'vistas/modulos/alertas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $alerta = New Alerta;
        Autorizacion::authorize('create', $alerta);

        require_once "app/Models/Ubicacion.php";
        $ubicacion = New \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/alertas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New Alerta);

        $request = SaveAlertasRequest::validated();

        $alerta = New Alerta;
        $respuesta = $alerta->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Alerta Semanal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La alerta semanal fue creada correctamente' );
            header("Location:" . Route::names('alertas.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Alerta Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('alertas.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Alerta);

        $alerta = New Alerta;

        if ( $alerta->consultar(null , $id) ) {

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuarios = $usuario->consultar();

            $contenido = array('modulo' => 'vistas/modulos/alertas/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Alerta);

        $request = SaveAlertasRequest::validated($id);


        $alerta = New Alerta;
        $alerta->id = $id;
        $respuesta = $alerta->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Alerta Semanal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La alerta semanal fue actualizada correctamente' );
            header("Location:" . Route::names('alertas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Alerta Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('alertas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Alerta);

        // Sirve para validar el Token
        if ( !SaveAlertasRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Alerta Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('alertas.index'));
            die();

        }

        $alerta = New Alerta;
        $alerta->id = $id;
        $respuesta = $alerta->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Alerta Semanal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La alerta semanal fue eliminada correctamente' );
            header("Location:" . Route::names('alertas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Alerta Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta actividad semanal no se podrá eliminar ***' );
            header("Location:" . Route::names('alertas.index'));

        }
        
        die();

    }
}
