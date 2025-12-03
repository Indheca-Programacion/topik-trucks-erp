<?php

namespace App\Models;

use App\Conexion;
use PDO;

class ServicioPartida
{
    static protected $fillable = [
        'id', 'cantidad', 'unidad', 'descripcion', 'costo_base', 'logistica', 'mantenimiento', 'utilidad', 'presupuestoId', 'servicioId'
    ];

    static protected $type = [
        'id' => 'integer',
        'cantidad' => 'decimal',
        'unidad' => 'string',
        'descripcion' => 'string',
        'costo_base' => 'decimal',
        'logistica' => 'decimal',
        'mantenimiento' => 'decimal',
        'utilidad' => 'decimal',
        'presupuestoId' => 'integer',
        'servicioId' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "servicio_partidas";

    protected $keyName = "id";

    public $id = null;  
    public $cantidad = null;  
    public $unidad = null;
    public $descripcion = null;
    public $costo_base = null;
    public $logistica = null;
    public $mantenimiento = null;
    public $utilidad = null;
    public $presupuestoId = null;
    public $servicioId = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE SERVICIO
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
                $this->cantidad = $respuesta["cantidad"];
                $this->unidad = $respuesta["unidad"];
                $this->descripcion = $respuesta["descripcion"];
                $this->costo_base = $respuesta["costo_base"];
                $this->costo_total = $respuesta["costo_total"];
                $this->logistica = $respuesta["logistica"];
                $this->mantenimiento = $respuesta["mantenimiento"];
                $this->utilidad = $respuesta["utilidad"];
                $this->precio_total = $respuesta["precio_total"];
                $this->presupuestoId = $respuesta["presupuestoId"];
                $this->servicioId = $respuesta["servicioId"];
            }

            return $respuesta;

        }

    }

    public function obtenerPartidasPresupuesto($presupuestoId) {

        $partidas = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName WHERE presupuestoId = $presupuestoId ORDER BY id", $error);

        return $partidas;

    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["costo_base"] = self::$type["costo_base"];
        $arrayPDOParam["logistica"] = self::$type["logistica"];
        $arrayPDOParam["mantenimiento"] = self::$type["mantenimiento"];
        $arrayPDOParam["utilidad"] = self::$type["utilidad"];
        $arrayPDOParam["presupuestoId"] = self::$type["presupuestoId"];
        $arrayPDOParam["servicioId"] = self::$type["servicioId"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["costo_base"] = self::$type["costo_base"];
        $arrayPDOParam["logistica"] = self::$type["logistica"];
        $arrayPDOParam["mantenimiento"] = self::$type["mantenimiento"];
        $arrayPDOParam["utilidad"] = self::$type["utilidad"];
        
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
