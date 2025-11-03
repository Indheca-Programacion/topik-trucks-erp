<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Gastos;

class SaveGastosRequest extends Request
{
    static public function rules($id)
    {
        $rules = [
                'tipoGasto' => 'required|integer',
                'encargado' => 'required|integer',
                'fecha_inicio' =>'required|date',
                'fecha_fin' =>'required|date',
                'banco' =>'string',
                'cuenta' =>'max:12',
                'clave' =>'max:20',
                'obra' =>'required|integer',
                'empresa' =>'required|integer',
            ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'empresa.required' => 'La empresa es obligatoria.',
            'obra.required' => 'La obra es obligatoria.',
            'obra.integer' => 'El valor debe ser tipo entero.',
            'tipoGasto.required' => 'El tipo de gasto es obligatorio.',
            'encargado.required' => 'El encargado es obligatorio.',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_fin.required' => 'La fecha de finalizacion es obligatoria.',
            'banco.required' => 'El banco es obligatorio.',
            'cuenta.required' => 'La cuenta es obligatoria.',
            'cuenta.max' => 'La cuenta debe ser máximo de 10 caracteres.',
            'cuenta.min' => 'La cuenta debe ser mínimo de 10 caracteres.',
            'clave.required' => 'La clave es obligatoria.',
            'clave.max' => 'La clave debe ser máximo de 18 caracteres.',
            'clave.min' => 'La clave debe ser mínimo de 18 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Gastos::fillable(), self::rules($id), self::messages());
    }
}
