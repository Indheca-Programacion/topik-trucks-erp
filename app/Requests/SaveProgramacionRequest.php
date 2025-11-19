<?php

namespace App\Requests;

// require_once "app/Requests/Request.php";
if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Programacion;

class SaveProgramacionRequest extends Request
{
    static public function rules($id)
    {
        $rules = [];

        $rules['maquinariaId'] = 'required|exists:'.CONST_BD_APP.'.maquinarias:id';
        $rules['servicioTipoId'] = 'required|exists:'.CONST_BD_APP.'.servicio_tipos:id';
        $rules['horoOdometroUltimo'] = 'required|decimal|minValue:0.0|max:11';
        $rules['cantidadSiguienteServicio'] = 'required|integer|minValue:1|max:7';

        return $rules;
    }

    static public function messages()
    {
        return [
            'maquinariaId.required' => 'El número económico es obligatorio.',
            'maquinariaId.exists' => 'El número económico seleccionado no existe.',
            'servicioTipoId.required' => 'El tipo de servicio es obligatorio.',
            'servicioTipoId.exists' => 'El tipo de servicio seleccionado no existe.',
            'horoOdometroUltimo.required' => 'El campo Servicio Anterior es obligatorio.',
            'horoOdometroUltimo.decimal' => 'El campo Servicio Anterior debe ser de tipo Decimal.',
            'horoOdometroUltimo.minValue' => 'El valor del campo Servicio Anterior no puede ser menor a 0.0',
            'horoOdometroUltimo.max' => 'El campo Servicio Anterior debe ser máximo de 7 dígitos.',
            'cantidadSiguienteServicio.required' => 'El campo Intervalo entre Servicios es obligatorio.',
            'cantidadSiguienteServicio.integer' => 'El campo Intervalo entre Servicios debe ser de tipo Numérico.',
            'cantidadSiguienteServicio.minValue' => 'El valor del campo Intervalo entre Servicios no puede ser menor a 1',
            'cantidadSiguienteServicio.max' => 'El campo Intervalo entre Servicios debe ser máximo de 5 dígitos.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(Programacion::fillable(), self::rules($id), self::messages());
    }
}
