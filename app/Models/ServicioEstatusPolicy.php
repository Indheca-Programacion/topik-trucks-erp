<?php

namespace App\Policies;

use App\Models\Usuario;
use App\Models\ServicioEstatus;
use App\Controllers\Autorizacion;

class ServicioEstatusPolicy
{
    /**
     * Determine whether the user can view the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function view(Usuario $usuario, ServicioEstatus $servicioEstatus)
    {        
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-estatus") ;
    }

    /**
     * Determine whether the user can create roles.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(Usuario $usuario)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-estatus");
    }

    /**
     * Determine whether the user can update the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function update(Usuario $usuario, ServicioEstatus $servicioEstatus)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-estatus");
    }

    /**
     * Determine whether the user can delete the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function delete(Usuario $usuario, ServicioEstatus $servicioEstatus)
    {
        return Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicio-estatus");
    }

    /**
     * Determine whether the user can restore the role.
     *
     * @param  \App\User  $user
     * @param  \App\Role  $role
     * @return mixed
     */
    public function restore(Usuario $usuario, ServicioEstatus $servicioEstatus)
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
    public function forceDelete(Usuario $usuario, ServicioEstatus $servicioEstatus)
    {
        //
    }
}
