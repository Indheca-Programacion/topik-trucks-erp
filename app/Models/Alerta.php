<?php

namespace App\Models;

if ( file_exists ( "app/Policies/AlertaPolicy.php" ) ) {
    require_once "app/Policies/AlertaPolicy.php";
} else {
    require_once "../Policies/AlertaPolicy.php";
}
use App\Conexion;
use App\Policies\AlertaPolicy;
use PDO;

class Alerta extends AlertaPolicy
{
    static protected $fillable = [
        'ubicacion', 'obra', 'usuarios'
    ];

    static protected $type = [
        'id' => 'integer',
        'ubicacion' => 'integer',
        'obra' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarios' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "configuracion_programacion_reportes";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT A.*, U.descripcion as 'ubicacion.descripcion', O.descripcion as 'obra.descripcion', CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombreCompleto'
                                                    FROM {$this->tableName} A
                                                    INNER JOIN ubicaciones U ON U.id = A.ubicacion
                                                    INNER JOIN obras O ON O.id = A.obra
                                                    INNER JOIN usuarios US ON US.id = A.usuarioIdCreacion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM {$this->tableName} WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM {$this->tableName} WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->ubicacion = $respuesta["ubicacion"];
                $this->obra = $respuesta["obra"];
                $this->usuarios = json_decode($respuesta["usuarios"]);
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["usuarios"] = json_encode($datos["usuarios"]);
        
        $arrayPDOParam = array();
        $arrayPDOParam["ubicacion"] = self::$type["ubicacion"];
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["usuarios"] = self::$type["usuarios"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);


        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        $datos["usuarios"] = isset($datos["usuarios"]) ? json_encode($datos["usuarios"]) : '[]';
        // Agregar al request para especificar el usuario que actualizó la Requisición
        $arrayPDOParam = array();
        $arrayPDOParam["ubicacion"] = self::$type["ubicacion"];
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["usuarios"] = self::$type["usuarios"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

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
