<?php

namespace App\Controllers;

require_once "app/Models/EstatusOrdenCompra.php";
require_once "app/Policies/EstatusOrdenCompraPolicy.php";
require_once "app/Requests/SaveEstatusOrdenCompraRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\EstatusOrdenCompra;
use App\Policies\EstatusOrdenCompraPolicy;
use App\Requests\SaveEstatusOrdenCompraRequest;
use App\Route;

class EstatusOrdenCompraController
{
    public function index()
    {
        Autorizacion::authorize('view', New EstatusOrdenCompra);

        $estatusOrdenCompra = New EstatusOrdenCompra;
        $estatuOrdenCompra = $estatusOrdenCompra->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/estatus-orden-compra/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $estatusOrdenCompra = New EstatusOrdenCompra;
        Autorizacion::authorize('create', $estatusOrdenCompra);

        $contenido = array('modulo' => 'vistas/modulos/estatus-orden-compra/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New EstatusOrdenCompra);

        $request = SaveEstatusOrdenCompraRequest::validated();

        $estatusOrdenCompra = New EstatusOrdenCompra;
        $respuesta = $estatusOrdenCompra->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue creado correctamente' );
            header("Location:" . Route::names('estatus-orden-compra.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('estatus-orden-compra.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New EstatusOrdenCompra);

        $estatusOrdenCompra = New EstatusOrdenCompra;

        if ( $estatusOrdenCompra->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/estatus-orden-compra/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New EstatusOrdenCompra);

        $request = SaveEstatusOrdenCompraRequest::validated($id);

        $estatusOrdenCompra = New EstatusOrdenCompra;
        $estatusOrdenCompra->id = $id;
        $respuesta = $estatusOrdenCompra->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue actualizado correctamente' );
            header("Location:" . Route::names('estatus-orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('estatus-orden-compra.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New EstatusOrdenCompra);

        // Sirve para validar el Token
        if ( !SaveEstatusOrdenCompraRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('estatus-orden-compra.index'));
            die();

        }

        $estatusOrdenCompra = New EstatusOrdenCompra;
        $estatusOrdenCompra->id = $id;
        $respuesta = $estatusOrdenCompra->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El estatus fue eliminado correctamente' );

            header("Location:" . Route::names('estatus-orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Estatus',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('estatus-orden-compra.index'));

        }
        
        die();

    }
}
