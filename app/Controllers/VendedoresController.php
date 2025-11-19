<?php

namespace App\Controllers;

require_once "app/Models/Vendedor.php";
require_once "app/Requests/SaveVendedoresRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Vendedor;
use App\Requests\SaveVendedoresRequest;
use App\Route;

class VendedoresController
{
    public function index()
    {

        $contenido = array('modulo' => 'vistas/modulos/vendedores/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function create()
    {
        
        $contenido = array('modulo' => 'vistas/modulos/vendedores/crear.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function store()
    {

        $request = SaveVendedoresRequest::validated();

        $vendedor = New Vendedor;
        $respuesta = $vendedor->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Vendedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El vendedor fue creado correctamente' );
            header("Location:" . Route::names('vendedores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Vendedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('vendedores.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        $vendedor = New Vendedor;
        $vendedor -> id = $id;

        if ( $vendedor->consultar(null , $id) ) {

            $contenido = array('modulo' => 'vistas/modulos/vendedores/editar.php');

            include "vistas/modulos/plantilla_proveedores.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla_proveedores.php";
        }
    }

    public function update($id)
    {
    	$vendedor = New Vendedor;
        $vendedor -> id = $id;
        
        $request = SaveVendedoresRequest::validated();
        
        $respuesta = $vendedor->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Vendedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El vendedor fue actualizado correctamente' );
            header("Location:" . Route::names('vendedores.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Vendedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('vendedores.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {

        // Verifica que el usuario no sea Administrador
        $vendedor = New Vendedor;
        $vendedor -> id = $id;

        $respuesta = $vendedor->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Vendedor',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El vendedor fue eliminado correctamente' );

            header("Location:" . Route::names('vendedores.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Vendedor',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este Vendedor no se podrá eliminar ***' );
            header("Location:" . Route::names('vendedores.index'));

        }
        
        die();
    }

}

