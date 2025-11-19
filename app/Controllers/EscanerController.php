<?php

namespace App\Controllers;

require_once "app/Models/Maquinaria.php";
require_once "app/Policies/MaquinariaPolicy.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Maquinaria;
use App\Policies\MaquinariaPolicy;
use App\Route;

class EscanerController
{
    public function index()
    {
        Autorizacion::authorize('view', new Empresa);

        $empresa = New Empresa;
        $empresas = $empresa->consultar();

        // include "vistas/modulos/empresas/index.php";

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/empresas/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
        $empresa = new Empresa;
        Autorizacion::authorize('create', $empresa);

        // include "vistas/modulos/empresas/crear.php";
        $contenido = array('modulo' => 'vistas/modulos/empresas/crear.php');

        include "vistas/modulos/plantilla.php";
    }

    public function edit($id)
    {
        Autorizacion::authorize('scan', new Maquinaria);

        $maquinaria = New Maquinaria;

        if ( $maquinaria->consultar(null , $id) ) {

            require_once "app/Models/Empresa.php";
            $empresa = New \App\Models\Empresa;
            $empresas = $empresa->consultar();

            require_once "app/Models/MaquinariaTipo.php";
            $maquinariaTipo = New \App\Models\MaquinariaTipo;
            $maquinariaTipos = $maquinariaTipo->consultar();

            require_once "app/Models/Marca.php";
            $marca = New \App\Models\Marca;
            $marcas = $marca->consultar();

            require_once "app/Models/Modelo.php";
            $modelo = New \App\Models\Modelo;
            $modelos = $modelo->consultar();

            require_once "app/Models/Color.php";
            $color = New \App\Models\Color;
            $colores = $color->consultar();

            require_once "app/Models/Estatus.php";
            $status = New \App\Models\Estatus;
            $estatus = $status->consultar();

            require_once "app/Models/Ubicacion.php";
            $ubicacion = New \App\Models\Ubicacion;
            $ubicaciones = $ubicacion->consultar();

            require_once "app/Models/Almacen.php";
            $almacen = New \App\Models\Almacen;
            $almacenes = $almacen->consultar();

            require_once "app/Models/Obra.php";
            $obra = New \App\Models\Obra;
            $obras = $obra->consultar();

            require_once "app/Models/Usuario.php";
            $usuario = New \App\Models\Usuario;
            $usuario->consultar(null,usuarioAutenticado()["id"]);
            $usuario->consultarPermisos();
            
            $contenido = array('modulo' => 'vistas/modulos/escaner/index.php');

            include "vistas/modulos/plantilla.php";
        } else {
            // include "vistas/modulos/errores/404.php";
            $contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            include "vistas/modulos/plantilla.php";
        }

    }

}