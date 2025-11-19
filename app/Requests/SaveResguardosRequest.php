<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Resguardo;

class SaveResguardosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 
                        'fechaEntrego' => 'required|date',
                        'almacenId' => 'required|integer',
                    ];
        }
        $rules["usuarioRecibioId"] = 'required|integer';
        $rules["observaciones"] = 'string';
        
        return $rules;
    }

    static public function messages()
    {
        return [
            'almacenId.required' => 'El almacen es obligatoria.',
            'almacenId.integer' => 'El almacen debe de ser valor entero.',
            'almacenId.exists' => 'El almacen seleccionada no existe.',
            'fechaEntrego.required' => 'La fecha es obligatoria.',
            'fechaEntrego.date' => 'La fecha no es valida.',
            'usuarioRecibioId.required' => 'El usuario es obligatorio.',
            'usuarioRecibioId.integer' => 'El usuario debe de ser valor entero.',
            'usuarioRecibioId.exists' => 'El usuario seleccionado no existe.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Resguardo::fillable(), self::rules($id), self::messages());
    }
}
