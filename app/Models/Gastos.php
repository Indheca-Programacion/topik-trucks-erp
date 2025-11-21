<?php

namespace App\Models;

if ( file_exists ( "app/Policies/GastosPolicy.php" ) ) {
    require_once "app/Policies/GastosPolicy.php";
} else {
    require_once "../Policies/GastosPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\GastosPolicy;

class Gastos extends GastosPolicy
{
    static protected $fillable = [
        'tipoGasto', 'encargado', 'fecha_inicio', 'fecha_fin', 'banco', 'cuenta', 'clave', 'empresa', 'obra'
    ];

    static protected $type = [
        'id' => 'integer',
        'obra' => 'string',
        'tipoGasto' => 'integer',
        'encargado' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_envio' => 'date',
        'banco' => 'string',
        'cuenta' => 'string',
        'clave' => 'string',
        'requisicionId' => 'integer',
        'cerrada' => 'integer',
        'empresa' => 'integer',
        'obra' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdAutorizacion' => 'integer',
        'usuarioIdRevision' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "gastos";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                    "SELECT G.*,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio, EM.nombreCorto as 'empresa.nombreCorto',
                    CASE
                        WHEN G.tipoGasto = 1 THEN 'deducible'
                        WHEN G.tipoGasto = 2 THEN 'no deducible'
                    END AS tipoGasto,
                    CASE
                        WHEN G.cerrada = 0 THEN 'ABIERTO'
                        WHEN G.cerrada = 2 THEN 'EN PROCESO'
                        WHEN G.cerrada = 3 THEN 'PROCESADO'
                        WHEN G.cerrada = 4 THEN 'PAGADO'
                        WHEN G.requisicionId is not null THEN 'CON REQ.'
                        WHEN G.cerrada = 1 THEN 'CERRADO'
                        ELSE 'DESCONOCIDO'
                    END AS estatus,
                    O.nombreCorto as 'obra.nombreCorto'
                    FROM  $this->tableName G
                    INNER JOIN usuarios E ON E.id = G.encargado
                    INNER JOIN empresas EM ON EM.ID = G.empresa
                    LEFT JOIN obras O on O.id = G.obra
                    order by G.fecha_envio desc", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->requisicionId = $respuesta["requisicionId"];
                $this->cerrada = $respuesta["cerrada"];
                $this->tipoGasto = $respuesta["tipoGasto"];
                $this->banco = $respuesta["banco"];
                $this->cuenta = $respuesta["cuenta"];
                $this->empresa = $respuesta["empresa"];
                $this->clave = $respuesta["clave"];
                $this->encargado = $respuesta["encargado"];
                $this->obra = $respuesta["obra"];
                $this->fecha_inicio = fFechaLarga($respuesta["fecha_inicio"]);
                $this->fecha_fin = $respuesta["fecha_fin"] ? fFechaLarga($respuesta["fecha_fin"]) : null;
                $this->fecha_envio = fFechaLarga($respuesta["fecha_envio"]);
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdAutorizacion = $respuesta["usuarioIdAutorizacion"];
                $this->usuarioIdRevision = $respuesta["usuarioIdRevision"];
            }

            return $respuesta;

        }
    }

    public function consultarFiltros($arrayFiltros)
    {
        $query  = "SELECT G.*,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio, EM.nombreCorto as 'empresa.nombreCorto',
                    CASE
                        WHEN G.tipoGasto = 1 THEN 'deducible'
                        WHEN G.tipoGasto = 2 THEN 'no deducible'
                    END AS tipoGasto,
                    CASE
                        WHEN G.cerrada = 0 THEN 'ABIERTO'
                        WHEN G.cerrada = 1 THEN 'CERRADO'
                        WHEN G.cerrada = 2 THEN 'EN PROCESO'
                        WHEN G.cerrada = 3 THEN 'PROCESADO'
                        WHEN G.cerrada = 4 THEN 'PAGADO'
                        WHEN G.requisicionId is not null THEN 'CON REQ.'
                        ELSE 'DESCONOCIDO'
                    END AS estatus,
                    O.nombreCorto as 'obra.nombreCorto'
                    FROM  $this->tableName G
                    INNER JOIN usuarios E ON E.id = G.encargado
                    INNER JOIN empresas EM ON EM.ID = G.empresa
                    LEFT JOIN obras O on O.id = G.obra";

        foreach ($arrayFiltros as $key => $value) {
            if ( $key == 0 ) $query .= " WHERE";
            if ( $key > 0 ) $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " order by G.fecha_envio desc";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    public function consultarCerrados()
    {
        return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName where cerrada = 0",$error);
    }

    public function consultarPorUsuario($id)
    {
        return Conexion::queryAll($this->bdName, 
                    "SELECT G.*,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio, EM.nombreCorto as 'empresa.nombreCorto',
                    CASE
                        WHEN G.tipoGasto = 1 THEN 'deducible'
                        WHEN G.tipoGasto = 2 THEN 'no deducible'
                    END AS tipoGasto,
                    CASE
                        WHEN G.requisicionId is not null THEN 'CON REQ.'
                        WHEN G.cerrada = 0 THEN 'ABIERTO'
                        WHEN G.cerrada = 1 THEN 'CERRADO'
                    END AS estatus,
                    O.nombreCorto as 'obra.nombreCorto'
                    FROM  $this->tableName G
                    INNER JOIN usuarios E ON E.id = G.encargado
                    INNER JOIN empresas EM ON EM.ID = G.empresa
                    LEFT JOIN obras O on O.id = G.obra
                    WHERE G.usuarioIdCreacion = $id
                    order by G.fecha_inicio desc", $error);
    }

    public function consultarArchivos($id)
    {
        $query = "SELECT    SA.*, SA.ruta as 'ruta'
                    FROM        gasto_archivos SA
                    INNER JOIN gasto_detalles GD ON GD.id = SA.gastoDetalleId
                    INNER JOIN gastos GA ON GA.id = GD.gastoId
                    WHERE GA.id = $id
                    ORDER BY    SA.id";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function crear($datos) {

        $datos["fecha_inicio"] = fFechaSQL($datos["fecha_inicio"]);
        $datos["encargado"] = $datos["encargado"];
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        $arrayPDOParam["tipoGasto"] = self::$type["tipoGasto"];
        $arrayPDOParam["encargado"] = self::$type["encargado"];
        $arrayPDOParam["fecha_inicio"] = self::$type["fecha_inicio"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["empresa"] = self::$type["empresa"];
        $arrayPDOParam["banco"] = self::$type["banco"];
        $arrayPDOParam["cuenta"] = self::$type["cuenta"];
        $arrayPDOParam["clave"] = self::$type["clave"];
        $arrayPDOParam["obra"] = self::$type["obra"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);
    }

    public function actualizar($datos)
    {
        $datos[$this->keyName] = $this->id;
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["encargado"] = self::$type["encargado"];
        $arrayPDOParam["banco"] = self::$type["banco"];
        $arrayPDOParam["cuenta"] = self::$type["cuenta"];
        $arrayPDOParam["clave"] = self::$type["clave"];

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

    public function actualizarRequisicionId($datos)
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function cerrarGasto()
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["cerrada"] = self::$type["cerrada"];
        $arrayPDOParam["fecha_fin"] = self::$type["fecha_fin"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $datos["cerrada"] = 1;
        $datos["fecha_fin"] = date("Y-m-d H:i:s");
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"] ?? 0;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function abrirGasto()
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["cerrada"] = self::$type["cerrada"];
        $datos["cerrada"] = 0;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function cambiarEstatus()
    {
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam["cerrada"] = self::$type["cerrada"];
        $datos["cerrada"] = $this->cerrada;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function autorizarGasto()
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["usuarioIdAutorizacion"] = self::$type["usuarioIdAutorizacion"];
        $datos["usuarioIdAutorizacion"] = usuarioAutenticado()["id"] ?? 0;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    public function revisarGasto()
    {
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["usuarioIdRevision"] = self::$type["usuarioIdRevision"];

        $datos["usuarioIdRevision"] = usuarioAutenticado()["id"] ?? 0;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

}