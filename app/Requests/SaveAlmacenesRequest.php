<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Almacen;

class SaveAlmacenesRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.almacenes', 
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.almacenes' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.almacenes:id:' . $id,
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.almacenes:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del Almacén es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto del Almacén es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Almacen::fillable(), self::rules($id), self::messages());
    }
}
