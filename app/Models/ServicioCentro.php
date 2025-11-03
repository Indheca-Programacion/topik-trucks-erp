<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ServicioCentroPolicy.php" ) ) {
    require_once "app/Policies/ServicioCentroPolicy.php";
} else {
    require_once "../Policies/ServicioCentroPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ServicioCentroPolicy;

class ServicioCentro extends ServicioCentroPolicy
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'nomenclaturaOT'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'nomenclaturaOT' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "servicio_centros";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;
    public $nomenclaturaOT;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CENTROS DE SERVICIO
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT SC.* FROM $this->tableName SC ORDER BY SC.descripcion", $error);

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
                $this->nomenclaturaOT = $respuesta["nomenclaturaOT"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["nomenclaturaOT"] = self::$type["nomenclaturaOT"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto, nomenclaturaOT) VALUES (:descripcion, :nombreCorto, :nomenclaturaOT)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["nomenclaturaOT"] = self::$type["nomenclaturaOT"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, nomenclaturaOT = :nomenclaturaOT WHERE id = :id", $datos, $arrayPDOParam, $error);

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
