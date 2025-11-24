<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Cliente;

class SaveClienteRequest extends Request
{
    static public function rules($id)
    {
       $rules = [
            'nombreCompleto' => 'required|string',
            'prefijo' => 'string|max:255',
            'telefono' => 'string|max:50',
            'correo' => 'string|max:255',
            'observaciones' => 'string',
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombreCompleto.required' => 'El nombre completo del Cliente es obligatorio.',
            'nombreCompleto.string' => 'El nombre completo debe ser de tipo String.',
            'prefijo.string' => 'El prefijo debe ser de tipo String.',
            'prefijo.max' => 'El prefijo debe ser máximo de 255 caracteres.',
            'telefono.string' => 'El teléfono debe ser de tipo String.',
            'telefono.max' => 'El teléfono debe ser máximo de 50 caracteres.',
            'correo.string' => 'El correo debe ser de tipo String.',
            'correo.max' => 'El correo debe ser máximo de 255 caracteres.',
            'observaciones.string' => 'Las observaciones deben ser de tipo String.' 
        ];
    }

    static public function validated($id = null) {
        return self::validating(Cliente::fillable(), self::rules($id), self::messages());
    }
}
