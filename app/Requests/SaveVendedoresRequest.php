<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Vendedor;

class SaveVendedoresRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 'nombreCompleto' => 'required|string|max:255',
                    'correo' => 'required|email|max:100',
                    'telefono' => 'required|string|max:100',
                    'zona' => 'required|string|max:100'
                    ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombreCompleto.required' => 'El nombre completo es obligatorio.',
            'nombreCompleto.string' => 'El nombre completo debe ser una cadena de texto.',
            'nombreCompleto.max' => 'El nombre completo no debe exceder los 255 caracteres.',

            'correo.required' => 'El correo electrónico es obligatorio.',
            'correo.email' => 'El correo electrónico debe tener un formato válido.',
            'correo.max' => 'El correo electrónico no debe exceder los 100 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser una cadena de texto.',
            'telefono.max' => 'El teléfono no debe exceder los 100 caracteres.',

            'zona.required' => 'La zona es obligatoria.',
            'zona.string' => 'La zona debe ser una cadena de texto.',
            'zona.max' => 'La zona no debe exceder los 100 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Vendedor::fillable(), self::rules($id), self::messages());
    }
}
