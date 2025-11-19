<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ObraPolicy.php" ) ) {
    require_once "app/Policies/ObraPolicy.php";
} else {
    require_once "../Policies/ObraPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ObraPolicy;

class Obra extends ObraPolicy
{
    static protected $fillable = [
        'empresaId', 'descripcion', 'nombreCorto', 'estatusId', 'periodos', 'fechaInicio', 'almacen'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'estatusId' => 'integer',
        'periodos' => 'integer',
        'fechaInicio' => 'date',
        'fechaFinalizacion' => 'date',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'almacen' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "obras";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $descripcion;
    public $nombreCorto;
    public $estatusId;
    public $fechaInicio;
    public $usuarioIdCreacion;
    public $usuarioIdActualizacion;
    public $periodos;
    public $almacen;


    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR OBRAS
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT O.*, E.nombreCorto AS 'empresas.nombreCorto',CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombreCompleto', US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno' FROM $this->tableName O INNER JOIN empresas E ON O.empresaId = E.id INNER JOIN usuarios US ON O.usuarioIdCreacion = US.id ORDER BY E.id, O.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->periodos = $respuesta["periodos"];
                $this->estatusId = $respuesta["estatusId"];
                $this->fechaInicio = $respuesta["fechaInicio"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->almacen = $respuesta["almacen"];

            }

            return $respuesta;

        }
    }

    public function consultarAbiertas()
    {
        return Conexion::queryAll($this->bdName, "SELECT O.*, E.nombreCorto AS 'empresas.nombreCorto',CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombreCompleto', US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno' FROM $this->tableName O INNER JOIN empresas E ON O.empresaId = E.id INNER JOIN usuarios US ON O.usuarioIdCreacion = US.id WHERE O.estatusId = 1 ORDER BY E.id, O.descripcion", $error);
    }

    public function consultarCC(){
        return Conexion::queryUniqueCC(CONST_BD_SECURITY_CC, "SELECT * FROM $this->tableName WHERE descripcion = '$this->descripcion'", $error);
    }

    public function consultarDetalleRenta($obraId)
    {
        $respuesta = Conexion::queryUniqueCC(CONST_BD_SECURITY_CC, "SELECT * FROM obra_detalles WHERE obraId = $obraId and indirectoId = 539;", $error);
        if ( !$respuesta){

            $lastId = 0;
            $datos = array();
            $datos["obraId"] = $obraId;
            $datos["indirectoId"] = 539; // Indirecto de R
            $datos["cantidad"] = 1;
            $datos["presupuesto"] = 0;
            $datos["presupuesto_dolares"] = 0;

            $arrayPDOParam = array();
            $arrayPDOParam["obraId"] = "integer";
            $arrayPDOParam["indirectoId"] = "integer";
            $arrayPDOParam["cantidad"] = "decimal";
            $arrayPDOParam["presupuesto"] = "decimal";
            $arrayPDOParam["presupuesto_dolares"] = "decimal";
            $campos = fCreaCamposInsert($arrayPDOParam);
            $respuesta = Conexion::queryExecuteCC(CONST_BD_SECURITY_CC, "INSERT INTO obra_detalles " . $campos, $datos, $arrayPDOParam, $error, $lastId);
            if ( $respuesta ) {
                $respuesta = Conexion::queryUniqueCC(CONST_BD_SECURITY_CC, "SELECT * FROM obra_detalles WHERE id = $lastId", $error);
            }
            
        }

        return $respuesta;
    }

    public function crear($datos)
    {
        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fechaInicio"] = fFechaSQL($datos["fechaInicio"]);

        $arrayPDOParam = array();        
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["periodos"] = self::$type["periodos"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["fechaInicio"] = self::$type["fechaInicio"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["almacen"] = self::$type["almacen"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) $this->id = $lastId;

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fechaInicio"] = fFechaSQL($datos["fechaInicio"]);
        $arrayPDOParam = array();
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["periodos"] = self::$type["periodos"];
        $arrayPDOParam["fechaInicio"] = self::$type["fechaInicio"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["almacen"] = self::$type["almacen"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function actualizarSemana($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["semanaExtra"] = self::$type["semanaExtra"];
        $campos = fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}
