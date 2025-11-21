<?php

namespace App\Requests;


if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/Estatus.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/Estatus.php";
}
use App\Models\Estatus;
use App\Models\Obra;

class SaveObrasRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'empresaId' => 'required|exists:'.CONST_BD_SECURITY.'.empresas:id',
                       'descripcion' => 'required|string|max:120|unique:'.CONST_BD_APP.'.obras', 
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.obras',
                       'almacen' => 'required'];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:120|unique:'.CONST_BD_APP.'.obras:id:' . $id, 
                       'nombreCorto' => 'required|string|max:20|unique:'.CONST_BD_APP.'.obras:id:' . $id ];
        }

        $rules['estatusId'] = 'required';
        $rules['periodos'] = 'required|integer|max:2';
        $rules['fechaInicio'] = 'required|date';

        return $rules;
    }

    static public function messages()
    {
        return [            
            'semanaExtra.required' => 'La semana es obligatoria',
            'semanaExtra.minValue' => 'El valor minimo es 1',
            'semanaExtra.integer' => 'La semana debe de ser valor entero',
            'prefijo.required' => 'El prefijo es obligatiorio.',
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'descripcion.required' => 'La descripcion de la Obra es obligatoria.',
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 120 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'nombreCorto.required' => 'El nombre corto de la Obra es obligatorio.',
            'nombreCorto.string' => 'El nombre corto debe ser de tipo String.',
            'nombreCorto.max' => 'El nombre corto debe ser máximo de 20 caracteres.',
            'nombreCorto.unique' => 'Este nombre corto ya ha sido registrado.',
            'estatusId.required' => 'El estatus es obligatorio.',
            'estatusId.exists' => 'El estatus seleccionado no existe.',
            'periodos.required' => 'El campo períodos es obligatorio.',
            'periodos.integer' => 'El campo períodos debe ser de tipo Numérico.',
            'periodos.max' => 'El campo períodos debe ser máximo de 2 dígitos.',
            'fechaInicio.required' => 'La fecha de inicio de la obra es obligatoria.',
            'fechaInicio.date' => 'La fecha de inicio de la obra no es válida.',
            'fechaFinalizacion.required' => 'La fecha de finalización de la obra es obligatoria.',
            'fechaFinalizacion.date' => 'La fecha de finalización de la obra no es válida.',
            'almacen.required' => 'El almacén es obligatorio.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Obra::fillable(), self::rules($id), self::messages());
    }
}
