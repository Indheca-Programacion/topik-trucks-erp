<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\Traslado;

class SaveTrasladoRequest extends Request
{
	static public function rules($id)
    {
        
        $rules["operador"] = "required|integer";
        $rules["maquinaria"] = "required|integer";
        $rules["empresa"] = "integer";
        $rules["ruta"] = "required|string";
        $rules["fecha"] = "required|date";
        $rules["kmInicial"] = "decimal";
        $rules["kmFinal"] = "decimal";
        $rules["kmRecorrido"] = "decimal";
        $rules["combustibleInicial"] = "decimal";
        $rules["combustibleFinal"] = "decimal";
        $rules["combustibleGastado"] = "decimal";
        $rules["rendimientoTeorico"] = "decimal";
        $rules["rendimientoReal"] = "decimal";
        $rules["deposito"] = "required|decimal";

        return $rules;
    }

    static public function messages()
    {
        return [
            "empresa.integer" => "La empresa debe ser un número entero",
            "operador.required" => "El operador es requerido",
            "operador.integer" => "El operador debe ser un número entero",
            "maquinaria.required" => "La maquinaria es requerida",
            "maquinaria.integer" => "La maquinaria debe ser un número entero",
            "ruta.required" => "La ruta es requerida",
            "ruta.string" => "La ruta debe ser una cadena de texto",
            "fecha.required" => "La fecha es requerida",
            "fecha.date" => "La fecha debe ser una fecha",
            "kmInicial.required" => "El kilometraje inicial es requerido",
            "kmInicial.decimal" => "El kilometraje inicial debe ser un número decimal",
            "kmFinal.required" => "El kilometraje final es requerido",
            "kmFinal.decimal" => "El kilometraje final debe ser un número decimal",
            "kmRecorrido.required" => "El kilometraje recorrido es requerido",
            "kmRecorrido.decimal" => "El kilometraje recorrido debe ser un número decimal",
            "combustibleInicial.required" => "El combustible inicial es requerido",
            "combustibleInicial.decimal" => "El combustible inicial debe ser un número decimal",
            "combustibleFinal.required" => "El combustible final es requerido",
            "combustibleFinal.decimal" => "El combustible final debe ser un número decimal",
            "combustibleGastado.required" => "El combustible gastado es requerido",
            "combustibleGastado.decimal" => "El combustible gastado debe ser un número decimal",
            "rendimientoTeorico.required" => "El rendimiento teórico es requerido",
            "rendimientoTeorico.decimal" => "El rendimiento teórico debe ser un número decimal",
            "rendimientoReal.required" => "El rendimiento real es requerido",
            "rendimientoReal.decimal" => "El rendimiento real debe ser un número decimal",
            "deposito.required" => "El depósito es requerido",
            "deposito.decimal" => "El depósito debe ser un número decimal"


        ];
    }

    static public function validated($id = null) {
        return self::validating(Traslado::fillable(), self::rules($id), self::messages());
    }
}
