<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Puesto;

class SavePuestosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 
                       'nombre' => 'required|string|max:50|unique:'.CONST_BD_APP.'.puesto' ];
        } else {
            $rules = [ 
                       'nombre' => 'required|string|max:50|unique:'.CONST_BD_APP.'.puesto:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'nombre.required' => 'El nombre del Puesto es obligatorio.',
            'nombre.string' => 'El nombre debe ser de tipo String.',
            'nombre.max' => 'El nombre debe ser máximo de 50 caracteres.',
            'nombre.unique' => 'Este nombre ya ha sido registrado.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Puesto::fillable(), self::rules($id), self::messages());
    }
}
