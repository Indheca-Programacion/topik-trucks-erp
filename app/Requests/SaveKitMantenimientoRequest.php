<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\KitMantenimiento;

class SaveKitMantenimientoRequest extends Request
{
    static public function rules($id)
    {

        $rules = [
            'tipoMantenimiento' => 'required|string|max:255',
            'tipoMaquinaria' => 'required|integer|exists:'.CONST_BD_APP.'.maquinaria_tipos:id',
            'modelo' => 'required|integer|exists:'.CONST_BD_APP.'.modelos:id',
            'proveedor' => 'integer|exists:'.CONST_BD_APP.'.proveedores:id',
            'observacion' => 'string|max:255',
            ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'tipoMantenimiento.required' => 'El tipo de mantenimiento es obligatorio',
            'tipoMantenimiento.string' => 'El tipo de mantenimiento debe ser una cadena de texto',
            'tipoMantenimiento.max' => 'El tipo de mantenimiento no debe exceder los 255 caracteres',

            'tipoMaquinaria.required' => 'El tipo de maquinaria es obligatorio',
            'tipoMaquinaria.integer' => 'El tipo de maquinaria debe ser un número entero',
            'tipoMaquinaria.exists' => 'El tipo de maquinaria no existe en la base de datos',

            'modelo.required' => 'El modelo es obligatorio',
            'modelo.integer' => 'El modelo debe ser un número entero',
            'modelo.exists' => 'El modelo no existe en la base de datos',

            'proveedor.integer' => 'El proveedor debe ser un número entero',
            'proveedor.exists' => 'El proveedor no existe en la base de datos',

            'observacion.string' => 'La observación debe ser una cadena de texto',
            'observacion.max' => 'La observación no debe exceder los 255 caracteres',
        ];
    }

    static public function validated($id = null) {
        return self::validating(KitMantenimiento::fillable(), self::rules($id), self::messages());
    }
}
