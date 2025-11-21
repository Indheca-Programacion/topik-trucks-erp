<?php

namespace App\Requests;
if ( file_exists ( "app/Controllers/Validacion.php" ) ) {
    require_once "app/Controllers/Validacion.php";
} else {
    require_once "../Controllers/Validacion.php";
}


use App\Models\MensajeRequisicion;

class SaveMensajeRequest extends Request
{
        static public function rules($id)
        {
            if ( self::method() === 'POST' ) {
                $rules = [ 'mensaje' => 'required|string',
                            'id_requisicion' => 'required|integer'];
            } 
            return $rules;
        }

    static public function messages()
    {
        return [
            'mensaje.string' => 'El mensaje es deseable.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(MensajeRequisicion::fillable(), self::rules($id), self::messages());
    }
}
