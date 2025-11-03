<?php

namespace App\Controllers;

require_once "app/Models/OrdenCompra.php";
require_once "app/Policies/OrdenCompraPolicy.php";
require_once "app/Requests/SaveOrdenCompraRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\OrdenCompra;
use App\Policies\OrdenCompraPolicy;
use App\Requests\SaveOrdenCompraRequest;
use App\Route;

class OrdenCompraProveedorController
{
    public function index()
    {
        // Autorizacion::authorize('view', new OrdenCompra);

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/orden-compra-proveedor/index.php');

        include "vistas/modulos/plantilla_proveedores.php";
    }

    public function create($id)
    {
        $ordenCompra = new OrdenCompra;
        Autorizacion::authorize('create', $ordenCompra);

        require_once "app/Models/Requisicion.php";
        $requisicion = New \App\Models\Requisicion;
        $requisicion->consultar(null, $id);
        $requisicion->consultarDetalles();

        require_once "app/Models/Estatus.php";
        $estatus = New \App\Models\Estatus;
        $estatuses = $estatus->consultar();

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/Proveedor.php";
        $proveedor = New \App\Models\Proveedor;
        $proveedores = $proveedor->consultar();

        $contenido = array('modulo' => 'vistas/modulos/orden-compra/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function store()
    {

        Autorizacion::authorize('create', New OrdenCompra);

        $request = SaveOrdenCompraRequest::validated();

        if ( !isset($request['detalles']) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Debe capturar al menos una partida de favor intente de nuevo' );
            $requisicionId = $request['requisicionId'];
            header("Location:" . Route::routes('requisiciones.crear-orden-compra', $requisicionId));

            die();

        }
        
        $ordenCompra = New OrdenCompra;
        $respuesta = $ordenCompra->crear($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue creado correctamente' );
            header("Location:" . Route::names('orden-compra.index'));

        } else {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Crear Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );

            header("Location:" . Route::names('requisiciones.crear-orden-compra', $request['requisicionId']));

        }
        
        die();

    }

    public function edit($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $ordenCompra = New OrdenCompra;

        if ( $ordenCompra->consultar(null , $id) ) {

            $ordenCompra->consultarDetalles();

            require_once "app/Models/Requisicion.php";
            $requisicion = New \App\Models\Requisicion;
            $requisicion->consultar(null, $ordenCompra->requisicionId);

            require_once "app/Models/Estatus.php";
            $estatus = New \App\Models\Estatus;
            $estatuses = $estatus->consultar();

            require_once "app/Models/Divisa.php";
            $divisa = New \App\Models\Divisa;
            $divisas = $divisa->consultar();

            require_once "app/Models/Proveedor.php";
            $proveedor = New \App\Models\Proveedor;
            $proveedores = $proveedor->consultar();

            $contenido = array('modulo' => 'vistas/modulos/orden-compra/editar.php');

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

        }

        include "vistas/modulos/plantilla.php";


    }

    public function update($id)
    {
        Autorizacion::authorize('update', new OrdenCompra);

        $request = SaveOrdenCompraRequest::validated($id);

        $ordenCompra = New OrdenCompra;
        $ordenCompra->id = $id;
        $respuesta = $ordenCompra->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue actualizado correctamente' );
            header("Location:" . Route::names('orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('orden-compra.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new OrdenCompra);

        $ordenCompra = New OrdenCompra;
        
        $ordenCompra->consultar(null , $id); // Para tener la ruta de la foto
        $respuesta = $ordenCompra->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'La orden de compra fue eliminado correctamente' );

            header("Location:" . Route::names('orden-compra.index'));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Orden de Compra',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este proveedor no se podrá eliminar ***' );
            header("Location:" . Route::names('orden-compra.index'));

        }
        
