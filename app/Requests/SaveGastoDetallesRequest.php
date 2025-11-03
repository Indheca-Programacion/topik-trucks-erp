<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\GastoDetalles;

class SaveGastoDetallesRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 'fecha' => 'required|date',
                'tipoGasto' => 'required|integer',
                'maquinaria' => 'required|integer',
                'ubicacion' => 'required|integer',
                'obra' => 'required|integer',
                'numeroParte' => 'required|string',
                'costo' =>'required|minValue:0|decimal',
                'cantidad' =>'required|minValue:1|decimal',
                'unidad' =>'required|string',
                'proveedor' =>'required|string',
                'factura' =>'required|string',
                'observaciones' =>'required|string',
                'solicito' =>'required|string',
            ];

        return $rules;
    }

    static public function messages()
    {
        return [
            'obra.required' => 'La obra es obligatoria.',
            'solicito.required' => 'La persona que solicito es obligatoria.',
            'fecha.required' => 'La fecha de inicio es obligatoria.',
            'fecha.date' => 'La fecha debe ser tipo fecha.',
            'tipoGasto.required' => 'El tipo de gasto es obligatorio.',
            'maquinaria.required' => 'El numero economico es obligatorio.',
            'ubicacion.required' => 'La ubicacion es obligatoria.',
            'numeroParte.required' => 'El numero de parte es obligatorio.',
            'costo.required' => 'El costo es obligatorio.',
            'cantidad.required' => 'La cantidad es obligatorio.',
            'cantidad.minValue' => 'La cantidad debe ser minimo 1.',
            'unidad.required' => 'La unidad es obligatoria.',
            'proveedor.required' => 'El proveedor es obligatorio.',
            'factura.required' => 'La factura es obligatoria.',
            'observaciones.required' => 'Las observaciones son obligatorias.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(GastoDetalles::fillable(), self::rules($id), self::messages());
    }
}
