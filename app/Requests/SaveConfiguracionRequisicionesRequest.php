<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ConfiguracionRequisicion;

class SaveConfiguracionRequisicionesRequest extends Request
{
    static public function rules($id)
    {
        $rules = [];

        $rules['inicialServicioEstatusId'] = 'required|exists:'.CONST_BD_APP.'.servicio_estatus:id';
        $rules['usuarioCreacionEliminarPartidas'] = 'value:on';

        return $rules;
    }

    static public function messages()
    {
        return [
            'inicialServicioEstatusId.required' => 'El estatus inicial es obligatorio.',
            'inicialServicioEstatusId.exists' => 'El estatus inicial seleccionado no existe.',
            'usuarioCreacionEliminarPartidas.value' => 'Selección inválida para el campo Eliminar partidas.'
        ];
    }

    static public function validated($id = null) {
        return self::validating(ConfiguracionRequisicion::fillable(), self::rules($id), self::messages());
    }
}
