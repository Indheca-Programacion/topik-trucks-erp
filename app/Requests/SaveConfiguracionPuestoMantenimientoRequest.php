<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ConfiguracionPuestoMantenimiento;

class SaveConfiguracionPuestoMantenimientoRequest extends Request
{
    static public function rules($id)
    {
        $rules = [];

        $rules['id_puesto'] = 'required';
        $rules['id_tipo_mantenimiento'] = 'required';

        return $rules;
    }

    static public function messages()
    {
        return [
            'id_puesto.required' => 'El puesto es obligatorio.',
            'id_tipo_mantenimiento.exists' => 'El Tipo mantenimiento es requerido.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(ConfiguracionPuestoMantenimiento::fillable(), self::rules($id), self::messages());
    }
}
