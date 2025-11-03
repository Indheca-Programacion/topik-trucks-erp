<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ConfiguracionProgramacionPolicy.php" ) ) {
    require_once "app/Policies/ConfiguracionProgramacionPolicy.php";
} else {
    require_once "../Policies/ConfiguracionProgramacionPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ConfiguracionProgramacionPolicy;

class ConfiguracionProgramacion extends ConfiguracionProgramacionPolicy
{
    static protected $fillable = [
        'servicioTipos', 'unidadesAbrirServicio'
    ];

    static protected $type = [
        'id' => 'integer',
        'servicioTipos' => 'string',
        'unidadesAbrirServicio' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "configuracion_programacion";

    protected $keyName = "id";

    public $id = null;    
    public $servicioTipos;
    public $unidadesAbrirServicio;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CONFIGURACION PROGRAMACION
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT CP.* FROM $this->tableName CP ORDER BY CP.id", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->servicioTipos = json_decode($respuesta["servicioTipos"]);
                $this->unidadesAbrirServicio = $respuesta["unidadesAbrirServicio"];
            }

            return $respuesta;

        }
    }

    public function checkServicioTipo($servicioTipoId)
    {
        if ( !isset($servicioTipoId) ) {
            return false;
        }

        $key = array_search($servicioTipoId, $this->servicioTipos);

        if ( $key === false ) {
            return false;
        }

        return true;
    }

    public function crear($datos)
    {
        return false;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Configuración
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Modificar el contenido de los checkboxes
        $datos["servicioTipos"] = ( isset($datos["servicioTipos"]) ) ? json_encode($datos["servicioTipos"]) : '[]';

        $arrayPDOParam = array();
        $arrayPDOParam["servicioTipos"] = self::$type["servicioTipos"];
        $arrayPDOParam["unidadesAbrirServicio"] = self::$type["unidadesAbrirServicio"];
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
}
