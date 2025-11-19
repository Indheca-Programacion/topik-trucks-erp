<?php

namespace App\Controllers;

require_once "app/Models/Inventario.php";
require_once "app/Models/InventarioSalida.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Inventario;
use App\Models\InventarioSalida;
use App\Route;

class InventarioSalidasController
{
    public function print($id)
    {

        $inventarioSalida = New InventarioSalida;


        if ( $inventarioSalida->consultar(null,$id) ) {
            
            $detalles = $inventarioSalida->consultarPartidaSalidaPorId($id);

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacen->consultar(null, $inventarioSalida->inventario);

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;

            // CONSULTAR USUARIO QUE AUTORIZO
            $usuario->consultar(null, $inventarioSalida->usuarioAutorizoId);
            $firmaAutorizo = $usuario->firma;
            unset($usuario);
            // OBTENER USUARIO DE CREACION
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null, $inventarioSalida->usuarioIdCreacion);
            $entregoFirma = $usuario->firma;

            $entrego = mb_strtoupper($usuario->nombreCompleto);
            

            include "reportes/vale-salida.php";

        } else {
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }
    }
}
