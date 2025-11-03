<?php

namespace App\Controllers;

require_once "app/Models/Gastos.php";
require_once "app/Requests/SaveGastosRequest.php";
require_once "app/Models/Usuario.php";
require_once "app/Models/Perfil.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/ConfiguracionCorreoElectronico.php";

use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Perfil;
use App\Models\Usuario;
use App\Models\Gastos;
use App\Requests\SaveGastosRequest;
use App\Route;

class GastosController
{
    public function index()
    {
        Autorizacion::authorize('view', New Gastos);

        require_once "app/Models/Obra.php";
        $obras = New \App\Models\Obra;
        $obras = $obras->consultar();

        require_once "app/Models/Empresa.php";
        $empresas = New \App\Models\Empresa;
        $empresas = $empresas->consultar();

        require_once "app/Models/Usuario.php";
        $usuarios = New \App\Models\Usuario;
        $usuarios = $usuarios->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/gastos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $gastos = New Gastos;
        Autorizacion::authorize('create', $gastos);

        require_once "app/Models/Empleado.php";
        $empleado = New \App\Models\Empleado;
        $empleados = $empleado->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();
        
        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuarios = $usuario->consultar();

        $contenido = array('modulo' => 'vistas/modulos/gastos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New Gastos);

        $request = SaveGastosRequest::validated();

        $gastos = New Gastos;
        $respuesta = $gastos->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Gastos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El gasto fue creado correctamente' );
            header("Location:" . Route::names('gastos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('gastos.create'));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', New Gastos);

        $gastos = New Gastos;
        $usuario = New Usuario;
        $usuario->id = usuarioAutenticado()["id"];
        $usuario->consultarPerfiles();
        $usuario->consultarPermisos();

        if ( $gastos->consultar(null , $id) ) {

            $crearRequisicion = false;
            if($usuario->checkPerfil("pagos")){
                $crearRequisicion = true;
            }

            $permitirAutorizar = false;
            if($usuario->checkPermiso("autorizar-gastos")){
                $permitirAutorizar = true;
            }

            $permitirRevisar = false;
            $revisoNombre = "";
            if($usuario->checkPermiso("revisar-gastos")){
                $permitirRevisar = true;
            }
            
            $usuario->consultar(null, $gastos->usuarioIdRevision);
            $revisoNombre = $usuario->nombreCompleto;

            require_once "app/Models/GastoDetalles.php";
            $gastoDetalles = New \App\Models\GastoDetalles;
            $detalles = $gastoDetalles->consultarPorGasto($id);
            
            require_once "app/Models/RequisicionGasto.php";
            $requisiciones = New \App\Models\RequisicionGasto;
            $requisiciones->consultar(null,$gastos->requisicionId);

            $usuarios = $usuario->consultar();

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();    

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            $contenido = array('modulo' => 'vistas/modulos/gastos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', New Gastos);

        $request = SaveGastosRequest::validated($id);

        $gastos = New Gastos;
        $gastos->id = $id;
        
        $respuesta = $gastos->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Gasto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Gasto fue actualizado correctamente' );
            header("Location:" . Route::names('gastos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Gasto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('gastos.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', New Gastos);

        // Sirve para validar el Token
        if ( !SaveGastosRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('gastos.index'));
            die();

        }

        $gastos = New Gastos;
        $gastos->id = $id;
        $respuesta = $gastos->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Gastos',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El Gasto fue eliminado correctamente' );

            header("Location:" . Route::names('gastos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Gastos',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('gastos.index'));

        }
        
        die();
    }

    public function print($id)
    {
        Autorizacion::authorize('view', New Gastos);

        $gastos = New Gastos;

        if ( $gastos->consultar(null , $id) ) {
            
            //TODO: Cambiar por usuarios
            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;

            $usuarioAutorizo = "";
            $firmaAutorizo = "";
            if(!is_null($gastos->usuarioIdAutorizacion)){
                $usuario->consultar(null, $gastos->usuarioIdAutorizacion);
                $usuarioAutorizo = $usuario->nombreCompleto;
                $firmaAutorizo = $usuario->firma;
            }

            $usuario->consultar(null, $gastos->encargado);
            
            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obra->consultar(null, $gastos->obra);

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresa->consultar(null, $gastos->empresa);

            require_once "app/Models/GastoDetalles.php";
            $gastosDetalles = New \App\Models\GastoDetalles;

            $detallesGastos = $gastosDetalles->consultarPorGasto($gastos->id);

            if($gastos->tipoGasto == 1){
                include "reportes/gastos-deducibles.php";
            }else{
                include "reportes/gastos-no-deducibles.php";
            }

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
