<?php

namespace App\Controllers;

require_once "app/Models/ConfiguracionPuestoMantenimiento.php";
require_once "app/Models/Puesto.php";
require_once "app/Models/MantenimientoTipo.php";
require_once "app/Policies/ConfiguracionRequisicionPolicy.php";
require_once "app/Requests/SaveConfiguracionRequisicionesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Conexion;
use App\Models\Puesto;
use App\Models\MantenimientoTipo;
use App\Models\ConfiguracionPuestoMantenimiento;
use App\Policies\ConfiguracionRequisicionPolicy;
use App\Requests\SaveConfiguracionRequisicionesRequest;
use App\Route;

class ConfiguracionPuestoMantenimientoController
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
        Autorizacion::authorize('update', New ConfiguracionPuestoMantenimiento);
        $configuracionPuestoManteniento = New ConfiguracionPuestoMantenimiento;
      
        if ($configuracionPuestoManteniento->consultar() ) {

            $mantenimientoTipo = new MantenimientoTipo;
            $mantenimientoTipos = $mantenimientoTipo->consultar();

            $puesto = new Puesto;
            $puestos = $puesto->consultar();
            
            $contenido = array('modulo' => 'vistas/modulos/configuracion-puesto-mantenimiento/editar.php');
            include "vistas/modulos/plantilla.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

    public function update($id)
    {
        // Autorizacion::authorize('update', New ConfiguracionRequisicion);
        
        // $request = SaveConfiguracionRequisicionesRequest::validated();

        // $configuracionRequisicion = New ConfiguracionRequisicion;
        // $configuracionRequisicion->id = $id;
        // $respuesta = $configuracionRequisicion->actualizar($request);

        // if ( $respuesta ) {

        //     $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
        //                                                    'titulo' => 'Actualizar Configuración',
        //                                                    'subTitulo' => 'OK',
        //                                                    'mensaje' => 'La configuración fue actualizada correctamente' );

        //     header("Location:" . Route::routes('configuracion-requisiciones'));

        // } else {

        //     $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
        //                                                    'titulo' => 'Actualizar Configuración',
        //                                                    'subTitulo' => 'Error',
        //                                                    'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
        //     header("Location:" . Route::routes('configuracion-requisiciones'));

        // }
        
        // die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Puesto);

        // Sirve para validar el Token
        if ( !SaveConfiguracionPuestoMantenimientoRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Color',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('colores.index'));
            die();

        }

        $puesto = New Puesto;
        $puesto->id = $id;
        $respuesta = $puesto->eliminarPuestoMantenimiento($id);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Color',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Puesto Mantenimiento fue eliminado correctamente' );
            header("Location:" . Route::names('configuracion-puesto-tipo.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Color',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este color no se podrá eliminar ***' );
            header("Location:" . Route::names('configuracion-puesto-tipos.index'));

        }
        
        die();

    }

}
