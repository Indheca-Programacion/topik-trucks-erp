<?php

namespace App\Controllers;

require_once "app/Models/Presupuesto.php";

require_once "app/Policies/PresupuestoPolicy.php";
require_once "app/Requests/SavePresupuestoRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Presupuesto;
use App\Policies\PresupuestoPolicy;
use App\Requests\SavePresupuestoRequest;
use App\Route;

class PresupuestosController
{
    public function index()
    {
        Autorizacion::authorize('view', new Presupuesto);

        $presupuesto = New Presupuesto;

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/presupuestos/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $presupuesto = new Presupuesto;
        Autorizacion::authorize('create', $presupuesto);

        require_once "app/Models/Maquinaria.php";
        $maquinaria = new \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultar();

        require_once "app/Models/Cliente.php";
        $cliente = new \App\Models\Cliente;
        $clientes = $cliente->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = new \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/ServicioEstatus.php";
        $servicioEstatus = new \App\Models\ServicioEstatus;
        $servicioEstatuss = $servicioEstatus->consultar();

        require_once "app/Models/MantenimientoTipo.php";
        $mantenimientoTipo = new \App\Models\MantenimientoTipo;
        $mantenimientoTipos = $mantenimientoTipo->consultar();

        require_once "app/Models/ServicioCentro.php";
        $servicioCentro = New \App\Models\ServicioCentro;
        $servicioCentros = $servicioCentro->consultar();

        require_once "app/Models/ServicioTipo.php";
        $servicioTipo = New \App\Models\ServicioTipo;
        $servicioTipos = $servicioTipo->consultar();

        require_once "app/Models/ServicioEstatus.php";
        $servicioEstatus = New \App\Models\ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar();

        // $maquinariaTipos = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xmaquinaria_tipos ORDER BY descripcion", $error);
        require_once "app/Models/MaquinariaTipo.php";
        $maquinariaTipo = New \App\Models\MaquinariaTipo;
        $maquinariaTipos = $maquinariaTipo->consultar();

        // $marcas = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xmarcas ORDER BY descripcion", $error);
        require_once "app/Models/Marca.php";
        $marca = New \App\Models\Marca;
        $marcas = $marca->consultar();

        // $modelos = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xmodelos ORDER BY descripcion", $error);
        require_once "app/Models/Modelo.php";
        $modelo = New \App\Models\Modelo;
        $modelos = $modelo->consultar();

        // $colores = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xcolores ORDER BY descripcion", $error);
        require_once "app/Models/Color.php";
        $color = New \App\Models\Color;
        $colores = $color->consultar();

        // $estatus = \App\Conexion::queryAll(CONST_BD_APP, "SELECT * FROM xestatus ORDER BY descripcion", $error);
        require_once "app/Models/Estatus.php";
        $status = New \App\Models\Estatus;
        $estatus = $status->consultar();

        $formularioEditable = true;
        $permitirModificarFechas = true;
        
        $contenido = array('modulo' => 'vistas/modulos/presupuestos/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('update', new Presupuesto);
        
        $request = SavePresupuestoRequest::validated();

        $presupuesto = New Presupuesto;
        $respuesta = $presupuesto->crear($request,$_FILES);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Presupuesto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El presupuesto fue creado correctamente' );
            header("Location:" . Route::names('presupuestos.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Presupuesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('presupuestos.create'));
        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new Presupuesto);

        $presupuesto = New Presupuesto;

        if ( $presupuesto->consultar(null , $id) ) {

            require_once "app/Models/Maquinaria.php";
            $maquinaria = new \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultar();

            require_once "app/Models/Cliente.php";
            $cliente = new \App\Models\Cliente;
            $clientes = $cliente->consultar();

            require_once "app/Models/Usuario.php";
            $usuario = new \App\Models\Usuario;
            $personal = $usuario->consultar();

            $serviciosPresupuesto = $presupuesto->obtenerServiciosPresupuesto($id);

            $totalPresupuesto = array_sum(array_column($serviciosPresupuesto, 'total'));
            
            $contenido = array('modulo' => 'vistas/modulos/presupuestos/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new Presupuesto);

        $request = SavePresupuestoRequest::validated($id);

        $presupuesto = New Presupuesto;
        $presupuesto->id = $id;
        $respuesta = $presupuesto->actualizar($request);
        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Presupuesto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El presupuesto fue actualizado correctamente' );
            header("Location:" . Route::names('presupuestos.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Presupuesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('presupuestos.edit', $id));
        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new Presupuesto);

        // Sirve para validar el Token
        if ( !SavePresupuestoRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Presupuesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('presupuestos.index'));
            die();

        }

        $presupuesto = New Presupuesto;
        // $presupuesto->id = $id;
        $presupuesto->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $presupuesto->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Presupuesto',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El presupuesto fue eliminado correctamente' );

            header("Location:" . Route::names('presupuestos.index'));
        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Presupuesto',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este presupuesto no se podrá eliminar ***' );
            header("Location:" . Route::names('presupuestos.index'));

        }
        
        die();

    }

    public function print($id)
    {
        Autorizacion::authorize('view', new Presupuesto);

        $presupuesto = New Presupuesto;

        if ( $presupuesto->consultar(null , $id) ) {

            require_once "app/Models/Empresa.php";
            $empresa = new \App\Models\Empresa;
            $empresa->consultar(null,7);

            require_once "app/Models/Cliente.php";
            $cliente = new \App\Models\Cliente;
            $cliente->consultar(null,$presupuesto->clienteId);

            $serviciosPresupuesto = $presupuesto->obtenerServiciosPresupuesto($id);

            $totalPresupuesto = array_sum(array_column($serviciosPresupuesto, 'total'));
            
            include "reportes/presupuesto.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
