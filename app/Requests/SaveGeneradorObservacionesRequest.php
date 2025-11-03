<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\GeneradorObservaciones;

class SaveGeneradorObservacionesRequest extends Request
{
    static public function rules($id=null)
    {
        $rules = [ 'fecha_inicio' => 'required|date',
                'fecha_fin' => 'required|date',
                'observaciones' => 'required|string',
                'generadorDetalle' => 'required|integer'];

        return $rules;
    }

    static public function messages()
    {
        return [
            'fecha_inicio.required' => 'La fecha es requerida',
            'fecha_fin.required' => 'La fecha es requerida',
            'observaciones.required' => 'La fecha es requerida',
            'generadorDetalle.required' => 'La fecha es requerida',
        ];
    }

    static public function validated($id = null) {
        return self::validating(GeneradorObservaciones::fillable(), self::rules($id), self::messages());
    }
}
