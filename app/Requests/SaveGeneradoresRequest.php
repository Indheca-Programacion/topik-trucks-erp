<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Generadores;

class SaveGeneradoresRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 'mes' => 'required|string',
                'ubicacionId' => 'required|string',
                'obraId' => 'required|integer',
                'empresaId' => 'required|integer'];
        return $rules;
    }

    static public function messages()
    {
        return [
            'mes.required' => 'El mes es obligatoria.',
            'ubicacionId.required' => 'La ubicacion es obligatoria.',
            'obra.required' => 'La obra es obligatoria',
            'empresaId.required' => 'La empresa es obligatoria'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Generadores::fillable(), self::rules($id), self::messages());
    }
}
