<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PresupuestoPolicy.php" ) ) {
    require_once "app/Policies/PresupuestoPolicy.php";
} else {
    require_once "../Policies/PresupuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PresupuestoPolicy;

class Presupuesto extends PresupuestoPolicy
{
    static protected $fillable = [
        'maquinariaId', 'clienteId', 'fuente'
    ];

    static protected $type = [
        'id' => 'integer',
        'maquinariaId' => 'integer',
        'clienteId' => 'integer',
        'fuente' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "presupuestos";

    protected $keyName = "id";

    public $id = null;
    public $maquinariaId = null;
    public $clienteId = null;
    public $fuente = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PRESUPUESTOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->clienteId = $respuesta["clienteId"];
                $this->fuente = $respuesta["fuente"];

            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["clienteId"] = self::$type["clienteId"];
        $arrayPDOParam["fuente"] = self::$type["fuente"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos , $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["clienteId"] = self::$type["clienteId"];
        $arrayPDOParam["fuente"] = self::$type["fuente"];
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
