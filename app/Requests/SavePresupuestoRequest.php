<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Presupuesto;

class SavePresupuestoRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            'maquinariaId' => 'required|integer|exists:'.CONST_BD_SECURITY.'.maquinarias:id',
            'clienteId' => 'required|integer|exists:'.CONST_BD_SECURITY.'.clientes:id',
            'fuente' => 'required|string|max:255'
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'maquinariaId.required' => 'La maquinaria es obligatoria.',
            'maquinariaId.integer' => 'El valor de la maquinaria debe ser un número entero.',
            'maquinariaId.exists' => 'La maquinaria seleccionada no existe.',

            'clienteId.required' => 'El cliente es obligatorio.',
            'clienteId.integer' => 'El valor del cliente debe ser un número entero.',
            'clienteId.exists' => 'El cliente seleccionado no existe.',

            'fuente.required' => 'La fuente es obligatoria.',
            'fuente.string' => 'El valor de la fuente debe ser una cadena de texto.',
            'fuente.max' => 'El valor de la fuente no debe exceder los 255 caracteres.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Presupuesto::fillable(), self::rules($id), self::messages());
    }
}