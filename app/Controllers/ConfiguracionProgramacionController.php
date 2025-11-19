<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionProgramacion.php";
require_once "app/Policies/ConfiguracionProgramacionPolicy.php";
require_once "app/Requests/SaveConfiguracionProgramacionRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Conexion;
use App\Models\ConfiguracionProgramacion;
use App\Policies\ConfiguracionProgramacionPolicy;
use App\Requests\SaveConfiguracionProgramacionRequest;
use App\Route;

class ConfiguracionProgramacionController
{
    public function index()
    {
    }

    public function create()
    {
    }

    public function store()
    {
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New ConfiguracionProgramacion);

        $configuracionProgramacion = New ConfiguracionProgramacion;

        if ( $configuracionProgramacion->consultar(null , $id) ) {
            require_once "app/Models/ServicioTipo.php";
            $servicioTipo = New \App\Models\ServicioTipo;
            $servicioTipos = $servicioTipo->consultar();

            $contenido = array('modulo' => 'vistas/modulos/configuracion-programacion/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        Autorizacion::authorize('update', New ConfiguracionProgramacion);
        
        $request = SaveConfiguracionProgramacionRequest::validated();
        // $request = SaveConfiguracionProgramacionRequest::value();

        $configuracionProgramacion = New ConfiguracionProgramacion;
        $configuracionProgramacion->id = $id;
        $respuesta = $configuracionProgramacion->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Configuración',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La configuración fue actualizada correctamente' );

            header("Location:" . Route::routes('configuracion-programacion'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Configuración',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::routes('configuracion-programacion'));

        }
        
        die();
    }

    public function destroy($id)
    {
    }
}
