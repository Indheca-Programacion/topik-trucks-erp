<?php

namespace App\Controllers;

require_once "app/Models/Usuario.php";
require_once "app/Controllers/Autorizacion.php";

use App\Models\Usuario;
use App\Route;

class ProgramacionController
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

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "combust-rendimiento", "ver") ) {

            header("Location:" . Route::routes('inicio'));
            die();

        }

        require_once "app/Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "app/Models/Obra.php";
        $obra = new \App\Models\Obra;
        $obras = $obra->consultar();

        // Buscar permiso para Actualizar Programación
        $permitirActualizarProgramacion = false;
        if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "programacion", "actualizar") ) $permitirActualizarProgramacion = true;

        $contenido = array('modulo' => 'vistas/modulos/programacion/index.php');

        include "vistas/modulos/plantilla.php";
    }

    public function create()
    {
    }

    public function store()
    {    
    }

    public function edit($id)
    {
    }

    public function update($id)
    {
    }

    public function destroy($id)
    {
    }
}
