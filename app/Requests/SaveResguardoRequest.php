<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/Resguardo.php";

    
} else {
    require_once "../Requests/Request.php";
    require_once "../Models/Resguardo.php";

}

use App\Models\Resguardo;

class SaveResguardoRequest extends Request
{
    static public function rules($id)
    {

        $rules = [
                    'observaciones' => 'string',
                    'usuarioRecibioId' => 'required|string',
                    'usuarioEntregoId' => 'required|string',
                    'salidaId' => 'required|string',
                    'firma' => 'required|string',
                    'fecha' => 'required|string',

                ];

        return $rules;
    }

    static public function messages()
    {
        return [        
            'usuarioRecibioId.required' => 'La persona que entrega es obligatoria.',
            'observaciones.string' => 'Las observaciones debe ser una cadena de caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Resguardo::fillable(), self::rules($id), self::messages());
    }
}
