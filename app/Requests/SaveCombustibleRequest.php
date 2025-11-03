<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Combustible;

class SaveCombustibleRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];
        if ( self::method() === 'POST' ) {
            $rules = [ 'empresaId' => 'required|exists:'.CONST_BD_SECURITY.'.empresas:id',
                       'empleadoId' => 'required|exists:'.CONST_BD_APP.'.empleados:id',
                       // 'fecha' => 'required|date|uniqueFields:'.CONST_BD_APP.'.combustibles:empresaId:empleadoId'
                       'fecha' => 'required|date',
                       'hora' => 'required' ];
        } 

        // $rules['fecha'] = 'required|date|uniqueFields:'.CONST_BD_APP.'.combustibles:empresaId:empleadoId:id:' . $id;
        // $rules['fecha'] = 'required|date';

        return $rules;
    }

    static public function messages()
    {
        return [
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'empleadoId.required' => 'El empleado es obligatorio.',
            'empleadoId.exists' => 'El empleado seleccionado no existe.',
            'fecha.required' => 'La fecha de la carga de combustible es obligatoria.',
            'fecha.date' => 'La fecha de la carga de combustible no es válida.',
            // 'fecha.uniqueFields' => 'La fecha de la carga de combustible ya ha sido registrada.'
            'hora.required' => 'La hora de la carga de combustible es obligatoria.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Combustible::fillable(), self::rules($id), self::messages());
    }
}
