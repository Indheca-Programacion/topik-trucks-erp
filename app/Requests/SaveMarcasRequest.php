<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Marca;

class SaveMarcasRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = ['descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.marcas'];
        } else {
            $rules = ['descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.marcas:id:' . $id];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion de la Marca es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Marca::fillable(), self::rules($id), self::messages());
    }
}
