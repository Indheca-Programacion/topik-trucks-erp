<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/PuestoUsuario.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/PuestoUsuario.php";
}

use App\Models\Cotizacion;

class SaveCotizacionesRequest extends Request
{
	static public function rules($id)
    {   
        $rules = [
            'fechaLimite' => 'required|datetime', 
            'proveedorId' => 'required|string|exists:proveedores:id',
            'vendedorId' => 'integer|exists:proveedor_vendedores:id',

        ];
        return $rules;
    }

    static public function messages()
    {
        return [
            'fechaLimite.required' => 'La fecha límite es obligatoria.',
            'fechaLimite.datetime' => 'La fecha límite no tiene el formato correcto.',
            'proveedorId.required' => 'El proveedor es obligatorio.',
            'proveedorId.string' => 'El proveedor debe ser una cadena de texto.',
            'proveedorId.exists' => 'El proveedor no existe en la base de datos.',
            'vendedorId.required' => 'El vendedor es obligatorio.',
            'vendedorId.string' => 'El vendedor debe ser una cadena de texto.',
            'vendedorId.exists' => 'El vendedor no existe en la base de datos.',
        ];
    }

    static public function validated($id = null) {        
        return self::validating(Cotizacion::fillable(), self::rules($id), self::messages());
    }
}
