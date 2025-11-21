<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\SolicitudTipo;

class SaveSolicitudTiposRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.solicitud_tipos',
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.solicitud_tipos' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.solicitud_tipos:id:' . $id,
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.solicitud_tipos:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del Tipo de Solicitud es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 30 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto del Tipo de Solicitud es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 10 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(SolicitudTipo::fillable(), self::rules($id), self::messages());
    }
}
