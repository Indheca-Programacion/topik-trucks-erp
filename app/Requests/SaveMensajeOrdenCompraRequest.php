<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
    require_once "app/Models/MensajeOrdenCompra.php";

} else {
    require_once "../Requests/Request.php";
    require_once "../Models/MensajeOrdenCompra.php";
}

use App\Models\MensajeOrdenCompra;

class SaveMensajeOrdenCompraRequest extends Request
{
    static public function rules($id)
    {
        $rules = [ 
                'observaciones' => 'required|max:30', 
                'ordenCompraId' => 'required', 
            ];
     
        return $rules;
    }

    static public function messages()
    {
        return [
            'observaciones.required' => 'El mensaje es obligatorio.',
            'observaciones.max' => 'El mensaje debe ser máximo de 30 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(MensajeOrdenCompra::fillable(), self::rules($id), self::messages());
    }
}
