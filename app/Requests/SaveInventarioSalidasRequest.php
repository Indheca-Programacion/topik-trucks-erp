<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\InventarioSalida;

class SaveInventarioSalidasRequest extends Request
{
    static public function rules($id)
    {

        $rules = [
                    'ordenCompra' => 'required|string',
                    'almacenId' => 'required|integer',
                    'entradaId' => 'required|integer',
                    'inventarioId' => 'required|integer',
                    'observaciones' => 'string',
                    'firma' => 'string',
                    'usuarioRecibioId' => 'required|integer',
                ];

        return $rules;
    }

    static public function messages()
    {
        return [        
            'usuarioRecibioId.required' => 'La persona que entrega es obligatoria.',
            'ordenCompra.required' => 'La orden de compra es obligatoria.',
            'ordenCompra.string' => 'La orden de compra debe ser una cadena de caracteres.',
            'almacenId.required' => 'El almacen es obligatorio.',
            'almacenId.integer' => 'El almacen debe ser de tipo Entero.',
            'almacenId.exists' => 'El almacen que ingreso no existe.',
            'observaciones.string' => 'Las observaciones debe ser una cadena de caracteres.',

        ];
    }

    static public function validated($id = null) {
        return self::validating(InventarioSalida::fillable(), self::rules($id), self::messages());
    }
}
