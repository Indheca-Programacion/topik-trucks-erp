<?php

namespace App\Models;

use App\Conexion;
use PDO;

class Desempeno
{
    static protected $fillable = [
        'descripcion', 'nombreCorto'
    ];

    static protected $type = [
        'id' => 'integer',
        'generador_detalle' => 'integer',
        'hmr' => 'decimal',
        'rr' => 'decimal',
        'lcc' => 'decimal',
        'observaciones' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "desempeno";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR ESTATUS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        return Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

    }

    public function consultarExistente($valor){
        return Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE generador_detalle = $valor", $error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["generador_detalle"] = self::$type["generador_detalle"];
        $arrayPDOParam["hmr"] = self::$type["hmr"];
        $arrayPDOParam["rr"] = self::$type["rr"];
        $arrayPDOParam["lcc"] = self::$type["lcc"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        
        $arrayPDOParam = array();
        $arrayPDOParam["generador_detalle"] = self::$type["generador_detalle"];
        $arrayPDOParam["hmr"] = self::$type["hmr"];
        $arrayPDOParam["rr"] = self::$type["rr"];
        $arrayPDOParam["lcc"] = self::$type["lcc"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE generador_detalle = :generador_detalle", $datos, $arrayPDOParam, $error);

    }

}
