<?php

namespace App\Requests;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

use App\Models\Empresa;

class SaveEmpresasSesionRequest extends Request
{
	static public function rules($id)
    {
        $rules = [];
        $rules['email'] = 'required|string';
        $rules['password'] = 'required|string';
        return $rules;
    }

    static public function messages()
    {
        
        return [
            'email.required' => 'El correo es obligatorio.',
            'email.string' => 'El correo debe ser de tipo String.',
            'password.required' => 'La contraseña es obligatoria',
            'password.string' => 'La contraseña correo debe ser de tipo String.',
        ];
    }

    static public function validated($id = null) {
        return self::validating(Empresa::fillable(), self::rules($id), self::messages());
    }
}
