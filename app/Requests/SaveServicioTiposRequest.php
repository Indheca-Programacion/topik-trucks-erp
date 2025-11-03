<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ServicioTipo;

class SaveServicioTiposRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.servicio_tipos', 
                       'numero' => 'required|integer|max:7|uniqueFields:'.CONST_BD_APP.'.servicio_tipos:unidadId' ];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:60|unique:'.CONST_BD_APP.'.servicio_tipos:id:' . $id, 
                       'numero' => 'required|integer|max:7|uniqueFields:'.CONST_BD_APP.'.servicio_tipos:unidadId:id:' . $id ];
        }

        $rules['unidadId'] = 'required|exists:'.CONST_BD_APP.'.unidades:id';

        return $rules;
    }

    static public function messages()
    {
        return [
            'descripcion.required' => 'La descripcion del Tipo de Servicio es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 60 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'numero.required' => 'El campo número es obligatorio.',
            'numero.integer' => 'El campo número debe ser de tipo Numérico.',
            'numero.max' => 'El campo número debe ser máximo de 7 dígitos.',
            'numero.uniqueFields' => 'Este número ya ha sido registrado.',
            'unidadId.required' => 'La unidad es obligatoria.',
            'unidadId.exists' => 'La unidad seleccionada no existe.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ServicioTipo::fillable(), self::rules($id), self::messages());
    }
}
