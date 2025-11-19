<?php

namespace App\Controllers;

require_once "app/Models/ServicioCentro.php";
require_once "app/Policies/ServicioCentroPolicy.php";
require_once "app/Requests/SaveServicioCentrosRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\ServicioCentro;
use App\Policies\ServicioCentroPolicy;
use App\Requests\SaveServicioCentrosRequest;
use App\Route;

class ServicioCentrosController
{
    public function index()
    {
        Autorizacion::authorize('view', New ServicioCentro);

        $servicioCentro = New ServicioCentro;
        $servicioCentros = $servicioCentro->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/servicio-centros/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $servicioCentro = New ServicioCentro;
        Autorizacion::authorize('create', $servicioCentro);

        $contenido = array('modulo' => 'vistas/modulos/servicio-centros/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New ServicioCentro);

        $request = SaveServicioCentrosRequest::validated();

        $servicioCentro = New ServicioCentro;
        $respuesta = $servicioCentro->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Centro de Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El centro de servicio fue creado correctamente' );
            header("Location:" . Route::names('servicio-centros.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Centro de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('servicio-centros.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ServicioCentro);

        $servicioCentro = New ServicioCentro;

        if ( $servicioCentro->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/servicio-centros/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ServicioCentro);

        $request = SaveServicioCentrosRequest::validated($id);

        $servicioCentro = New ServicioCentro;
        $servicioCentro->id = $id;
        $respuesta = $servicioCentro->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Centro de Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El centro de servicio fue actualizado correctamente' );
            header("Location:" . Route::names('servicio-centros.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Centro de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('servicio-centros.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New ServicioCentro);

        // Sirve para validar el Token
        if ( !SaveServicioCentrosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Centro de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('servicio-centros.index'));
            die();

        }

        $servicioCentro = New ServicioCentro;
        $servicioCentro->id = $id;
        $respuesta = $servicioCentro->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Centro de Servicio',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El centro de servicio fue eliminado correctamente' );

            header("Location:" . Route::names('servicio-centros.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Centro de Servicio',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este centro de servicio no se podrá eliminar ***' );
            header("Location:" . Route::names('servicio-centros.index'));

        }
        
        die();

    }
}
