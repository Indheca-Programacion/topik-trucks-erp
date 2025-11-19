<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Actividad;

class SaveActividadesRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];
        if ( self::method() === 'POST' ) {
            $rules = [ 'empresaId' => 'required|exists:'.CONST_BD_SECURITY.'.empresas:id',
                       'empleadoId' => 'required|exists:'.CONST_BD_APP.'.empleados:id' ];
        } 

        $rules['fechaInicial'] = 'required|date';
        $rules['fechaFinal'] = 'required|date';

        return $rules;
    }

    static public function messages()
    {
        return [
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'empleadoId.required' => 'El empleado es obligatorio.',
            'empleadoId.exists' => 'El empleado seleccionado no existe.',
            'fechaInicial.required' => 'La fecha inicial de la actividad semanal es obligatoria.',
            'fechaInicial.date' => 'La fecha inicial de la actividad semanal no es válida.',
            'fechaFinal.required' => 'La fecha final de la actividad semanal es obligatoria.',
            'fechaFinal.date' => 'La fecha final de la actividad semanal no es válida.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Actividad::fillable(), self::rules($id), self::messages());
    }
}
