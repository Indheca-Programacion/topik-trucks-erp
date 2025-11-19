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

class KitMantenimientoController
{
    public function index()
    {
        Autorizacion::authorize('view', New KitMantenimiento);

        $contenido = array('modulo' => 'vistas/modulos/kit-mantenimiento/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $kitMantenimiento = New KitMantenimiento;
        Autorizacion::authorize('create', $kitMantenimiento);

        require_once "app/Models/MantenimientoTipo.php";
        $mantenimientoTipo = New \App\Models\MantenimientoTipo;
        $tiposMantenimiento = $mantenimientoTipo->consultar();

        require_once "app/Models/MaquinariaTipo.php";
        $maquinariaTipo = New \App\Models\MaquinariaTipo;
        $tiposMaquinaria = $maquinariaTipo->consultar();

        require_once "app/Models/Modelo.php";
        $modelo = New \App\Models\Modelo;
        $modelos = $modelo->consultar();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

        $formularioEditable = true;

        $contenido = array('modulo' => 'vistas/modulos/kit-mantenimiento/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New KitMantenimiento);

        $request = SaveKitMantenimientoRequest::validated();

        $kitMantenimiento = New KitMantenimiento;
        $respuesta = $kitMantenimiento->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Kit de Mantenimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El kit de mantenimiento fue creado correctamente' );
            header("Location:" . Route::names('kit-mantenimiento.edit', $kitMantenimiento->id));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Kit de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('kit-mantenimiento.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New KitMantenimiento);

        $kitMantenimiento = New KitMantenimiento;

        if ( $kitMantenimiento->consultar(null , $id) ) {

            $kitMantenimiento->consultarDetalles();

            require_once "app/Models/MantenimientoTipo.php";
            $mantenimientoTipo = New \App\Models\MantenimientoTipo;
            $tiposMantenimiento = $mantenimientoTipo->consultar();

            require_once "app/Models/MaquinariaTipo.php";
            $maquinariaTipo = New \App\Models\MaquinariaTipo;
            $tiposMaquinaria = $maquinariaTipo->consultar();

            require_once "app/Models/Modelo.php";
            $modelo = New \App\Models\Modelo;
            $modelos = $modelo->consultar();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

            $formularioEditable = false;

            $contenido = array('modulo' => 'vistas/modulos/kit-mantenimiento/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New KitMantenimiento);

        $request = SaveKitMantenimientoRequest::validated($id);

        $kitMantenimiento = New KitMantenimiento;
        $kitMantenimiento->id = $id;
        $respuesta = $kitMantenimiento->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Kit de Mantenimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El kit de mantenimiento fue actualizado correctamente' );
            header("Location:" . Route::names('kit-mantenimiento.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Kit de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('kit-mantenimiento.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New KitMantenimiento);

        // Sirve para validar el Token
        if ( !SaveKitMantenimientoRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Kit de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('kit-mantenimiento.index'));
            die();

        }

        $kitMantenimiento = New KitMantenimiento;
        $kitMantenimiento->id = $id;
        $respuesta = $kitMantenimiento->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Kit de Mantenimiento',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El kit de mantenimiento fue eliminado correctamente' );
            header("Location:" . Route::names('kit-mantenimiento.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Kit de Mantenimiento',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta actividad semanal no se podrá eliminar ***' );
            header("Location:" . Route::names('kit-mantenimiento.index'));

        }
        
        die();

    }
}
