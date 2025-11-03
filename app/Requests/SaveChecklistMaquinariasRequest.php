<?php

namespace App\Requests;

require_once "app/Requests/Request.php";

use App\Models\ChecklistMaquinaria;

class SaveChecklistMaquinariasRequest extends Request
{
    static public function rules($id)
    {
        $rules['obraId'] = 'required|exists:'.CONST_BD_APP.'.obras:id';
        $rules['ubicacionId'] = 'required|exists:'.CONST_BD_APP.'.ubicaciones:id';
        $rules['maquinariaId'] = 'required|exists:'.CONST_BD_APP.'.maquinarias:id';
        $rules['fecha'] = 'required|date';
        $rules['horometroInicial'] = 'required|decimal|minValue:0.1|max:15';
        $rules['horometroFinal'] = 'decimal|minValue:0.1|max:15';
        $rules['observaciones'] = 'string|max:255';
        $rules['combustibleInicial'] = 'required|integer|minValue:1';
        $rules['combustibleFinal'] = 'required|integer|minValue:1';
        $rules['acMotor'] = 'required|integer|minValue:1';
        $rules['acHidraulico'] = 'integer|minValue:1';
        $rules['acTransmision'] = 'integer|minValue:1';
        $rules['anticongelante'] = 'integer|minValue:1';
        $rules['acMalacatePrinc'] = 'integer|minValue:1';
        $rules['acMalacateAux'] = 'integer|minValue:1';
        

        return $rules;
    }

    static public function messages()
    {
        return [
            'obraId.required' => 'El campo Obra es obligatorio.',
            'obraId.exists' => 'La obra seleccionada no existe.',
            'ubicacionId.required' => 'El campo Ubicación es obligatorio.',
            'ubicacionId.exists' => 'La ubicación seleccionada no existe.',
            'maquinariaId.required' => 'El campo Número Económico es obligatorio.',
            'maquinariaId.exists' => 'El número económico seleccionado no existe.',
            'fecha.required' => 'El campo Fecha es obligatorio.',
            'fecha.date' => 'El campo Fecha debe ser una fecha válida.',
            'horometroInicial.required' => 'El campo Horómetro Inicial es obligatorio.',
            'horometroInicial.decimal' => 'El campo Horómetro Inicial debe ser un número decimal.',
            'horometroInicial.minValue' => 'El campo Horómetro Inicial debe ser mayor que 0.1.',
            'horometroInicial.max' => 'El campo Horómetro Inicial debe ser menor que 9.',
            'horometroFinal.decimal' => 'El campo Horómetro Final debe ser un número decimal.',
            'horometroFinal.minValue' => 'El campo Horómetro Final debe ser mayor que 0.1.',
            'horometroFinal.max' => 'El campo Horómetro Final debe ser menor que 9.',
            'observaciones.string' => 'El campo Observaciones debe ser una cadena de texto.',
            'observaciones.max' => 'El campo Observaciones no puede exceder los 255 caracteres.',
            'combustibleInicial.required' => 'El campo Combustible Inicial es obligatorio.',
            'combustibleInicial.integer' => 'El campo Combustible Inicial debe ser un número entero.',
            'combustibleInicial.minValue' => 'El campo Combustible Inicial debe ser mayor que 0.',
            'combustibleFinal.required' => 'El campo Combustible Final es obligatorio.',
            'combustibleFinal.integer' => 'El campo Combustible Final debe ser un número entero.',
            'combustibleFinal.minValue' => 'El campo Combustible Final debe ser mayor que 0.',
            'acMotor.required' => 'El campo AC Motor es obligatorio.',
            'acMotor.integer' => 'El campo AC Motor debe ser un número entero.',
            'acMotor.minValue' => 'El campo AC Motor debe ser mayor que 0.',


        ];
    }

    static public function validated($id = null) {
        return self::validating(ChecklistMaquinaria::fillable(), self::rules($id), self::messages());
    }
}
