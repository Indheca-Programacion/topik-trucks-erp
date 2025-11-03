<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\GeneradorDetalles;

class SaveGeneradorDetallesRequest extends Request
{
    static public function rules($id=null)
    {
        $rules = [ 'fechaInicio' => 'required',
                'fk_generador' => 'required|integer',
                'fk_maquinaria' => 'required|integer',
                'generadorId' => 'required|integer'];

        return $rules;
    }

    static public function messages()
    {
        return [
            'fk_generador.required' => 'El generador es obligatorio.',
            'fechaInicio.required' => 'La fecha es obligatoria.',
            'fk_maquinaria.required' => 'La maquinaria es obligatoria'
        ];
    }

    static public function validated($id = null) {
        return self::validating(GeneradorDetalles::fillable(), self::rules($id), self::messages());
    }
}
