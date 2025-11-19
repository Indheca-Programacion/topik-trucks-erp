<?php

namespace App\Models;

use App\Conexion;
use PDO;

class ChecklistTarea
{
    static protected $fillable = [
        'descripcion', 'sectionId', 'maquinariaTipoId'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'sectionId' => 'integer',
        'maquinariaTipoId' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "tarea_checklist";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $sectionId;
    public $maquinariaTipoId;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR COLORES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName C ORDER BY C.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryAll($this->bdName, "SELECT CT.*, SC.descripcion as 'seccion' FROM $this->tableName CT INNER JOIN section_checklist SC ON SC.id = CT.sectionId  WHERE $item = '$valor' ORDER BY SC.orden asc", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta[0]["id"];
                $this->descripcion = $respuesta[0]["descripcion"];
                $this->sectionId = $respuesta[0]["sectionId"];
                $this->maquinariaTipoId = $respuesta[0]["maquinariaTipoId"];

            }

            return $respuesta;

        }

    }

    public function consultarRespuestas($id)
    {
        return Conexion::queryAll($this->bdName, 
                                "SELECT CR.* , TC.descripcion,TC.sectionId, SC.descripcion AS 'seccion', 
                                case when CR.respuesta = 1 then 'Bueno'
                                when CR.respuesta = 2 then 'Malo'
                                when CR.respuesta = 0 then 'N/A' end as 'respuesta'
                                FROM checklist_respuestas CR 
                                INNER JOIN tarea_checklist TC ON TC.id = CR.tareaId
                                INNER JOIN section_checklist SC ON SC.id = TC.sectionId
                                WHERE CR.checklist_maquinaria = $id", $error);
    }

    public function consultarObservaciones($id)
    {
        return Conexion::queryAll($this->bdName, 
                                "SELECT CO.*, SC.descripcion AS 'seccion'
                                FROM checklist_observaciones CO
                                INNER JOIN section_checklist SC ON SC.id = CO.sectionId
                                WHERE CO.checklist_maquinaria = $id", $error);
    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["sectionId"] = self::$type["sectionId"];
        $arrayPDOParam["maquinariaTipoId"] = self::$type["maquinariaTipoId"];

        $campos = fCreaCamposInsert($arrayPDOParam);
        
        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

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
