<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ConfiguracionProgramacion;

class SaveConfiguracionProgramacionRequest extends Request
{
    static public function rules($id)
    {
        $rules = [];

        // $rules['servicioTipos'] = 'string';
        $rules['unidadesAbrirServicio'] = 'required|integer|minValue:1|max:3';

        return $rules;
    }

    static public function messages()
    {
        return [
            // 'servicioTipos.required' => 'El tipo de servicio es obligatorio.',
            'unidadesAbrirServicio.required' => 'El campo Unidades para abrir una Orden de Trabajo es obligatorio.',
            'unidadesAbrirServicio.integer' => 'El campo Unidades para abrir una Orden de Trabajo debe ser de tipo Numérico.',
            'unidadesAbrirServicio.minValue' => 'El valor del campo Unidades para abrir una Orden de Trabajo no puede ser menor a 1',
            'unidadesAbrirServicio.max' => 'El campo Unidades para abrir una Orden de Trabajo debe ser máximo de 3 dígitos.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ConfiguracionProgramacion::fillable(), self::rules($id), self::messages());
    }
}
