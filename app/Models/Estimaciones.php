<?php

namespace App\Models;

use App\Conexion;
use PDO;

class Estimaciones
{
    static protected $fillable = [
        'descripcion', 'nombreCorto'
    ];

    static protected $type = [
        'id' => 'integer',
        'generador_detalle_id' => 'integer',
        'costo' => 'decimal',
        'pu' => 'decimal',
        'operacion' => 'decimal',
        'comb' => 'decimal',
        'mantto' => 'decimal',
        'flete' => 'decimal',
        'ajuste' => 'decimal',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "estimaciones";

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
        return Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE generador_detalle_id = $valor", $error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["generador_detalle_id"] = self::$type["generador_detalle_id"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["pu"] = self::$type["pu"];
        $arrayPDOParam["operacion"] = self::$type["operacion"];
        $arrayPDOParam["comb"] = self::$type["comb"];
        $arrayPDOParam["mantto"] = self::$type["mantto"];
        $arrayPDOParam["flete"] = self::$type["flete"];
        $arrayPDOParam["ajuste"] = self::$type["ajuste"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        
        $arrayPDOParam = array();
        $arrayPDOParam["generador_detalle_id"] = self::$type["generador_detalle_id"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["pu"] = self::$type["pu"];
        $arrayPDOParam["operacion"] = self::$type["operacion"];
        $arrayPDOParam["comb"] = self::$type["comb"];
        $arrayPDOParam["mantto"] = self::$type["mantto"];
        $arrayPDOParam["flete"] = self::$type["flete"];
        $arrayPDOParam["ajuste"] = self::$type["ajuste"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE generador_detalle_id = :generador_detalle_id", $datos, $arrayPDOParam, $error);

    }

}
