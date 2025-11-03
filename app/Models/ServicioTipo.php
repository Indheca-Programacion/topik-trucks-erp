<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ServicioTipoPolicy.php" ) ) {
    require_once "app/Policies/ServicioTipoPolicy.php";
} else {
    require_once "../Policies/ServicioTipoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ServicioTipoPolicy;

class ServicioTipo extends ServicioTipoPolicy
{
    static protected $fillable = [
        'descripcion', 'numero', 'unidadId'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'numero' => 'integer',
        'unidadId' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "servicio_tipos";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $numero;
    public $unidadId;
    public $unidad;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE SERVICIO
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT TS.*, U.descripcion AS 'unidades.descripcion' FROM $this->tableName TS INNER JOIN unidades U ON TS.unidadId = U.id ORDER BY TS.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->numero = $respuesta["numero"];
                $this->unidadId = $respuesta["unidadId"];

                // require_once "app/Models/Unidad.php";
                if ( file_exists ( "app/Models/Unidad.php" ) ) {
                    require_once "app/Models/Unidad.php";
                } else {
                    require_once "../Models/Unidad.php";
                }
                $unidad = New Unidad;
                $this->unidad = $unidad->consultar(null, $this->unidadId);
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, numero, unidadId) VALUES (:descripcion, :numero, :unidadId)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["unidadId"] = self::$type["unidadId"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, numero = :numero, unidadId = :unidadId WHERE id = :id", $datos, $arrayPDOParam, $error);

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
