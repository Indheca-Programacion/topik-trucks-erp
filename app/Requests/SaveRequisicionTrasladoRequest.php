<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Requisicion;

class SaveRequisicionesRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];
        if ( self::method() === 'POST' ) {
            $rules = [ 'servicioId' => 'required|exists:'.CONST_BD_APP.'.servicios:id' ];
        }

        if ( isset($_REQUEST['servicioEstatusId']) ) $rules['servicioEstatusId'] = 'required|exists:'.CONST_BD_APP.'.servicio_estatus:id';
        if ( isset($_REQUEST['observacion']) ) $rules['observacion'] = 'required|string|max:100';
        
        return $rules;
    }

    static public function messages()
    {
        return [
            'servicioId.required' => 'El servicio (id) es obligatorio.',
            'servicioId.exists' => 'El servicio (id) proporcionado no existe.',
            'servicioEstatusId.required' => 'El estatus es obligatorio.',
            'servicioEstatusId.exists' => 'El estatus seleccionado no existe.',
            'servicioEstatusId.exists' => 'El estatus seleccionado no existe.',
            'observacion.required' => 'La Observación es obligatoria.',
            'observacion.string' => 'La Observación debe ser de tipo String.',
            'observacion.max' => 'La Observación debe ser máximo de 100 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Requisicion::fillable(), self::rules($id), self::messages());
    }
}
