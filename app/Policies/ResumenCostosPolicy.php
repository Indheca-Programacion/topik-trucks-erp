<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\ResumenCostos;
use App\Controllers\Autorizacion;

class ResumenCostosPolicy
{
    public function view(Usuario $usuario, ResumenCostos $obj)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "ResumenCostos");
    }

    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "ResumenCostos");
    }

    public function update(Usuario $usuario, ResumenCostos $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "ResumenCostos");
    }

    public function delete(Usuario $usuario, ResumenCostos $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "ResumenCostos");
    }

    public function restore(Usuario $usuario, ResumenCostos $obj)
    {
        //
    }

    public function forceDelete(Usuario $usuario, ResumenCostos $obj)
    {
        //
    }
}
