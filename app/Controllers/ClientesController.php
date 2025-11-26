<?php

namespace App\Controllers;

require_once "app/Models/Cliente.php";
require_once "app/Policies/ClientePolicy.php";
require_once "app/Requests/SaveClienteRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Cliente;
use App\Policies\ClientePolicy;
use App\Requests\SaveClienteRequest;
use App\Route;

class ClientesController
{
    public function index()
    {
        Autorizacion::authorize('view', new Cliente);
        $cliente = New Cliente;
        $clientes = $cliente->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/clientes/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $cliente = new Cliente;
        Autorizacion::authorize('create', $cliente);

        $contenido = array('modulo' => 'vistas/modulos/clientes/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Cliente);

        $request = SaveClienteRequest::validated();

        $cliente = New Cliente;
        $respuesta = $cliente->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Cliente',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El cliente fue creado correctamente' );
            header("Location:" . Route::names('clientes.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Cliente',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('clientes.create'));
        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Cliente);

        $cliente = New Cliente;

        if ( $cliente->consultar(null , $id) ) {
            $contenido = array('modulo' => 'vistas/modulos/clientes/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Cliente);

        $request = SaveClienteRequest::validated($id);

        $cliente = New Cliente;
        $cliente->id = $id;
        $respuesta = $cliente->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Cliente',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El cliente fue actualizado correctamente' );
            header("Location:" . Route::names('clientes.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Cliente',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('clientes.edit', $id));
        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Cliente);

        // Sirve para validar el Token
        if ( !SaveClienteRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Cliente',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('clientes.index'));
            die();

        }

        $cliente = New Cliente;
        $cliente->id = $id;
        $respuesta = $cliente->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Cliente',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El cliente fue eliminado correctamente' );

            header("Location:" . Route::names('clientes.index'));
        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Cliente',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este cliente no se podrá eliminar ***' );
            header("Location:" . Route::names('clientes.index'));

        }
        
        die();

    }
}
