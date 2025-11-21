<?php

namespace App\Models;

if ( file_exists ( "app/Policies/GeneradorObservacionesPolicy.php" ) ) {
    require_once "app/Policies/GeneradorObservacionesPolicy.php";
} else {
    require_once "../Policies/GeneradorObservacionesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\GeneradorObservacionesPolicy;

class GeneradorObservaciones extends GeneradorObservacionesPolicy
{
    static protected $fillable = [
        'fecha_inicio', 'fecha_fin', 'observaciones', 'generadorDetalle'
    ];

    static protected $type = [
        'id' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'observaciones' => 'string',
        'generadorDetalle' => 'integer'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "generador_observaciones";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT R.*, U.descripcion AS ubicacion, US.nombre, US.apellidoPaterno
            FROM $this->tableName R
            INNER JOIN ubicaciones U ON U.id = R.ubicacionId
            INNER JOIN usuarios US ON US.id = R.usuarioIdCreacion
            ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT GO.*,GD.fk_generador FROM $this->tableName GO INNER JOIN generador_detalles GD on GD.id = GO.generadorDetalle WHERE GO.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->generadorDetalle = $respuesta["generadorDetalle"];
                $this->generadorId = $respuesta["fk_generador"];
                $this->fecha_inicio = fFechaLarga($respuesta["fecha_inicio"]);
                $this->fecha_fin = fFechaLarga($respuesta["fecha_fin"]);
                $this->observaciones = $respuesta["observaciones"];
            }

            return $respuesta;

        }

    }

    public function consultarObservaciones($generadorId){
        return Conexion::queryAll($this->bdName,"SELECT 
                                                    GO.*, 
                                                    M.numeroEconomico, 
                                                    M.empresaId
                                                FROM generador_observaciones GO
                                                INNER JOIN generador_detalles GD ON GD.id = GO.generadorDetalle
                                                INNER JOIN generadores G ON G.id = GD.fk_generador
                                                INNER JOIN maquinarias M ON M.id = GD.fk_maquinaria
                                                WHERE G.id = $generadorId");
    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["generadorDetalle"] = self::$type["generadorDetalle"];

        $columna=fCreaCamposInsert($arrayPDOParam);
        $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        $datos["fecha_fin"] = fFechaSQL($datos["fecha_fin"]);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$columna, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el reporte
            $this->id = $lastId;

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        $datos["fecha_fin"] = fFechaSQL($datos["fecha_fin"]);
        
        $arrayPDOParam = array();
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["generadorDetalle"] = self::$type["generadorDetalle"];
        
        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);

        
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
