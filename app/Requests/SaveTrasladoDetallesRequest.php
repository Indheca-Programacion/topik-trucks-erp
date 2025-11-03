<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\TrasladoDetalle;

class SaveTrasladoDetallesRequest extends Request
{
	static public function rules($id)
    {
        $rules = [
            "traslado" => "required|integer",
            "gasto" => "required|integer",
            "proveedor" => "string",
            "folio" => "string",
            "total" => "required|decimal",
            "descripcion" => "required|string",
        ];

        return $rules;
    }

    static public function messages()
    {
        return [
            "traslado.required" => "El traslado es requerido",
            "traslado.integer" => "El traslado debe ser un número entero",
            "gasto.required" => "El gasto es requerido",
            "gasto.integer" => "El gasto debe ser un número entero",
            "proveedor.string" => "El proveedor debe ser una cadena de texto",
            "folio.string" => "El folio debe ser una cadena de texto",
            "total.required" => "El total es requerido",
            "total.decimal" => "El total debe ser un número decimal",
            "descripcion.required" => "La descripción es requerida",
            "descripcion.string" => "La descripción debe ser una cadena de texto",
        ];
    }

    static public function validated($id = null) {
        return self::validating(TrasladoDetalle::fillable(), self::rules($id), self::messages());
    }
}
