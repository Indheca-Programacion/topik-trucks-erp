<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Servicio;

class SaveServiciosRequest extends Request
{    
	static public function rules($id)
    {
        $rules = [];
        if ( self::method() === 'POST' ) {
            $rules = [ 'empresaId' => 'required|exists:'.CONST_BD_SECURITY.'.empresas:id',
                       'servicioCentroId' => 'required|exists:'.CONST_BD_APP.'.servicio_centros:id' ];
        } 

        $rules['maquinariaId'] = 'required|exists:'.CONST_BD_APP.'.maquinarias:id';
        $rules['obraId'] = 'required|exists:'.CONST_BD_APP.'.obras:id';
        $rules['horoOdometro'] = 'required|decimal|minValue:0.1|max:9';
        $rules['mantenimientoTipoId'] = 'required|exists:'.CONST_BD_APP.'.mantenimiento_tipos:id';
        $rules['servicioTipoId'] = 'required|exists:'.CONST_BD_APP.'.servicio_tipos:id';
        $rules['servicioEstatusId'] = 'required|exists:'.CONST_BD_APP.'.servicio_estatus:id';
        $rules['solicitudTipoId'] = 'required|exists:'.CONST_BD_APP.'.solicitud_tipos:id';
        $rules['horasProyectadas'] = 'required|decimal|minValue:0.01|max:8';
        $rules['fechaSolicitud'] = 'required|date';
        $rules['fechaProgramacion'] = 'date';
        $rules['fechaFinalizacion'] = 'required|date';
        $rules['descripcion'] = 'required|string|max:255';

        return $rules;
    }

    static public function messages()
    {
        return [
            'empresaId.required' => 'La empresa es obligatoria.',
            'empresaId.exists' => 'La empresa seleccionada no existe.',
            'servicioCentroId.required' => 'El centro de servicio es obligatorio.',
            'servicioCentroId.exists' => 'El centro de servicio seleccionado no existe.',
            'maquinariaId.required' => 'El número económico es obligatorio.',
            'maquinariaId.exists' => 'El número económico seleccionado no existe.',
            'obraId.required' => 'La obra es obligatoria.',
            'obraId.exists' => 'La obra seleccionada no existe.',
            'horoOdometro.required' => 'El campo Horómetro / Odómetro es obligatorio.',
            'horoOdometro.decimal' => 'El campo Horómetro / Odómetro debe ser de tipo Decimal.',
            'horoOdometro.minValue' => 'El valor del campo Horómetro / Odómetro no puede ser menor a 0.1',
            'horoOdometro.max' => 'El campo Horómetro / Odómetro debe ser máximo de 7 dígitos.',
            'mantenimientoTipoId.required' => 'El tipo de mantenimiento es obligatorio.',
            'mantenimientoTipoId.exists' => 'El tipo de mantenimiento seleccionado no existe.',
            'servicioTipoId.required' => 'El tipo de servicio es obligatorio.',
            'servicioTipoId.exists' => 'El tipo de servicio seleccionado no existe.',
            'servicioEstatusId.required' => 'El estatus es obligatorio.',
            'servicioEstatusId.exists' => 'El estatus seleccionado no existe.',
            'solicitudTipoId.required' => 'El tipo de solicitud es obligatorio.',
            'solicitudTipoId.exists' => 'El tipo de solicitud seleccionado no existe.',
            'horasProyectadas.required' => 'El campo Horas Hombre Proyectadas es obligatorio.',
            'horasProyectadas.decimal' => 'El campo Horas Hombre Proyectadas debe ser de tipo Decimal.',
            'horasProyectadas.minValue' => 'El valor del campo Horas Hombre Proyectadas no puede ser menor a 0.01',
            'horasProyectadas.max' => 'El campo Horas Hombre Proyectadas debe ser máximo de 6 dígitos.',
            'fechaSolicitud.required' => 'La fecha solicitud del servicio es obligatoria.',
            'fechaSolicitud.date' => 'La fecha solicitud del servicio no es válida.',
            'fechaProgramacion.date' => 'La fecha programación del servicio no es válida.',
            'fechaFinalizacion.required' => 'La fecha finalización del servicio es obligatoria.',
            'fechaFinalizacion.date' => 'La fecha finalización del servicio no es válida.',
            'descripcion.required' => 'La descripción del trabajo es obligatoria.',
            'descripcion.string' => 'La descripción del trabajo debe ser de tipo String.',
            'descripcion.max' => 'La descripción del trabajo debe ser máximo de 255 caracteres.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Servicio::fillable(), self::rules($id), self::messages());
    }
}
