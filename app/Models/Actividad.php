<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ActividadPolicy.php" ) ) {
    require_once "app/Policies/ActividadPolicy.php";
} else {
    require_once "../Policies/ActividadPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ActividadPolicy;

class Actividad extends ActividadPolicy
{
    static protected $fillable = [
        'empresaId', 'folio', 'empleadoId', 'fechaInicial', 'fechaFinal', 'detalles'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'folio' => 'integer',
        'empleadoId' => 'integer',
        'fechaInicial' => 'date',
        'fechaFinal' => 'date',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "actividades";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $folio;
    public $empleadoId;
    public $fechaInicial;
    public $fechaFinal;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR ULTIMO VALOR DEL CAMPO folio
    =============================================*/
    public function consultarLastId($empresaId) {

        $query = "SELECT    MAX(A.folio) AS 'folio'
                  FROM      actividades A
                  WHERE     A.empresaId = {$empresaId}";

        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);

        return $respuesta;

    }

    /*=============================================
    MOSTRAR ACTIVIDADES CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT    A.*, EM.nombreCorto AS 'empresas.nombreCorto',
                            CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'empleados.nombreCompleto',
                            ( SELECT SUM(AD.horas) FROM actividad_detalles AD WHERE AD.actividadId = A.id ) AS sumHorasTrabajadas
                FROM        actividades A
                INNER JOIN  empresas EM ON A.empresaId = EM.id
                INNER JOIN  empleados E ON A.empleadoId = E.id";

        foreach ($arrayFiltros as $key => $value) {
            if ( $key == 0 ) $query .= " WHERE";
            if ( $key > 0 ) $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " ORDER BY    A.fechaInicial DESC, A.id DESC";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR ACTIVIDADES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT A.*, EM.nombreCorto AS 'empresas.nombreCorto', CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'empleados.nombreCompleto', ( SELECT SUM(AD.horas) FROM actividad_detalles AD WHERE AD.actividadId = A.id ) AS sumHorasTrabajadas FROM {$this->tableName} A INNER JOIN empresas EM ON A.empresaId = EM.id INNER JOIN empleados E ON A.empleadoId = E.id ORDER BY A.fechaInicial DESC, A.id DESC", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT A.*, ( SELECT SUM(AD.horas) FROM actividad_detalles AD WHERE AD.actividadId = A.id ) AS sumHorasTrabajadas FROM {$this->tableName} A WHERE A.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT A.*, ( SELECT SUM(AD.horas) FROM actividad_detalles AD WHERE AD.actividadId = A.id ) AS sumHorasTrabajadas FROM {$this->tableName} A WHERE A.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->folio = $respuesta["folio"];
                $this->empleadoId = $respuesta["empleadoId"];
                $this->fechaInicial = $respuesta["fechaInicial"];
                $this->fechaFinal = $respuesta["fechaFinal"];
                $this->sumHorasTrabajadas = ( is_null($respuesta["sumHorasTrabajadas"]) ) ? 0 : $respuesta["sumHorasTrabajadas"];
            }

            return $respuesta;

        }

    }

    public function consultarDetalles() {

        $query = "SELECT    AD.*, S.folio AS 'servicios.folio', E.nombreCorto AS 'empresas.nombreCorto',
                            SC.descripcion AS 'servicio_centros.descripcion',
                            M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                            SE.descripcion AS 'servicio_estatus.descripcion'
                FROM        actividad_detalles AD
                INNER JOIN  servicios S ON AD.servicioId = S.id
                INNER JOIN  empresas E ON S.empresaId = E.id
                INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                INNER JOIN  maquinarias M ON S.maquinariaId = M.id
                INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                WHERE       AD.actividadId = {$this->id}
                ORDER BY    AD.fecha, AD.id";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);
    
        $this->detalles = $resultado;

    }

    public function crear($datos) {

        // Buscar el último folio según la Empresa
        $lastId = $this->consultarLastId($datos["empresaId"]);

        if ( $lastId === false ) {
            return false;
        }

        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Agregar al request para especificar numero y folio del Servicio
        $datos["folio"] = (int) $lastId["folio"] + 1;

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fechaInicial"] = fFechaSQL($datos["fechaInicial"]);
        // $datos["fechaFinal"] = fFechaSQL($datos["fechaFinal"]);
        $datos["fechaFinal"] = date('Y-m-d', strtotime($datos["fechaInicial"].' + 6 days'));
        
        $arrayPDOParam = array();
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["empleadoId"] = self::$type["empleadoId"];
        $arrayPDOParam["fechaInicial"] = self::$type["fechaInicial"];
        $arrayPDOParam["fechaFinal"] = self::$type["fechaFinal"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $actividadId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $actividadId);

        if ( $respuesta ) {

            $this->id = $actividadId;

            $respuesta = $this->insertarDetalles($datos['detalles']);

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // Convertir los campos date (fechaLarga) a formato SQL
        // $datos["fechaInicial"] = fFechaSQL($datos["fechaInicial"]);
        // $datos["fechaFinal"] = fFechaSQL($datos["fechaFinal"]);

        $arrayPDOParam = array();
        // $arrayPDOParam["fechaInicial"] = self::$type["fechaInicial"];
        // $arrayPDOParam["fechaFinal"] = self::$type["fechaFinal"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            $respuesta = $this->insertarDetalles($datos['detalles']);

        }

        return $respuesta;

    }

    function insertarDetalles(array $arrayDetalles) {

        $respuesta = false;

        $insertarPDOParam = array();
        $insertarPDOParam["actividadId"] = self::$type[$this->keyName];
        $insertarPDOParam["servicioId"] = "integer";
        $insertarPDOParam["fecha"] = "date";
        $insertarPDOParam["descripcion"] = "string";
        $insertarPDOParam["horas"] = "decimal";

        for ($i = 0; $i < count($arrayDetalles["fecha"]); $i++) {

            $insertar = array();
            $insertar["actividadId"] = $this->id;
            $insertar["servicioId"] = $arrayDetalles["servicioId"][$i];
            $insertar["fecha"] = $arrayDetalles["fecha"][$i];
            $insertar["descripcion"] = $arrayDetalles["descripcion"][$i];
            $insertar["horas"] = $arrayDetalles["horas"][$i];

            // Convertir los campos date (fechaLarga) a formato SQL
            $insertar["fecha"] = fFechaSQL($insertar["fecha"]);

            // Quitar las comas de los campos decimal
            $insertar["horas"] = str_replace(',', '', $insertar["horas"]);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO actividad_detalles (actividadId, servicioId, fecha, descripcion, horas) VALUES (:actividadId, :servicioId, :fecha, :descripcion, :horas)", $insertar, $insertarPDOParam, $error);

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
}
