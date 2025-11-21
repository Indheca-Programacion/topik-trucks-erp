<?php

namespace App\Models;

if ( file_exists ( "app/Policies/UbicacionPolicy.php" ) ) {
    require_once "app/Policies/UbicacionPolicy.php";
} else {
    require_once "../Policies/UbicacionPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\UbicacionPolicy;

class Ubicacion extends UbicacionPolicy
{
    static protected $fillable = [
        'descripcion', 'nombreCorto'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "ubicaciones";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR UBICACIONES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT U.* FROM $this->tableName U ORDER BY U.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
            }

            return $respuesta;

        }

    }

    public function consultarCoordinadorUbicacion($item) {


            // CAMBIAR SI BOORRAN EL PUESTO

            $respuesta = Conexion::queryAll($this->bdName, 
            "SELECT PU.id_usuario as id_coordinador
            FROM usuarios US 
            INNER JOIN puesto_usuario PU ON US.ubicacionId = PU.id_zona 
            INNER JOIN usuarios U on PU.id_usuario = U.id WHERE US.id = $item AND PU.id_puesto = 19;", $error);
            $id_coordinador = $respuesta[0]['id_coordinador'] ?? null;

            return $id_coordinador;
    }

    public function consultarManagerUbicacion($item) {

        // CAMBIAR SI BOORRAN EL PUESTO

        $respuesta = Conexion::queryAll($this->bdName, 
        "SELECT PU.id_usuario as id_manager
        FROM usuarios US 
        INNER JOIN puesto_usuario PU ON US.ubicacionId = PU.id_zona 
        INNER JOIN usuarios U on PU.id_usuario = U.id WHERE US.id = $item AND PU.id_puesto = 18", $error);
        $id_manager = $respuesta[0]['id_manager'] ?? null;


        return $id_manager;


}


    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto) VALUES (:descripcion, :nombreCorto)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
