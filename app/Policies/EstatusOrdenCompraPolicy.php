<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\EstatusOrdenCompra;
use App\Controllers\Autorizacion;

class EstatusOrdenCompraPolicy
{
    public function view(Usuario $usuario, EstatusOrdenCompra $estatusOrdenCompra)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "estatus-orden-compra") ;
    }

    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "estatus-orden-compra");
    }

    public function update(Usuario $usuario, EstatusOrdenCompra $estatusOrdenCompra)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "estatus-orden-compra");
    }

    public function delete(Usuario $usuario, EstatusOrdenCompra $estatusOrdenCompra)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "estatus-orden-compra");
    }

    public function restore(Usuario $usuario, EstatusOrdenCompra $estatusOrdenCompra)
    {
        //
    }

    public function forceDelete(Usuario $usuario, EstatusOrdenCompra $estatusOrdenCompra)
    {
        //
    }
}
