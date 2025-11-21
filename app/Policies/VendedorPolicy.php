<?php

namespace App\Policies;

use App\Models\Vendedor;
use App\Controllers\Autorizacion;

class VendedorPolicy
{
    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(Usuario $usuario, Vendedor $vendedor)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vendedores");
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vendedores");
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(Usuario $usuario, Vendedor $vendedor)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vendedores");
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(Usuario $usuario, Vendedor $vendedor)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vendedores");
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(Usuario $usuario, Vendedor $vendedor)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vendedores");
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function forceDelete(Usuario $usuario, Vendedor $vendedor)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "vendedores");
    }
}