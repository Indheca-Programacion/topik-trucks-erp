<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Maquinaria;

class SaveMaquinariasRequest extends Request
{    
	static public function rules($id)
    {
        // $rules = [];
        if ( self::method() === 'POST' ) {
            $rules = [ 'numeroEconomico' => 'required|string|max:30|unique:'.CONST_BD_APP.'.maquinarias' ];
        } else {
            $rules = [ 'numeroEconomico' => 'required|string|max:30|unique:'.CONST_BD_APP.'.maquinarias:id:' . $id ];
        }

        $rules['empresaId'] = 'required|exists:'.CONST_BD_SECURITY.'.empresas:id';
        // $rules['numeroEconomico'] = 'required|string|max:30';
        $rules['numeroFactura'] = 'required|string|max:20';
        $rules['maquinariaTipoId'] = 'required|exists:'.CONST_BD_APP.'.maquinaria_tipos:id';
        $rules['modeloId'] = 'required|exists:'.CONST_BD_APP.'.modelos:id';
        $rules['year'] = 'required|integer|min:4|max:4';
        $rules['descripcion'] = 'required|string|max:80';
        $rules['serie'] = 'required|string|max:30';
        $rules['colorId'] = 'exists:'.CONST_BD_APP.'.colores:id';
        $rules['estatusId'] = 'required|exists:'.CONST_BD_APP.'.estatus:id';
        $rules['ubicacionId'] = 'required|exists:'.CONST_BD_APP.'.ubicaciones:id';
        $rules['almacenId'] = 'required|exists:'.CONST_BD_APP.'.almacenes:id';
        $rules['obraId'] = 'required|exists:'.CONST_BD_APP.'.obras:id';
        $rules['observaciones'] = 'string';
        $rules['fugas'] = 'string';
        $rules['transmision'] = 'string';
        $rules['sistema'] = 'string';
        $rules['motor'] = 'string';

        return $rules;
    }

    static public function messages()
    {
        return [
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'numeroEconomico.required' => 'El número económico de la Maquinaria es obligatorio.',
            'numeroEconomico.string' => 'El número económico debe ser de tipo String.',
            'numeroEconomico.max' => 'El número económico debe ser máximo de 30 caracteres.',
            'numeroEconomico.unique' => 'Este número económico ya ha sido registrado.',
            'numeroFactura.required' => 'El número de factura  es obligatorio.',
            'numeroFactura.string' => 'El número de factura debe ser de tipo String.',
            'numeroFactura.max' => 'El número de factura debe ser máximo de 20 caracteres.',
            'maquinariaTipoId.required' => 'El tipo de maquinaria es obligatorio.',
            'maquinariaTipoId.exists' => 'El tipo de maquinaria seleccionado no existe.',
            'modeloId.required' => 'El modelo es obligatorio.',
            'modeloId.exists' => 'El modelo seleccionado no existe.',
            'year.required' => 'El año es obligatorio.',
            'year.integer' => 'El año debe ser de tipo Numérico.',
            'year.min' => 'El año debe ser mínimo de 4 dígitos.',
            'year.max' => 'El año debe ser máximo de 4 dígitos.',
            'descripcion.required' => 'La descripcion es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 80 caracteres.',
            'serie.required' => 'La serie es obligatoria.',
            'serie.string' => 'La serie debe ser de tipo String.',
            'serie.max' => 'La serie debe ser máximo de 30 caracteres.',
            'colorId.exists' => 'El color seleccionado no existe.',
            'estatusId.required' => 'El estatus es obligatorio.',
            'estatusId.exists' => 'El estatus seleccionado no existe.',
            'ubicacionId.required' => 'La ubicación es obligatoria.',
            'ubicacionId.exists' => 'La ubicación seleccionada no existe.',
            'almacenId.required' => 'El almacén es obligatorio.',
            'almacenId.exists' => 'El almacén seleccionado no existe.',
            'obraId.required' => 'La obra es obligatoria.',
            'obraId.exists' => 'La obra seleccionada no existe.',
            'observaciones.string' => 'El campo observaciones debe ser de tipo String.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Maquinaria::fillable(), self::rules($id), self::messages());
    }
}
