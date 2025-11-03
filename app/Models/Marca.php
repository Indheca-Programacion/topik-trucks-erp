<?php

namespace App\Models;

if ( file_exists ( "app/Policies/MarcaPolicy.php" ) ) {
    require_once "app/Policies/MarcaPolicy.php";
} else {
    require_once "../Policies/MarcaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\MarcaPolicy;

class Marca extends MarcaPolicy
{
    static protected $fillable = [
        'descripcion'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "marcas";

    protected $keyName = "id";

    public $id = null;
    public $descripcion;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MARCAS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName ORDER BY descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion) VALUES (:descripcion)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

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
