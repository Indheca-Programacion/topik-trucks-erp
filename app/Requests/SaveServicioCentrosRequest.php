<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ServicioCentro;

class SaveServicioCentrosRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.servicio_centros', 
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.servicio_centros',
                       'nomenclaturaOT' => 'required|string|max:5|unique:'.CONST_BD_APP.'.servicio_centros' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:30|unique:'.CONST_BD_APP.'.servicio_centros:id:' . $id, 
                       'nombreCorto' => 'required|string|max:10|unique:'.CONST_BD_APP.'.servicio_centros:id:' . $id,
                       'nomenclaturaOT' => 'required|string|max:5|unique:'.CONST_BD_APP.'.servicio_centros:id:' . $id ];
        }

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del Centro de Servicio es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 30 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto del Centro de Servicio es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 10 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.',
            'nomenclaturaOT.required' => 'La nomenclatura para la Orden de Trabajo es obligatoria.',
            'nomenclaturaOT.string' => 'La nomenclatura debe ser de tipo String.',
            'nomenclaturaOT.max' => 'La nomenclatura debe ser máximo de 5 caracteres.',
            'nomenclaturaOT.unique' => 'Esta nomenclatura ya ha sido registrada.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ServicioCentro::fillable(), self::rules($id), self::messages());
    }
}
