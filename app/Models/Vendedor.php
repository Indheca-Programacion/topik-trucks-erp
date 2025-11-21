<?php

namespace App\Models;

if ( file_exists ( "app/Policies/VendedorPolicy.php" ) ) {
    require_once "app/Policies/VendedorPolicy.php";
} else {
    require_once "../Policies/VendedorPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\VendedorPolicy;

class Vendedor extends VendedorPolicy
{
    static protected $fillable = [
        'nombreCompleto', 'telefono', 'correo', 'proveedorId', 'zona'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombreCompleto' => 'string',
        'telefono' => 'string',
        'correo' => 'string',
        'proveedorId' => 'integer',
        'zona' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "proveedor_vendedores";    

    protected $keyName = "id";

    public $id = null;
    public $nombreCompleto = null;
    public $telefono = null;
    public $correo = null;
    public $proveedorId = null;


    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR USUARIOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            $proveedorId = \usuarioAutenticadoProveedor()["id"];

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where proveedorId = $proveedorId", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->nombreCompleto = $respuesta["nombreCompleto"];
                $this->telefono = $respuesta["telefono"];
                $this->correo = $respuesta["correo"];
                $this->proveedorId = $respuesta["proveedorId"];
                $this->zona = $respuesta["zona"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {
        // Request con el nombre del archivo (firma)
        $datos["proveedorId"] = \usuarioAutenticadoProveedor()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["nombreCompleto"] = self::$type["nombreCompleto"];
        $arrayPDOParam["telefono"] = self::$type["telefono"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["zona"] = self::$type["zona"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el registro
            $this->id = $lastId;

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];
        $arrayPDOParam["nombreCompleto"] = self::$type["nombreCompleto"];
        $arrayPDOParam["telefono"] = self::$type["telefono"];
        $arrayPDOParam["correo"] = self::$type["correo"];
        $arrayPDOParam["zona"] = self::$type["zona"];

        $datos[$this->keyName] = $this->id;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;

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
