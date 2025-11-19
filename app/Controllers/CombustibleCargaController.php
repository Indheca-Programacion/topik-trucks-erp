<?php

namespace App\Controllers;

require_once "app/Models/Combustible.php";
// require_once "app/Policies/CombustiblePolicy.php";
require_once "app/Requests/SaveCombustibleRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Combustible;
// use App\Policies\CombustiblePolicy;
use App\Requests\SaveCombustibleRequest;
use App\Requests\Request;
use App\Route;

class CombustibleCargaController
{
    public function index()
    {
        Autorizacion::authorize('view', New Combustible);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        $contenido = array('modulo' => 'vistas/modulos/combustible-cargas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $combustible = New Combustible;
        Autorizacion::authorize('create', $combustible);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultarActivos();

        require_once "app/Models/EmpleadoFuncion.php";
        $empleadoFuncion = New \App\Models\EmpleadoFuncion;
        $empleadoFuncion->consultar('nombreCorto', 'operador');

        // require_once "app/Models/Maquinaria.php";
        // $maquinaria = New \App\Models\Maquinaria;
        // $maquinarias = $maquinaria->consultar();

        // require_once "app/Models/Ubicacion.php";
        // $ubicacion = New \App\Models\Ubicacion;
        // $ubicaciones = $ubicacion->consultar();

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/combustible-cargas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New Combustible);

        $request = SaveCombustibleRequest::validated();

        // $_POST["fecha"] = fFechaSQL($request["fecha"]);
        // if ( !Validacion::validar("fecha", $_POST["fecha"], ['uniqueFields', CONST_BD_APP.'.combustibles', 'empresaId', 'empleadoId']) ) {

            // $_SESSION[CONST_SESSION_APP]["errors"]['fecha'] = "La fecha de la carga de combustible ya ha sido registrada.";

            // header("Location:" . Route::names('combustible-cargas.create'));
            // die();

        // }

        // if ( !isset($request['detalles']) ) {

        //     $_SESSION[CONST_SESSION_APP]["errors"] = [ 'Debe capturar al menos un consumo.' ];

        //     header("Location:" . Route::names('combustible-cargas.create'));
        //     die();

        // }

        $combustible = New Combustible;
        $respuesta = $combustible->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Carga de Combustible',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El registro fue creado correctamente' );
            // header("Location:" . Route::names('combustible-cargas.index'));
            header("Location:" . Route::names('combustible-cargas.edit', $combustible->id));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Carga de Combustible',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('combustible-cargas.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Combustible);

        $combustible = New Combustible;

        if ( $combustible->consultar(null , $id) ) {

            $combustible->consultarDetalles();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/Empleado.php";
            $empleado = New \App\Models\Empleado;
            $empleados = $empleado->consultar();

            require_once "app/Models/EmpleadoFuncion.php";
            $empleadoFuncion = New \App\Models\EmpleadoFuncion;
            $empleadoFuncion->consultar('nombreCorto', 'operador');

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            $formularioEditable = true;
            // if ( $combustible->estatus["consumoAbierto"] ) $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/combustible-cargas/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Combustible);

        // $request = SaveCombustibleRequest::validated($id);
        $request = Request::value($id);

        if ( !isset($request['detalles']) && !isset($request['partidasEliminadas']) ) {

            $_SESSION[CONST_SESSION_APP]["errors"] = [ 'Debe capturar al menos una carga.' ];

            header("Location:" . Route::names('combustible-cargas.edit', $id));
            die();

        }

        $combustible = New Combustible;
        $combustible->id = $id;
        $respuesta = $combustible->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Carga de Combustible',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El registro fue actualizado correctamente' );
            header("Location:" . Route::names('combustible-cargas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Carga de Combustible',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('combustible-cargas.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Combustible);

        // Sirve para validar el Token
        if ( !SaveCombustibleRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Carga de Combustible',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('combustible-cargas.index'));
            die();

        }

        $combustible = New Combustible;
        $combustible->id = $id;
        $respuesta = $combustible->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Carga de Combustible',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El registro fue eliminado correctamente' );
            header("Location:" . Route::names('combustible-cargas.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Carga de Combustible',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta carga de combustible no se podrá eliminar ***' );
            header("Location:" . Route::names('combustible-cargas.index'));

        }
        
        die();

    }
}
