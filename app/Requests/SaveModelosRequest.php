<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Modelo;

class SaveModelosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = ['descripcion' => 'required|string|max:60|uniqueFields:'.CONST_BD_APP.'.modelos:marcaId'];
        } else {
            $rules = ['descripcion' => 'required|string|max:60|uniqueFields:'.CONST_BD_APP.'.modelos:marcaId:id:' . $id];
        }

        $rules['marcaId'] = 'required|exists:'.CONST_BD_APP.'.marcas:id';

        return $rules;
    }

    static public function messages()
    {
        return [
            'marcaId.required' => 'La marca es obligatoria.',
            'marcaId.exists' => 'La marca seleccionada no existe.',
            'descripcion.required' => 'La descripcion del Tipo de Maquinaria es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.uniqueFields' => 'Esta descripcion ya ha sido registrada.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Modelo::fillable(), self::rules($id), self::messages());
    }
}
