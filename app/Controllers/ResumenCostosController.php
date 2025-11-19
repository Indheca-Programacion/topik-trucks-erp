<?php

namespace App\Controllers;

require_once "app/Models/ResumenCostos.php";
require_once "app/Policies/ResumenCostosPolicy.php";
require_once "app/Requests/SaveResumenCostosRequest.php";
require_once "app/Controllers/Autorizacion.php";

require_once "app/Models/Usuario.php";
require_once "app/Controllers/Autorizacion.php";
require_once "app/Models/Obra.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/Perfil.php";

use App\Models\ResumenCostos;
use App\Policies\ResumenCostosPolicy;
use App\Requests\SaveResumenCostosRequest;
use App\Route;

use App\Models\Obra;
use App\Models\Usuario;
use App\Models\Requisicion;
use App\Models\Perfil;
use DateTime;

class ResumenCostosController
{
    public function index()
    {
        if ( !usuarioAutenticado() ) {
            header("Location:" . Route::routes('ingreso'));
            die();
        }

        // Validar Autorizacion
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "costos-resumen", "ver") ) {

            $contenido = array('modulo' => 'vistas/modulos/errores/403.php');
            include "vistas/modulos/plantilla.php";
            die();

        }

        require_once "app/Models/Divisa.php";
        $divisa = New \App\Models\Divisa;
        $divisas = $divisa->consultar();

        require_once "app/Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        $contenido = array('modulo' => 'vistas/modulos/ResumenCostos/index.php');

        include "vistas/modulos/plantilla.php";
    }


    public function create()
    {
        //
    }

    public function store()
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update($id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
