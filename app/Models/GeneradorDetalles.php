<?php

namespace App\Models;

if ( file_exists ( "app/Policies/GeneradorDetallesPolicy.php" ) ) {
    require_once "app/Policies/GeneradorDetallesPolicy.php";
} else {
    require_once "../Policies/GeneradorDetallesPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\GeneradorDetallesPolicy;

class GeneradorDetalles extends GeneradorDetallesPolicy
{
    static protected $fillable = [
        'fk_generador', 'fechaInicio','fk_maquinaria', 'id', 'laborados', 'fallas', 'paros', 'entrega', 'descanso', 'clima'
    ];

    static protected $type = [
        'id' => 'integer',
        'fk_maquinaria' => 'integer',
        'fk_generador' => 'integer',
        'fechaInicio' => 'date',
        'laborados' => 'string',
        'fallas' => 'string',
        'paros' => 'string',
        'entrega' => 'string',
        'descanso' => 'string',
        'clima' => 'string',
        'diaParcial' => 'string',

    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "generador_detalles";

    protected $keyName = "id";

    public $id = null;
    public $codigo;
    public $nombre;
    public $descripcion;
    public $maquinaria;
    public $generador;
    public $fecha_inicio;


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

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
                
            }

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->maquinaria = $respuesta["fk_maquinaria"];
                $this->generador = $respuesta["fk_generador"];
                $this->fecha_inicio = $respuesta["fechaInicio"];
                
            }

            return $respuesta;

        }

    }

    public function consultarDetalles($generadorId){
        return Conexion::queryAll($this->bdName,"SELECT 
                                                M.id, 
                                                GD.id as generadorId, 
                                                M.numeroEconomico, 
                                                M.descripcion AS equipo, 
                                                MA.descripcion AS marca, 
                                                MO.descripcion AS modelo, 
                                                M.serie, 
                                                GD.fechaInicio AS fecha,
                                                GD.laborados, 
                                                GD.fallas, 
                                                GD.paros, 
                                                GD.clima, 
                                                GD.diaParcial, 
                                                M.empresaId
                                                FROM $this->tableName GD
                                                INNER JOIN generadores G ON G.id = GD.fk_generador
                                                INNER JOIN maquinarias M ON M.id = GD.fk_maquinaria
                                                INNER JOIN modelos MO ON MO.id = M.modeloId
                                                INNER JOIN marcas MA ON MO.marcaId = MA.id
                                                WHERE G.id = $generadorId");
    }

    public function consultarEstimaciones($id)
    {
        return Conexion::queryAll($this->bdName,
            "SELECT GD.id, M.empresaId, GD.fechaInicio as fecha, 
            M.numeroEconomico, M.descripcion AS equipo, MA.descripcion AS marca, MO.descripcion AS modelo,
            CONCAT(MT.descripcion, ' | ', MA.descripcion, ' | ', MO.descripcion) AS descripcion,
            GD.laborados, GD.fallas, GD.paros, GD.clima, 
                COALESCE(E.costo, 0) AS costo,
                COALESCE(E.pu, 0) AS pu,
                COALESCE(E.operacion, 0) AS operacion,
                COALESCE(E.comb, 0) AS comb,
                COALESCE(E.mantto, 0) AS mantto,
                COALESCE(E.flete, 0) AS flete,
                COALESCE(E.ajuste, 0) AS ajuste
            FROM $this->tableName GD
            INNER JOIN maquinarias M ON M.id = GD.fk_maquinaria
            INNER JOIN modelos MO ON MO.id = M.modeloId
            INNER JOIN marcas MA ON MO.marcaId = MA.id
            inner join maquinaria_tipos MT ON MT.id = M.maquinariatipoId
            LEFT JOIN estimaciones E ON E.generador_detalle_id = GD.id
            WHERE GD.fk_generador = $id
            ORDER BY M.empresaId");
    }

    public function consultarDesempeno($id)
    {
        return Conexion::queryAll($this->bdName,
            "SELECT GD.id, M.numeroEconomico ,GD.laborados, GD.fallas, GD.paros, GD.clima, 
                COALESCE(D.hmr, 0) AS hmr,		
                COALESCE(D.rr, 0) AS rr,
                COALESCE(D.lcc, 0) AS lcc,
                COALESCE(D.observaciones, '') AS observaciones
            FROM $this->tableName GD
            INNER JOIN maquinarias M ON M.id = GD.fk_maquinaria
            LEFT JOIN desempeno D ON D.generador_detalle = GD.id
            WHERE fk_generador = $id ");
    }

    public function crear($datos) {
        
        $arrayPDOParam = array();
        $arrayPDOParam["fk_maquinaria"] = self::$type["fk_maquinaria"];
        $arrayPDOParam["fechaInicio"] = self::$type["fechaInicio"];
        $arrayPDOParam["fk_generador"] = self::$type["fk_generador"];

        $columna=fCreaCamposInsert($arrayPDOParam);
        $datos["fechaInicio"] =$datos["fechaInicio"];

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$columna, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el reporte
            $this->id = $lastId;
            $this->fk_maquinaria = $datos["fk_maquinaria"];
            $this->actualizarUbicacionObra();
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["fk_maquinaria"] = self::$type["fk_maquinaria"];
        $arrayPDOParam["fechaInicio"] = self::$type["fechaInicio"];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET fechaInicio = :fechaInicio, fk_maquinaria = :fk_maquinaria WHERE id = :id", $datos, $arrayPDOParam, $error);

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

    public function updateIncidencias($datos)
    {

        $arrayPDOParam = array();
        $arrayPDOParam["laborados"] = self::$type["laborados"];
        $arrayPDOParam["fallas"] = self::$type["fallas"];
        $arrayPDOParam["paros"] = self::$type["paros"];
        $arrayPDOParam["clima"] = self::$type["clima"];
        $arrayPDOParam["diaParcial"] = self::$type["diaParcial"];
        
        $campos = fCreaCamposUpdate($arrayPDOParam);
        
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . "  WHERE id = :id", $datos, $arrayPDOParam, $error);
        return $respuesta;
    }

    public function obtenerIncidencias($mes,$maquinariaId){
        return Conexion::queryAll($this->bdName, 
        "SELECT GD.laborados, GD.fallas, GD.paros, GD.clima
        FROM $this->tableName GD
        INNER JOIN generadores G ON G.id = GD.fk_generador
        WHERE GD.fk_maquinaria = $maquinariaId AND G.mes = '$mes'
        ", $error);
    }

    public function actualizarUbicacionObra()
    {
        $datos = array();
        $datos["id"] = $this->fk_maquinaria;
        $datos["ubicacionId"] = $this->ubicacionId;
        $datos["obraId"] = $this->obraId;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["ubicacionId"] = 'integer';
        $arrayPDOParam["obraId"] = 'integer';

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE maquinarias SET ubicacionId = :ubicacionId, obraId = :obraId WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }
}
