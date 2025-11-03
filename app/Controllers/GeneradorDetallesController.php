<?php

namespace App\Controllers;

require_once "app/Models/GeneradorDetalles.php";
require_once "app/Policies/GeneradorDetallesPolicy.php";
require_once "app/Requests/SaveGeneradorDetallesRequest.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\GeneradorDetalles;
use App\Policies\GeneradorDetallesPolicy;
use App\Requests\SaveGeneradorDetallesRequest;
use App\Route;

class GeneradorDetallesController
{

    public function edit($id)
    {
        Autorizacion::authorize('update', new GeneradorDetalles);

        $generadorDetalles = New GeneradorDetalles;

        if ( $generadorDetalles->consultar(null , $id) ) {

            require_once "app/Models/Maquinaria.php";
            $maquinaria = New \App\Models\Maquinaria;
            $maquinarias = $maquinaria->consultarMaquinasGenerador($generadorDetalles->generador);

            $maquinaSelected = $maquinaria->consultar(null,$generadorDetalles->maquinaria);

            $contenido = array('modulo' => 'vistas/modulos/generador-detalles/editar.php');

            include "vistas/modulos/plantilla.php";
        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

    public function update($id)
    {
        Autorizacion::authorize('update', new GeneradorDetalles);

        $request = SaveGeneradorDetallesRequest::validated($id);

        $generadorDetalle = New GeneradorDetalles;
        $generadorDetalle->id = $id;
        

        $respuesta = $generadorDetalle->actualizar($request);

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Actualizar Detalle de Generador',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El detalle del generador fue actualizado correctamente' );
            header("Location:" . Route::names('generadores.edit', $_POST["generadorId"]));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Actualizar Detalle de Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo' );
            
            header("Location:" . Route::names('generador-detalles.edit', $id));

        }
        
        die();
    }

    public function destroy($id)
    {
        
        Autorizacion::authorize('delete', new GeneradorDetalles);

        // Sirve para validar el Token
        if ( !SaveGeneradorDetallesRequest::validatingToken($error) ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => $error );
            header("Location:" . Route::names('generadores.edit',$_POST["generador"]));
            die();

        }

        $generador = New GeneradorDetalles;
        $generador->id = $id;
        $respuesta = $generador->eliminar();

        if ( $respuesta ) {

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-success',
                                                           'titulo' => 'Eliminar Generadores',
                                                           'subTitulo' => 'OK',
                                                           'mensaje' => 'El generador fue eliminado correctamente' );

            header("Location:" . Route::names('generadores.edit',$_POST["generador"]));

        } else {            

            $_SESSION[CONST_SESSION_APP]["flash"] = array( 'clase' => 'bg-danger',
                                                           'titulo' => 'Eliminar Generador',
                                                           'subTitulo' => 'Error',
                                                           'mensaje' => 'Hubo un error al procesar, de favor intente de nuevo. *** Tome en cuenta que si existen registros que hacen referencia a este estatus no se podrá eliminar ***' );
            header("Location:" . Route::names('generadores.edit',$_POST["generador"]));

        }
        
        die();

    }

}
