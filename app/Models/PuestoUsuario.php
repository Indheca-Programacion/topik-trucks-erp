<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PuestoPolicy.php" ) ) {
    require_once "app/Policies/PuestoPolicy.php";
} else {
    require_once "../Policies/PuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PuestoPolicy;

class PuestoUsuario extends PuestoPolicy
{
    static protected $fillable = [
        'id_puesto', 'id_usuario','id_zona'
    ];

    static protected $type = [
        'id_puesto' => 'integer',
        'id_usuario' => 'integer',
        'id_zona' => 'string'

    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "puesto_usuario";

    protected $keyName = "id";

    public $id_puesto = null;    
    public $id_usuario = null;    
    public $zona;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PUESTOS
    =============================================*/

    public function crearPuestoUsuario($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["id_puesto"] = self::$type["id_puesto"];
        $arrayPDOParam["id_usuario"] = self::$type["id_usuario"];
        $arrayPDOParam["zona"] = self::$type["zona"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO puesto_usuario (id_puesto,id_usuario,zona) VALUES (:id_puesto, :id_usuario, :zona)", $datos, $arrayPDOParam, $error);

    }

}
