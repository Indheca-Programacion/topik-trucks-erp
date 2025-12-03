<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\ServicioPartida;

class SaveServicioPartidaRequest extends Request
{
    static public function rules($id)
    {

        $rules = [  'cantidad' => 'required|decimal',
                    'unidad' => 'required|string',
                    'descripcion' => 'required|string',
                    'costo_base' => 'required|decimal',
                    'logistica' => 'required|decimal',
                    'mantenimiento' => 'required|decimal',
                    'utilidad' => 'required|decimal',
                    'presupuestoId' => 'required|integer|exists:presupuestos:id',
                    'servicioId' => 'required|integer|exists:servicios:id'
                 ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'cantidad.required' => 'La cantidad es obligatoria',
            'cantidad.decimal' => 'La cantidad debe ser un número decimal',
            'unidad.required' => 'La unidad es obligatoria',
            'unidad.string' => 'La unidad debe ser una cadena de texto',
            'descripcion.required' => 'La descripción es obligatoria',
            'descripcion.string' => 'La descripción debe ser una cadena de texto',
            'costo_base.required' => 'El costo base es obligatorio',
            'costo_base.decimal' => 'El costo base debe ser un número decimal',
            'logistica.required' => 'El costo de logística es obligatorio',
            'logistica.decimal' => 'El costo de logística debe ser un número decimal',
            'mantenimiento.required' => 'El costo de mantenimiento es obligatorio',
            'mantenimiento.decimal' => 'El costo de mantenimiento debe ser un número decimal',
            'utilidad.required' => 'La utilidad es obligatoria',
            'utilidad.decimal' => 'La utilidad debe ser un número decimal',
            'presupuestoId.required' => 'El ID del presupuesto es obligatorio',
            'presupuestoId.integer' => 'El ID del presupuesto debe ser un número entero',
            'presupuestoId.exists' => 'El ID del presupuesto no existe',
            'servicioId.required' => 'El ID del servicio es obligatorio',
            'servicioId.integer' => 'El ID del servicio debe ser un número entero',
            'servicioId.exists' => 'El ID del servicio no existe'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ServicioPartida::fillable(), self::rules($id), self::messages());
    }
}
