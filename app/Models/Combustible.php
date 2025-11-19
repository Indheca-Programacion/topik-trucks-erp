<?php

namespace App\Models;

if ( file_exists ( "app/Policies/CombustiblePolicy.php" ) ) {
    require_once "app/Policies/CombustiblePolicy.php";
} else {
    require_once "../Policies/CombustiblePolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\CombustiblePolicy;

class Combustible extends CombustiblePolicy
{
    static protected $fillable = [
        'empresaId', 'empleadoId', 'fecha', 'hora', 'detalles', 'partidasEliminadas'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'empleadoId' => 'integer',
        'fecha' => 'date',
        'hora' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "combustibles";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $empleadoId;
    public $fecha;
    public $hora;

    static public function fillable()
    {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR CARGAS DE COMBUSTIBLE CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT    C.*, EM.nombreCorto AS 'empresas.nombreCorto',
                            CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'empleados.nombreCompleto',
                            ( SELECT IFNULL(SUM(CD.litros), 0) FROM combustible_detalles CD WHERE CD.combustibleId = C.id ) AS sumLitros
                FROM        {$this->tableName} C
                INNER JOIN  empresas EM ON C.empresaId = EM.id
                INNER JOIN  empleados E ON C.empleadoId = E.id";

        foreach ($arrayFiltros as $key => $value) {
            if ( $key == 0 ) $query .= " WHERE";
            if ( $key > 0 ) $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " ORDER BY    C.fecha DESC, C.hora DESC, C.id DESC";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR CARGAS DE COMBUSTIBLE
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            $query = "SELECT C.*, EM.nombreCorto AS 'empresas.nombreCorto',
                            CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS 'empleados.nombreCompleto',
                            ( SELECT IFNULL(SUM(CD.litros), 0) FROM combustible_detalles CD WHERE CD.combustibleId = C.id ) AS sumLitros,
                            ( SELECT IFNULL(GROUP_CONCAT(CD.observaciones SEPARATOR ', '), '') FROM combustible_detalles CD WHERE CD.combustibleId = C.id ) AS observaciones
                FROM        {$this->tableName} C
                INNER JOIN  empresas EM ON C.empresaId = EM.id
                INNER JOIN  empleados E ON C.empleadoId = E.id
                ORDER BY    C.fecha DESC, C.hora DESC, C.id DESC";

            return Conexion::queryAll($this->bdName, $query, $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT C.*, ( SELECT SUM(CD.litros) FROM combustible_detalles CD WHERE CD.combustibleId = C.id ) AS sumLitros FROM {$this->tableName} C WHERE C.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT C.*, ( SELECT SUM(CD.litros) FROM combustible_detalles CD WHERE CD.combustibleId = C.id ) AS sumLitros FROM {$this->tableName} C WHERE C.$item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->empleadoId = $respuesta["empleadoId"];
                $this->fecha = $respuesta["fecha"];
                $this->hora = $respuesta["hora"];
                $this->sumLitros = ( is_null($respuesta["sumLitros"]) ) ? 0 : $respuesta["sumLitros"];
            }

            return $respuesta;

        }
    }

    public function consultarDetalles()
    {
        $query = "SELECT CD.*, E.nombreCorto AS 'empresas.nombreCorto',
                        M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                        U.descripcion AS 'ubicaciones.descripcion',
                        CONCAT(EM.nombre, ' ', EM.apellidoPaterno, ' ', IFNULL(EM.apellidoMaterno, '')) AS 'empleados.nombreCompleto'
            FROM        combustible_detalles CD
            INNER JOIN  combustibles C ON CD.combustibleId = C.id
            INNER JOIN  empresas E ON C.empresaId = E.id
            INNER JOIN  maquinarias M ON CD.maquinariaId = M.id
            INNER JOIN  ubicaciones U ON CD.ubicacionId = U.id
            INNER JOIN  empleados EM ON CD.empleadoId = EM.id
            WHERE       CD.combustibleId = {$this->id}
            ORDER BY    CD.id";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);
    
        $this->detalles = $resultado;
    }

    public function crear($datos)
    {
        // Buscar el último folio según la Empresa
        // $lastId = $this->consultarLastId($datos["empresaId"]);

        // if ( $lastId === false ) {
        //     return false;
        // }

        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        // Agregar al request para especificar numero y folio del Servicio
        // $datos["folio"] = (int) $lastId["folio"] + 1;

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        
        $arrayPDOParam = array();
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        // $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["empleadoId"] = self::$type["empleadoId"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["hora"] = self::$type["hora"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $combustibleId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $combustibleId);

        if ( $respuesta ) {

            $this->id = $combustibleId;

            // $respuesta = $this->insertarDetalles($datos['detalles']);

        }

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            // $respuesta = $this->insertarDetalles($datos['detalles']);

            $arrayDetalles = isset($datos['detalles']) ? $datos['detalles'] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles);

            if ( isset($datos["partidasEliminadas"]) ) $respuesta = $this->eliminarDetalles($datos["partidasEliminadas"]);

        }

        return $respuesta;
    }

    function insertarDetalles(array $arrayDetalles)
    {
        $respuesta = false;

        $insertarPDOParam = array();
        $insertarPDOParam["combustibleId"] = self::$type[$this->keyName];
        $insertarPDOParam["maquinariaId"] = "integer";
        $insertarPDOParam["ubicacionId"] = "integer";
        $insertarPDOParam["empleadoId"] = "integer";
        $insertarPDOParam["horoOdometro"] = "decimal";
        $insertarPDOParam["litros"] = "decimal";
        $insertarPDOParam["observaciones"] = "string";

        for ($i = 0; $i < count($arrayDetalles["maquinariaId"]); $i++) {

            $insertar = array();
            $insertar["combustibleId"] = $this->id;
            $insertar["maquinariaId"] = $arrayDetalles["maquinariaId"][$i];
            $insertar["ubicacionId"] = $arrayDetalles["ubicacionId"][$i];
            $insertar["empleadoId"] = $arrayDetalles["empleadoId"][$i];
            $insertar["horoOdometro"] = $arrayDetalles["horoOdometro"][$i];
            $insertar["litros"] = $arrayDetalles["litros"][$i];
            $insertar["observaciones"] = $arrayDetalles["observaciones"][$i];

            // Quitar las comas de los campos decimal
            $insertar["horoOdometro"] = str_replace(',', '', $insertar["horoOdometro"]);
            $insertar["litros"] = str_replace(',', '', $insertar["litros"]);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO combustible_detalles (combustibleId, maquinariaId, ubicacionId, empleadoId, horoOdometro, litros, observaciones) VALUES (:combustibleId, :maquinariaId, :ubicacionId, :empleadoId, :horoOdometro, :litros, :observaciones)", $insertar, $insertarPDOParam, $error);

            // Actualizar la ubicación de la maquinaria
            if ( $respuesta && $arrayDetalles["ubicacionActualId"][$i] != $arrayDetalles["ubicacionId"][$i] ) {

                $actualizar = array();
                $actualizar["id"] = $arrayDetalles["maquinariaId"][$i];
                $actualizar["ubicacionId"] = $arrayDetalles["ubicacionId"][$i];

                $updatePDOParam = array();
                $updatePDOParam["id"] = "integer";
                $updatePDOParam["ubicacionId"] = "integer";

                Conexion::queryExecute($this->bdName, "UPDATE maquinarias SET ubicacionId = :ubicacionId WHERE id = :id", $actualizar, $updatePDOParam, $error);

            }

        }

        return $respuesta;
    }

    function eliminarDetalles(array $arrayDetalles = null)
    {
        $respuesta = false;

        if ( $arrayDetalles ) {

            $eliminarPDOParam = array();
            $eliminarPDOParam["id"] = "integer";
            $eliminarPDOParam["combustibleId"] = self::$type[$this->keyName];

            for ($i = 0; $i < count($arrayDetalles); $i++) {

                $eliminar = array();
                $eliminar["id"] = $arrayDetalles[$i];
                $eliminar["combustibleId"] = $this->id;

                $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM combustible_detalles WHERE id = :id AND combustibleId = :combustibleId", $eliminar, $eliminarPDOParam, $error);

            }

        }

        return $respuesta;
    }

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM combustible_detalles WHERE combustibleId = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        }

        return $respuesta;
    }
}
