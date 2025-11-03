<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Alerta;

class SaveAlertasRequest extends Request
{
	static public function rules($id)
    {

        $rules['ubicacion'] = 'required|integer';
        $rules['obra'] = 'required|integer';

        return $rules;
    }

    static public function messages()
    {
        return [
            'usuarios.required' => 'Los usuarios son obligatorios.',
            'ubicacion.required' => 'La ubicacion es obligatoria.',
            'obra.required' => 'La obra es obligatoria.',
            'ubicacion.integer' => 'La ubicacion debe ser de tipo entero.',
            'obra.integer' => 'La obra debe ser de tipo entero.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Alerta::fillable(), self::rules($id), self::messages());
    }
}
