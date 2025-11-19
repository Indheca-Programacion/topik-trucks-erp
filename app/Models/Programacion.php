<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ProgramacionPolicy.php" ) ) {
    require_once "app/Policies/ProgramacionPolicy.php";
} else {
    require_once "../Policies/ProgramacionPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ProgramacionPolicy;

class Programacion extends ProgramacionPolicy
{
    static protected $fillable = [
        'maquinariaId', 'servicioTipoId', 'horoOdometroUltimo', 'cantidadSiguienteServicio'
    ];

    static protected $type = [
        'id' => 'integer',
        'maquinariaId' => 'integer',
        'servicioTipoId' => 'integer',
        'horoOdometroUltimo' => 'decimal',
        'cantidadSiguienteServicio' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "programaciones";

    protected $keyName = "id";

    public $id = null;
    public $maquinariaId;
    public $servicioTipoId;
    public $horoOdometroUltimo;
    public $cantidadSiguienteServicio;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PROGRAMACION
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT P.* FROM $this->tableName P ORDER BY P.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->servicioTipoId = $respuesta["servicioTipoId"];
                $this->horoOdometroUltimo = $respuesta["horoOdometroUltimo"];
                $this->cantidadSiguienteServicio = $respuesta["cantidadSiguienteServicio"];
            }

            return $respuesta;

        }
    }

    /*=============================================
    MOSTRAR PROGRAMACION DE LA MAQUINARIA
    =============================================*/
    public function consultarMaquinaria()
    {
        $query = "SELECT P.*
            FROM        {$this->tableName} P
            WHERE       P.maquinariaId = {$this->maquinariaId}
            ORDER BY    P.servicioTipoId";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function crear($datos)
    {
        // Agregar al request para especificar el usuario que creó la Programación
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Quitar las comas de los campos decimal
        $datos["horoOdometroUltimo"] = str_replace(',', '', $datos["horoOdometroUltimo"]);

        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["servicioTipoId"] = self::$type["servicioTipoId"];
        $arrayPDOParam["horoOdometroUltimo"] = self::$type["horoOdometroUltimo"];
        $arrayPDOParam["cantidadSiguienteServicio"] = self::$type["cantidadSiguienteServicio"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Programación
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Quitar las comas de los campos decimal
        $datos["horoOdometroUltimo"] = str_replace(',', '', $datos["horoOdometroUltimo"]);

        $arrayPDOParam = array();
        // $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        // $arrayPDOParam["servicioTipoId"] = self::$type["servicioTipoId"];
        $arrayPDOParam["horoOdometroUltimo"] = self::$type["horoOdometroUltimo"];
        if ( isset($datos["cantidadSiguienteServicio"]) ) $arrayPDOParam["cantidadSiguienteServicio"] = self::$type["cantidadSiguienteServicio"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function eliminar()
    {
        return false;
    }

    public function eliminarMaquinaria()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos['maquinariaId'] = $this->maquinariaId;
        
        $arrayPDOParam = array();
        $arrayPDOParam['maquinariaId'] = self::$type['maquinariaId'];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE maquinariaId = :maquinariaId", $datos, $arrayPDOParam, $error);
    }
}
