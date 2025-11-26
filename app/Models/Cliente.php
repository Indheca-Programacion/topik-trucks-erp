<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ClientePolicy.php" ) ) {
    require_once "app/Policies/ClientePolicy.php";
} else {
    require_once "../Policies/ClientePolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ClientePolicy;

class Cliente extends ClientePolicy
{
    static protected $fillable = [
        'nombreCompleto', 'prefijo', 'telefono', 'correo', 'observaciones', 'metodoPago'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombreCompleto' => 'string',
        'prefijo' => 'string',
        'telefono' => 'string',
        'correo' => 'string',
        'observaciones' => 'string',
        'metodoPago' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "clientes";

    protected $keyName = "id";

    public $id = null; 
    public $nombreCompleto;
    public $prefijo;
    public $telefono;
    public $correo;
    public $observaciones;
    public $metodoPago;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CLIENTES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT C.* FROM $this->tableName C", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->nombreCompleto = $respuesta["nombreCompleto"];
                $this->prefijo = $respuesta["prefijo"];
                $this->telefono = $respuesta["telefono"];
                $this->correo = $respuesta["correo"];
                $this->observaciones = $respuesta["observaciones"];
                // $this->metodoPago = $respuesta["metodoPago"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];

            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["nombreCompleto"] = self::$type["nombreCompleto"];
        $arrayPDOParam["prefijo"] = self::$type["prefijo"];
        $arrayPDOParam["telefono"] = self::$type["telefono"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        // $arrayPDOParam["metodoPago"] = self::$type["metodoPago"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {
            $this->id = $lastId;
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["nombreCompleto"] = self::$type["nombreCompleto"];
        $arrayPDOParam["prefijo"] = self::$type["prefijo"];
        $arrayPDOParam["telefono"] = self::$type["telefono"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        // $arrayPDOParam["metodoPago"] = self::$type["metodoPago"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE id = :id", $datos, $arrayPDOParam, $error);

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
