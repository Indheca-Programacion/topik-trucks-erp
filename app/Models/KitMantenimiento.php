<?php

namespace App\Models;

if ( file_exists ( "app/Policies/KitMantenimientoPolicy.php" ) ) {
    require_once "app/Policies/KitMantenimientoPolicy.php";
} else {
    require_once "../Policies/KitMantenimientoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\KitMantenimientoPolicy;

class KitMantenimiento extends KitMantenimientoPolicy
{
    static protected $fillable = [
        'tipoMantenimiento', 'tipoMaquinaria', 'modelo', 'proveedorId', 'observacion', 'detalles'
    ];

    static protected $type = [
        'id' => 'integer',
        'tipoMantenimiento' => 'string',
        'tipoMaquinaria' => 'integer',
        'modelo' => 'integer',
        'proveedorId' => 'integer',
        'observacion' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "kit_mantenimiento";

    protected $keyName = "id";

    public $id = null;    
    public $tipoMantenimiento;
    public $tipoMaquinaria;
    public $modelo;
    public $proveedorId;
    public $nombreCorto;
    public $observacion;
    public $detalles = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR TIPOS DE MANTENIMIENTO
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT KM.*, MAT.descripcion as tipoMaquinaria, MO.descripcion as modelo,
                                                            CASE 
                                                                    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', IFNULL(P.apellidoPaterno, ''), ' ', IFNULL(P.apellidoMaterno, '')))
                                                                    WHEN P.personaFisica = 0 THEN P.razonSocial 
                                                            END AS proveedor 
                                                      FROM $this->tableName KM
                                                      INNER JOIN maquinaria_tipos MAT ON KM.tipoMaquinaria = MAT.id
                                                      INNER JOIN modelos MO ON KM.modelo = MO.id
                                                      left JOIN proveedores P ON KM.proveedorId = P.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->tipoMantenimiento = $respuesta["tipoMantenimiento"];
                $this->tipoMaquinaria = $respuesta["tipoMaquinaria"];
                $this->modelo = $respuesta["modelo"];
                $this->proveedorId = $respuesta["proveedorId"];
                $this->observacion = $respuesta["observacion"];
            }

            return $respuesta;

        }

    }

    public function consultarDetalles() {

        $this->detalles = Conexion::queryAll($this->bdName, "SELECT * FROM kit_mantenimiento_detalles WHERE kitMantenimientoId = $this->id", $error);

        return $this->detalles;

    }
    
    public function consultarKitsParaMaquinaria()
    {
        return Conexion::queryAll($this->bdName, "SELECT KM.*, MAT.descripcion as tipoMaquinaria, MO.descripcion as modelo
                                                      FROM $this->tableName KM
                                                      INNER JOIN maquinaria_tipos MAT ON KM.tipoMaquinaria = MAT.id
                                                      INNER JOIN modelos MO ON KM.modelo = MO.id
                                                      left JOIN proveedores P ON KM.proveedorId = P.id
                                                      WHERE MAT.id = $this->tipoMaquinaria and MO.id = $this->modelo", $error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["tipoMantenimiento"] = self::$type["tipoMantenimiento"];
        $arrayPDOParam["tipoMaquinaria"] = self::$type["tipoMaquinaria"];
        $arrayPDOParam["modelo"] = self::$type["modelo"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["observacion"] = self::$type["observacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);
        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {
            $this->id = $lastId;
            $arrayDetalles = isset($datos["detalles"]) ? $datos["detalles"] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles);
        }
        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["tipoMantenimiento"] = self::$type["tipoMantenimiento"];
        $arrayPDOParam["tipoMaquinaria"] = self::$type["tipoMaquinaria"];
        $arrayPDOParam["modelo"] = self::$type["modelo"];
        $arrayPDOParam["proveedorId"] = self::$type["proveedorId"];
        $arrayPDOParam["observacion"] = self::$type["observacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta =  Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {
            $arrayDetalles = isset($datos["detalles"]) ? $datos["detalles"] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles);
        } 

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

    function insertarDetalles(array $arrayDetalles = null) {

        $respuesta = false;
    
        if ( $arrayDetalles ) {

            $insertarPDOParam = array();
            $insertarPDOParam["kitMantenimientoId"] = self::$type[$this->keyName];
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["unidad"] = "string";
            $insertarPDOParam["numeroParte"] = "string";
            $insertarPDOParam["concepto"] = "string";
            $insertarPDOParam["costo"] = "decimal";

            for ($i = 0; $i < count($arrayDetalles["cantidad"]); $i++) { 

                $insertar = array();
                $insertar["kitMantenimientoId"] = $this->id;
                $insertar["cantidad"] = $arrayDetalles["cantidad"][$i];
                $insertar["unidad"] = $arrayDetalles["unidad"][$i];
                $insertar["numeroParte"] = $arrayDetalles["numeroParte"][$i];
                $insertar["concepto"] = $arrayDetalles["concepto"][$i];
                $insertar["costo"] = $arrayDetalles["costo"][$i] ?? 0;

                // Quitar las comas de los campos decimal
                $insertar["cantidad"] = str_replace(',', '', $insertar["cantidad"]);

                $kitMantenimientoDetalleId = 0;
                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO kit_mantenimiento_detalles (kitMantenimientoId, cantidad, unidad, numeroParte, concepto, costo) VALUES (:kitMantenimientoId, :cantidad, :unidad, :numeroParte, :concepto, :costo)", $insertar, $insertarPDOParam, $error, $kitMantenimientoDetalleId);

            }
            
        }

        return $respuesta;

    }

    public function editar($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];
        $arrayPDOParam["cantidad"] = "decimal";
        $arrayPDOParam["unidad"] = "string";
        $arrayPDOParam["numero_parte"] = "string";
        $arrayPDOParam["concepto"] = "string";

        return Conexion::queryExecute($this->bdName, "UPDATE kit_mantenimiento_detalles SET cantidad = :cantidad, unidad = :unidad, numeroParte = :numero_parte, concepto = :concepto WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminarKitMaquinaria($id) {
        $datos = array();
        $datos["id"] = $id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];

        return Conexion::queryExecute($this->bdName, "DELETE FROM kits_maquinarias WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function agregarAlaMaquinaria($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["kitId"] = self::$type["id"];
        $arrayPDOParam["maquinariaId"] = self::$type["id"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO kits_maquinarias (kitId, maquinariaId) VALUES (:kitId, :maquinariaId)", $datos, $arrayPDOParam, $error);

    }
}
