<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ComprobacionGasto;

class SaveComprobacionGastoRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];

        $rules['servicioEstatusId'] = 'required|exists:'.CONST_BD_APP.'.servicio_estatus:id';
        $rules['empresaId'] = 'required|exists:'.CONST_BD_APP.'.empresas:id';
        $rules['maquinariaId'] = 'required|exists:'.CONST_BD_APP.'.maquinarias:id';
        $rules['monto'] = 'required|decimal|min:0';
        $rules['justificacion'] = 'required|string|max:255'; // Esta campo tiene ese length porque en BD tiene tinytext
        $rules['obraId'] = 'required|exists:'.CONST_BD_APP.'.obras:id';
        $rules['fechaRequerida'] = 'required|date';

        return $rules;
    }

    static public function messages()
    {
        return [
            'estatusId.required' => 'El estatus es obligatorio.',
            'estatusId.exists' => 'El estatus seleccionado no es válido.',
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no es válida.',
            'maquinariaId.required' => 'La maquinaria es obligatoria.',
            'maquinariaId.exists' => 'La maquinaria seleccionada no es válida.',
            'monto.required' => 'El monto es obligatorio.',
            'monto.decimal' => 'El monto debe ser un número decimal válido.',
            'monto.min' => 'El monto debe ser mayor o igual a 0.',
            'justificacion.required' => 'La justificación es obligatoria.',
            'justificacion.string' => 'La justificación debe ser una cadena de texto válida.',
            'justificacion.max' => 'La justificación no debe exceder los 500 caracteres.',
            'obraId.required' => 'La obra es obligatoria.',
            'obraId.exists' => 'La obra seleccionada no es válida.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ComprobacionGasto::fillable(), self::rules($id), self::messages());
    }
}
