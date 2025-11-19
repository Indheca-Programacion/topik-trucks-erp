<?php

namespace App\Controllers;

require_once "app/Models/MaquinariaTipo.php";
require_once "app/Policies/MaquinariaTipoPolicy.php";
require_once "app/Requests/SaveMaquinariaTiposRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\MaquinariaTipo;
use App\Policies\MaquinariaTipoPolicy;
use App\Requests\SaveMaquinariaTiposRequest;
use App\Route;

class MaquinariaTiposController
{
    public function index()
    {
        Autorizacion::authorize('view', new MaquinariaTipo);

        $maquinariaTipo = New MaquinariaTipo;
        $maquinariaTipos = $maquinariaTipo->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/maquinaria-tipos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $maquinariaTipo = new MaquinariaTipo;
        Autorizacion::authorize('create', $maquinariaTipo);

        $contenido = array('modulo' => 'vistas/modulos/maquinaria-tipos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New MaquinariaTipo);

        $request = SaveMaquinariaTiposRequest::validated();

        $maquinariaTipo = New MaquinariaTipo;
        $respuesta = $maquinariaTipo->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Tipo de Maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de maquinaria fue creado correctamente' );
            header("Location:" . Route::names('maquinaria-tipos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Tipo de Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('maquinaria-tipos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new MaquinariaTipo);

        $maquinariaTipo = New MaquinariaTipo;

        if ( $maquinariaTipo->consultar(null , $id) ) {

            require_once "app/Models/ChecklistSection.php";
            $checklistSection = New \App\Models\ChecklistSection;
            $secciones = $checklistSection->consultar();

            require_once "app/Models/ChecklistTarea.php";
            $checklistTarea = New \App\Models\ChecklistTarea;
            $tareas = $checklistTarea->consultar('maquinariaTipoId', $id);

            $contenido = array('modulo' => 'vistas/modulos/maquinaria-tipos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new MaquinariaTipo);

        $request = SaveMaquinariaTiposRequest::validated($id);

        $maquinariaTipo = New MaquinariaTipo;
        $maquinariaTipo->id = $id;
        $respuesta = $maquinariaTipo->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Tipo de Maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de maquinaria fue actualizado correctamente' );
            header("Location:" . Route::names('maquinaria-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Tipo de Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('maquinaria-tipos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new MaquinariaTipo);

        // Sirve para validar el Token
        if ( !SaveMaquinariaTiposRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('maquinaria-tipos.index'));
            die();

        }

        $maquinariaTipo = New MaquinariaTipo;
        $maquinariaTipo->id = $id;
        $respuesta = $maquinariaTipo->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Tipo de Maquinaria',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El tipo de maquinaria fue eliminado correctamente' );

            header("Location:" . Route::names('maquinaria-tipos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Tipo de Maquinaria',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este tipo de maquinaria no se podrá eliminar ***' );
            header("Location:" . Route::names('maquinaria-tipos.index'));

        }
        
        die();

    }
}
