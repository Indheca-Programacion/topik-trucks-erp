<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\ServicioTipo;
use App\Controllers\Autorizacion;

class ServicioTipoPolicy
{
    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(Usuario $usuario, ServicioTipo $servicioTipo)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-tipos") ;
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-tipos");
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(Usuario $usuario, ServicioTipo $servicioTipo)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-tipos");
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(Usuario $usuario, ServicioTipo $servicioTipo)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-tipos");
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(Usuario $usuario, ServicioTipo $servicioTipo)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function forceDelete(Usuario $usuario, ServicioTipo $servicioTipo)
    {
        //
    }
}
