<?php

namespace App\Controllers;

require_once "app/Models/Actividad.php";
require_once "app/Policies/ActividadPolicy.php";
require_once "app/Requests/SaveActividadesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Actividad;
use App\Policies\ActividadPolicy;
use App\Requests\SaveActividadesRequest;
use App\Route;

class ActividadesController
{
    public function index()
    {
        Autorizacion::authorize('view', New Actividad);

        // $actividad = New Actividad;
        // $actividades = $actividad->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        $contenido = array('modulo' => 'vistas/modulos/actividad-semanal/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $actividad = New Actividad;
        Autorizacion::authorize('create', $actividad);

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultarActivos();

        require_once "app/Models/EmpleadoFuncion.php";
        $empleadoFuncion = New \App\Models\EmpleadoFuncion;
        $empleadoFuncion->consultar('nombreCorto', 'tecnico');

        // require_once "app/Models/Servicio.php";
        // $servicio = New \App\Models\Servicio;
        // $servicios = $servicio->consultarAbiertos();

        $formularioEditable = true;
        $contenido = array('modulo' => 'vistas/modulos/actividad-semanal/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {    
        Autorizacion::authorize('create', New Actividad);

        $request = SaveActividadesRequest::validated();

        if ( !isset($request['detalles']) ) {

            $_SESSION[CONST_SESSION_APP]["errors"] = [ 'Debe capturar al menos una actividad.' ];

            header("Location:" . Route::names('actividad-semanal.create'));
            die();

        }

        $actividad = New Actividad;
        $respuesta = $actividad->crear($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Actividad Semanal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La actividad semanal fue creada correctamente' );
            header("Location:" . Route::names('actividad-semanal.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Actividad Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('actividad-semanal.create'));

        }
        
        die();
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Actividad);

        $actividad = New Actividad;

        if ( $actividad->consultar(null , $id) ) {

            $actividad->consultarDetalles();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/Empleado.php";
            $empleado = New \App\Models\Empleado;
            $empleados = $empleado->consultar();

            require_once "app/Models/Servicio.php";
            $servicio = New \App\Models\Servicio;
            // $servicios = $servicio->consultarAbiertos();
            $servicios = $servicio->consultarAbiertos($actividad->empresaId);

            $formularioEditable = true;
            // if ( $actividad->estatus["actividadAbierta"] ) $formularioEditable = true;

            $contenido = array('modulo' => 'vistas/modulos/actividad-semanal/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Actividad);

        $request = SaveActividadesRequest::validated($id);

        if ( !isset($request['detalles']) ) {

            $_SESSION[CONST_SESSION_APP]["errors"] = [ 'Debe capturar al menos una actividad.' ];

            header("Location:" . Route::names('actividad-semanal.edit', $id));
            die();

        }

        $actividad = New Actividad;
        $actividad->id = $id;
        $respuesta = $actividad->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Actividad Semanal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La actividad semanal fue actualizada correctamente' );
            header("Location:" . Route::names('actividad-semanal.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Actividad Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('actividad-semanal.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', New Actividad);

        // Sirve para validar el Token
        if ( !SaveActividadesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Actividad Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );

            header("Location:" . Route::names('actividad-semanal.index'));
            die();

        }

        $actividad = New Actividad;
        $actividad->id = $id;
        $respuesta = $actividad->eliminar();

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Actividad Semanal',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La actividad semanal fue eliminada correctamente' );
            header("Location:" . Route::names('actividad-semanal.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Actividad Semanal',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a esta actividad semanal no se podrá eliminar ***' );
            header("Location:" . Route::names('actividad-semanal.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Actividad);

        $actividad = New Actividad;

        if ( $actividad->consultar(null , $id) ) {

            $actividad->consultarDetalles();

            $detalleFechas = array();

            // $fechaInicial = strtotime($actividad->fechaInicial);
            // $fechaFinal = strtotime($actividad->fechaFinal);
            $fechaInicial = new \DateTime($actividad->fechaInicial);
            $fechaFinal = new \DateTime($actividad->fechaFinal);

            while ( $fechaInicial <= $fechaFinal) {

                $arrayActividades = array();
                foreach($actividad->detalles as $key => $detalle) {
                    if ( $detalle['fecha'] == $fechaInicial->format('Y-m-d') ) array_push($arrayActividades, $detalle);
                }

                $fechas = array( 'fecha' => $fechaInicial->format('Y-m-d'),
                                 'detalles' => $arrayActividades );

                array_push($detalleFechas, $fechas);

                $fechaInicial->add(new \DateInterval('P1D'));

            }

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $actividad->empresaId);

            require_once "app/Models/Empleado.php";
            $empleado = New \App\Models\Empleado;
            $empleado->consultar(null, $actividad->empleadoId);

            $empleadoNombre = $empleado->nombre . ' ' . $empleado->apellidoPaterno;
            if ( !is_null($empleado->apellidoMaterno) ) $empleadoNombre .= ' ' . $empleado->apellidoMaterno;
            
            include "reportes/actividad.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }
}
