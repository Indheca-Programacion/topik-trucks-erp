<?php

namespace App\Controllers;

require_once "app/Models/Servicio.php";
require_once "app/Models/TrasladoDetalle.php";
require_once "app/Models/Traslado.php";
require_once "app/Policies/TrasladoPolicy.php";
require_once "app/Requests/SaveTrasladoRequest.php";
require_once "app/Controllers/Autorizacion.php";


use App\Models\Servicio;
use App\Models\TrasladoDetalle;
use App\Models\Traslado;
use App\Policies\TrasladoPolicy;
use App\Requests\SaveTrasladoRequest;
use App\Route;

class TrasladosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Traslado);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/traslados/index.php');

        include "vistas/modulos/plantilla.php";
        
    }

    public function create()
    {
        Autorizacion::authorize('create', new Traslado);

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        $contenido = array('modulo' => 'vistas/modulos/traslados/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Traslado);

        $request = SaveTrasladoRequest::validated();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, $request['empresa']);

        require_once "app/Models/ServicioCentro.php";
        $servicioCentro = New \App\Models\ServicioCentro;
        $servicioCentro->consultar(null, 9);

        $datos = [
            "servicio_centros.nomenclaturaOT" => $servicioCentro->nomenclaturaOT,
            "empresas.nomenclaturaOT" => $empresa->nomenclaturaOT,
            "empresaId" => $request['empresa'],
            "servicioCentroId" => 9,
            "fechaSolicitud" => $request['fecha'],
            "fechaProgramacion" => $request['fecha'],
            "horasProyectadas" => 1,
            "maquinariaId" => $request['maquinaria'],
            "ubicacionId" => 44,
            "mantenimientoTipoId" => 7,
            "servicioTipoId" => 10,
            "servicioEstatusId" => 20,
            "solicitudTipoId" => 3,
            "horasProyectadas" => 1,
            "descripcion" => $request['ruta'],
        ];

        $servicio = New Servicio;
        $servicio->crear($datos);

        $traslado = New Traslado;
        $request['servicio'] = $servicio->id;        

        $respuesta = $traslado->crear($request);
        
        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Traslado',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El traslado fue creado correctamente' );
            header("Location:" . Route::names('traslados.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Traslado',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            header("Location:" . Route::names('traslados.create'));

        }
    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Traslado);

        $traslado = New Traslado;
        $traslado->consultar(null,$id);
        $trasladoArchivos = $traslado->consultarArchivos();
        $traslado->consultarRequisiciones();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();

        require_once "app/Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Servicio.php";
        $servicio = New \App\Models\Servicio;
        $servicio->consultar(null, $traslado->servicio);

        $contenido = array('modulo' => 'vistas/modulos/traslados/editar.php');

        include "vistas/modulos/plantilla.php";
    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Traslado);

        $request = SaveTrasladoRequest::validated();

        $traslado = New Traslado;
        $traslado->id = $id;
        $respuesta = $traslado->actualizar($request);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Traslado',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El traslado fue actualizado correctamente' );
            header("Location:" . Route::names('traslados.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Traslado',
                                                           'subTitulo' => 'ERROR',
                                                           'mensaje' => 'El traslado no fue actualizado' );
            header("Location:" . Route::names('traslados.edit', $id));

        }
    }

    public function destroy($id)
    {
        Autorizacion::authorize('delete', new Traslado);

        $traslado = New Traslado;

        $respuesta = $traslado->eliminar($id);

        if ($respuesta) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Traslado',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El traslado fue eliminado correctamente' );
            header("Location:" . Route::names('traslados.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Traslado',
                                                           'subTitulo' => 'ERROR',
                                                           'mensaje' => 'El traslado no fue eliminado' );
            header("Location:" . Route::names('traslados.index'));

        }

    }

    public function print($id)
    {
        Autorizacion::authorize('view', new Traslado);

        $traslado = New Traslado;
        
        if ( $traslado->consultar(null,$id) ) {

            $traslado->consultarRequisiciones();

            $trasladoDetalles = new TrasladoDetalle;
            $trasladoDetalles->traslado = $id;
            $detalles = $trasladoDetalles->consultarPorTraslado();

            $deducibles = [];
            $nodeducibles = [];

            foreach ($detalles as $detalle) {
                if ($detalle['gasto'] == 1) {
                    $deducibles[] = $detalle;
                } elseif ($detalle['gasto'] == 2) {
                    $nodeducibles[] = $detalle;
                }
            }

            require_once "app/Models/Empleado.php";
            $operador = New \App\Models\Empleado;
            $operador->consultar(null, $traslado->operador);

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinaria->consultar(null, $traslado->maquinaria);
            
            $servicio = New Servicio;
            $servicio->consultar(null, $traslado->servicio);

            $servicio->folio = mb_strtoupper($servicio->folio);
            $traslado->requisicionFolio = isset($traslado->requisiciones[0]["folio"]) ? mb_strtoupper($traslado->requisiciones[0]["folio"]) : $servicio->folio;

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $servicio->empresaId);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $traslado->usuarioIdCreacion);

            $realiza = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
            if ( !is_null($usuario->apellidoMaterno) ) $realiza .= ' ' . $usuario->apellidoMaterno;
            $realizaFirma = $usuario->firma;
            unset($usuario);

            include 'reportes/traslado.php';

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }
    }

}
