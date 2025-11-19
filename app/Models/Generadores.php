<?php

namespace App\Models;

// require_once "app/conexion.php";
// require_once "app/Policies/PermisoPolicy.php";

if ( file_exists ( "app/Policies/GeneradoresPolicy.php" ) ) {
    require_once "app/Policies/GeneradoresPolicy.php";
} else {
    require_once "../Policies/GeneradoresPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\GeneradoresPolicy;

class Generadores extends GeneradoresPolicy
{
    static protected $fillable = [
        'obra', 'ubicacionId', 'fechaCreacion', 'mes', 'empresaId','obraId'
    ];

    static protected $type = [
        'id' => 'integer',
        'obra' => 'string',
        'ubicacionId' => 'integer',
        'empresaId' => 'integer',
        'obraId' => 'integer',
        'mes' => 'date',
        'usuarioIdCreacion' => 'integer',
        'firmado' => 'integer',
        'estimacionFirma' => 'integer',
        'estimacionSupervisorFirma' => 'integer',
        'generadorSupervisorFirma' => 'integer',
        'folio' => 'string',
        'observaciones' => 'string'
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "generadores";

    protected $keyName = "id";

    public $id = null;
    public $codigo;
    public $nombre;
    public $descripcion;
    public $folio;
    public $obraId;
    public $obra;
    public $observaciones;
    public $mes;
    public $ubicacionId;
    public $usuarioIdCreacion;
    public $empresaId;
    public $firmado;
    public $estimacionFirma;
    public $estimacionSupervisorFirma;
    public $generadorSupervisorFirma;
    public $ubicacion;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PERMISOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT R.*, COALESCE(O.descripcion, R.obra) as obra, U.descripcion AS ubicacion, CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombreCompleto'
            FROM $this->tableName R
            INNER JOIN ubicaciones U ON U.id = R.ubicacionId
            INNER JOIN usuarios US ON US.id = R.usuarioIdCreacion
            LEFT JOIN obras O ON O.id = R.obraId
            order by folio desc
            ", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {

                $mes = date("Y-m", strtotime($respuesta["mes"]));

                $this->id = $respuesta["id"];
                $this->folio = 'GEN-'.$respuesta["folio"];
                $this->obraId = $respuesta["obraId"];
                $this->obra = $respuesta["obra"];
                $this->mes = $mes;
                $this->ubicacionId = $respuesta["ubicacionId"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->observaciones = $respuesta["observaciones"];
                $this->empresaId = $respuesta["empresaId"];
                $this->firmado = $respuesta["firmado"];
                $this->estimacionFirma = $respuesta["estimacionFirma"];
                $this->estimacionSupervisorFirma = $respuesta["estimacionSupervisorFirma"];
                $this->generadorSupervisorFirma = $respuesta["generadorSupervisorFirma"];

                if ( file_exists ( "app/Models/Obra.php" ) ) {
                    require_once "app/Models/Obra.php";
                } else {
                    require_once "../Models/Obra.php";
                }
                $obra = New \App\Models\Obra;
                if(!is_null($this->obraId)) {
                    $obra->consultar(null, $this->obraId);
                    $this->obra = mb_strtoupper(fString($obra->descripcion));
                }

                if ( file_exists ( "app/Models/Ubicacion.php" ) ) {
                    require_once "app/Models/Ubicacion.php";
                } else {
                    require_once "../Models/Ubicacion.php";
                }
                $ubicacion = New \App\Models\Ubicacion;
                if(!is_null($this->ubicacionId)) {
                    $ubicacion->consultar(null, $this->ubicacionId);
                    $this->ubicacion = mb_strtoupper(fString($ubicacion->descripcion));
                }  
                
            }

            return $respuesta;

        }

    }

    public function consultarSinFirma() {

        return Conexion::queryAll($this->bdName, 
        "SELECT R.*, COALESCE(O.descripcion, R.obra) as obra, U.descripcion AS ubicacion, CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombreCompleto'
            FROM $this->tableName R
            INNER JOIN ubicaciones U ON U.id = R.ubicacionId
            INNER JOIN usuarios US ON US.id = R.usuarioIdCreacion
            LEFT JOIN obras O ON O.id = R.obraId
            WHERE R.estimacionFirma IS NULL or R.firmado IS NULL
            order by folio desc
        ", $error);

    }

    public function obtenerFolio() {
        return Conexion::queryUnique($this->bdName, "SELECT folio FROM $this->tableName ORDER BY $this->keyName DESC LIMIT 1;", $error);
    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["mes"] = self::$type["mes"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["folio"] = self::$type["folio"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $lastFolio = $this->obtenerFolio();
        $datos["folio"] = (int) $lastFolio["folio"] + 1;

        $columna=fCreaCamposInsert($arrayPDOParam);
        $fecha = strtotime($datos["mes"]);
        $datos["mes"] = date("Y-m-d",$fecha);
        
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
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["codigo"] = self::$type["codigo"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET codigo = :codigo, descripcion = :descripcion WHERE id = :id", $datos, $arrayPDOParam, $error);

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

    public function firmar($firma) {
        // Agregar al request para firmar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["firmado"] = $firma;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["firmado"] = self::$type["firmado"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET firmado = :firmado WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function autorizarEstimacion($firma) {
        // Agregar al request para firmar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["estimacionFirma"] = $firma;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estimacionFirma"] = self::$type["estimacionFirma"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET estimacionFirma = :estimacionFirma WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function autorizarEstimacionSupervisor(){
        // Agregar al request para firmar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["estimacionSupervisorFirma"] = usuarioAutenticado()["id"];
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["estimacionSupervisorFirma"] = self::$type["estimacionSupervisorFirma"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET estimacionSupervisorFirma = :estimacionSupervisorFirma WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function autorizarSupervisor()
    {
        // Agregar al request para firmar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["generadorSupervisorFirma"] = usuarioAutenticado()["id"];
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["generadorSupervisorFirma"] = self::$type["generadorSupervisorFirma"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET generadorSupervisorFirma = :generadorSupervisorFirma WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function actualizarObservacion()
    {
        // Agregar al request para actualizar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        $datos["observaciones"] = $this->observaciones;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET observaciones = :observaciones WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }
}
