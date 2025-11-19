<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\OrdenCompra;
use App\Controllers\Autorizacion;

class OrdenCompraPolicy
{
    public function view(Usuario $usuario, OrdenCompra $obj)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompra");
    }

    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompra");
    }

    public function update(Usuario $usuario, OrdenCompra $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompra");
    }

    public function delete(Usuario $usuario, OrdenCompra $obj)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "OrdenCompra");
    }

    public function restore(Usuario $usuario, OrdenCompra $obj)
    {
        //
    }

    public function forceDelete(Usuario $usuario, OrdenCompra $obj)
    {
        //
    }
}
