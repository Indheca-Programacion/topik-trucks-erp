<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\ChecklistTarea;

class SaveChecklistTareaRequest extends Request
{
    static public function rules($id)
    {
        if ( self::method() === 'POST' ) {
            $rules = [ 'descripcion' => 'required|string|max:50|uniqueKeys:'.CONST_BD_APP.'.tarea_checklist:maquinariaTipoId:'.$_POST["maquinariaTipoId"]];
        } else {
            $rules = [ 'descripcion' => 'required|string|max:50|unique:'.CONST_BD_APP.'.tarea_checklist:id:' . $id];
        }
        $rules["sectionId"] = "required|integer";
        $rules["maquinariaTipoId"] = "required|integer";

        return $rules;
    }

    static public function messages()
    {
        return [
            "descripcion.required" => "La descripcion es obligatoria",
            'descripcion.string' => 'La descripcion debe ser de tipo String.',
            'descripcion.max' => 'La descripcion debe ser máximo de 50 caracteres.',
            'descripcion.unique' => 'Esta descripcion ya ha sido registrada.',
            'sectionId.required' => 'El id de la sección es obligatorio',
            'sectionId.integer' => 'El id de la sección debe ser un número entero',
            'maquinariaTipoId.required' => 'El id del tipo de mantenimiento es obligatorio',
            'maquinariaTipoId.integer' => 'El id del tipo de mantenimiento debe ser un número entero',
            'descripcion.uniqueKeys' => 'La tarea ya ha sido registrado para esta sección',
            
        ];
    }

    static public function validated($id = null) {
        return self::validating(ChecklistTarea::fillable(), self::rules($id), self::messages());
    }
}