        die();

    }

    public function print($id)
    {
        $ordenCompra = New OrdenCompra;
        $ordenCompra->ordenCompraId = $id;
        $ordenDeCompraDatos = $ordenCompra->consultarOrdenDeCompra();    

        if ( $ordenDeCompraDatos ) {

            foreach ($ordenDeCompraDatos as $key => $value) {
            
                require_once "app/Models/Requisicion.php";
                $requisicion = New \App\Models\Requisicion;
                $requisicion->consultar(null, $value["requisicionId"]);
                
                require_once "app/Models/Empresa.php";
                $empresa = New \App\Models\Empresa;
                $empresa->consultar(null, $requisicion->servicio["empresaId"]);
                    
                require_once "app/Models/Divisa.php";
                $divisa = New \App\Models\Divisa;
                $divisa->consultar(null, $value["monedaId"]);

                require_once "app/Models/Proveedor.php";
                $proveedor = New \App\Models\Proveedor;
                $proveedor->consultar(null, $value["proveedorId"]);

                require_once "app/Models/Obra.php";
                $obra = New \App\Models\Obra;
                $obra->consultar(null, $requisicion->servicio["obraId"]);

                $maquinaria = New \App\Models\Maquinaria;
                $maquinaria->consultarMaquinariaPorRequisicion($requisicion->id);

                require_once "app/Models/DatosBancarios.php";
                $datosBancarios = New \App\Models\DatosBancarios;
                $datosBancarios->consultar(null, $value["datoBancarioId"]);

                /********************** USUARIOS *****************************/
                require_once "app/Models/Usuario.php";
                $usuarioElabora = New \App\Models\Usuario;
                $usuarioAprueba = New \App\Models\Usuario;
                $usuarioAutoriza = New \App\Models\Usuario;
                /*****************************************************/

                /********************** USUARIO ELABORA *****************************/
                $usuarioElabora->consultar(null, $value["usuarioIdCreacion"]);

                // NOMBRE COMPLETO USUARIO ELABORA
                $nombreCompletoUsuarioElabora = mb_strtoupper($usuarioElabora->nombre . ' ' . $usuarioElabora->apellidoPaterno);
                if ( !is_null($usuarioElabora->apellidoMaterno) ) $nombreCompletoUsuarioElabora .= ' ' . mb_strtoupper($usuarioElabora->apellidoMaterno);

                // FIRMA USUARIO ELABORA
                $firmaUsuarioElabora = $usuarioElabora->firma;
                /*****************************************************/
                
                /********************** USUARIO APRUEBA *****************************/
                $usuarioAprueba->consultar(null,$value["usuarioIdAprobacion"]);

                // NOMBRE COMPLETO USUARIO APRUEBA
                $nombreCompletoUsuarioAprueba = mb_strtoupper($usuarioAprueba->nombre . ' ' . $usuarioAprueba->apellidoPaterno);
                if ( !is_null($usuarioAprueba->apellidoMaterno) ) $nombreCompletoUsuarioAprueba .= ' ' . mb_strtoupper($usuarioAprueba->apellidoMaterno);

                // FIRMA USUARIO APRUEBA
                $firmaUsuarioAprueba = $usuarioAprueba->firma;
                /*****************************************************/

                /********************** USUARIO AUTORIZA *****************************/
                $usuarioAutoriza->consultar(null, $value["usuarioIdAutorizacion"]);

                // NOMBRE COMPLETO USUARIO ELABORA
                $nombreCompletoUsuarioAutoriza = mb_strtoupper($usuarioAutoriza->nombre . ' ' . $usuarioAutoriza->apellidoPaterno);
                if ( !is_null($usuarioAutoriza->apellidoMaterno) ) $nombreCompletoUsuarioAutoriza .= ' ' . mb_strtoupper($usuarioAutoriza->apellidoMaterno);

                // FIRMA USUARIO ELABORA
                $firmaUsuarioAutoriza = $usuarioAutoriza->firma;
                /*****************************************************/

            
                include "reportes/ordencompra.php";
            }

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }

}

?>