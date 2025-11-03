<?php

namespace App\Models;

if ( file_exists ( "app/Policies/TrasladoPolicy.php" ) ) {
    require_once "app/Policies/TrasladoPolicy.php";
} else {
    require_once "../Policies/TrasladoPolicy.php";
}

use App\Policies\TrasladoPolicy;
use App\Conexion;
use PDO;

class Traslado extends TrasladoPolicy
{
    static protected $fillable = [
        'operador','maquinaria', 'ruta', 'fecha', 'kmInicial', 'kmFinal', 'kmRecorrido', 'combustibleInicial', 'combustibleFinal', 'combustibleGastado', 'rendimientoTeorico', 'rendimientoReal', 'deposito', 'empresa', 'estatus'
    ];

    static protected $type = [
        'id' => 'integer',
        'operador' => 'integer',
        'maquinaria' => 'integer',
        'ruta' => 'string',
        'fecha' => 'date',
        'kmInicial' => 'decimal',
        'kmFinal' => 'decimal',
        'kmRecorrido' => 'decimal',
        'combustibleInicial' => 'decimal',
        'combustibleFinal' => 'decimal',
        'combustibleGastado' => 'decimal',
        'rendimientoTeorico' => 'decimal',
        'rendimientoReal' => 'decimal',
        'deposito' => 'decimal',
        'usuarioIdCreacion' => 'integer',
        'empresa' => 'integer',
        'servicio' => 'integer',
        'estatus' => 'integer',

    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "traslados";

    protected $keyName = "id";

    public $id;
    public $operador;
    public $maquinaria;
    public $ruta;
    public $fecha;
    public $kmInicial;
    public $kmFinal;
    public $kmRecorrido;
    public $combustibleInicial;
    public $combustibleFinal;
    public $combustibleGastado;
    public $rendimientoTeorico;
    public $rendimientoReal;
    public $deposito;
    public $usuarioIdCreacion;
    public $fechaCreacion;
    public $fechaActualizacion;

    public function __construct(Type $var = null) {
        $this->requisiciones = null;
    }

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName,
                                        "SELECT T.*, CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS creo, 
                                            CONCAT(O.nombre, ' ', O.apellidoPaterno, ' ', IFNULL(O.apellidoMaterno, '')) AS operador,
                                            M.numeroEconomico, S.folio as 'servicio.folio'
                                        FROM $this->tableName T
                                        INNER JOIN usuarios US ON US.id = T.usuarioIdCreacion
                                        INNER JOIN empleados O ON O.id = T.operador
                                        INNER JOIN maquinarias M ON M.id = T.maquinaria
                                        INNER JOIN servicios S on S.id = T.servicio
                                        ORDER BY T.id desc", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT* FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->operador = $respuesta["operador"];
                $this->maquinaria = $respuesta["maquinaria"];
                $this->ruta = mb_strtoupper($respuesta["ruta"]);
                $this->fecha = fFechaLarga($respuesta["fecha"]);
                $this->kmInicial = $respuesta["kmInicial"];
                $this->kmFinal = $respuesta["kmFinal"];
                $this->kmRecorrido = $respuesta["kmRecorrido"];
                $this->combustibleInicial = $respuesta["combustibleInicial"];
                $this->combustibleFinal = $respuesta["combustibleFinal"];
                $this->combustibleGastado = $respuesta["combustibleGastado"];
                $this->rendimientoTeorico = $respuesta["rendimientoTeorico"];
                $this->rendimientoReal = $respuesta["rendimientoReal"];
                $this->deposito = $respuesta["deposito"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->fechaCreacion = fFechaLarga($respuesta["fechaCreacion"]);
                $this->fechaActualizacion = fFechaLarga($respuesta["fechaActualizacion"]);
                $this->empresa = $respuesta["empresa"];
                $this->servicio = $respuesta["servicio"];
                $this->estatus = $respuesta["estatus"];
                
                $this->creo = $respuesta["usuarioIdCreacion"];
            }

            return $respuesta;

        }

    }

    public function consultarRequisiciones() {

        $this->requisiciones = Conexion::queryAll($this->bdName,
                                        "SELECT R.*, SE.descripcion as 'servicio_estatus.descripcion'
                                        FROM requisiciones R
                                        INNER JOIN servicios S ON S.id = R.servicioId
                                        INNER JOIN servicio_estatus SE ON SE.id = R.servicioEstatusId
                                        WHERE S.id = $this->servicio", $error);

    }

    public function consultarArchivos() {

        return Conexion::queryAll($this->bdName,
                                        "SELECT A.*
                                        FROM traslado_archivos A
                                        inner join traslado_detalles TD ON TD.id = A.trasladoDetalleId
                                        inner join traslados T ON T.id = TD.traslado
                                        WHERE T.id = $this->id", $error);

    }

    public function crear($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["operador"] = self::$type["operador"];
        $arrayPDOParam["maquinaria"] = self::$type["maquinaria"];
        $arrayPDOParam["ruta"] = self::$type["ruta"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["kmInicial"] = self::$type["kmInicial"];
        $arrayPDOParam["kmFinal"] = self::$type["kmFinal"];
        $arrayPDOParam["kmRecorrido"] = self::$type["kmRecorrido"];
        $arrayPDOParam["combustibleInicial"] = self::$type["combustibleInicial"];
        $arrayPDOParam["combustibleFinal"] = self::$type["combustibleFinal"];
        $arrayPDOParam["combustibleGastado"] = self::$type["combustibleGastado"];
        $arrayPDOParam["rendimientoTeorico"] = self::$type["rendimientoTeorico"];
        $arrayPDOParam["rendimientoReal"] = self::$type["rendimientoReal"];
        $arrayPDOParam["deposito"] = self::$type["deposito"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        $arrayPDOParam["empresa"] = self::$type["empresa"];
        $arrayPDOParam["servicio"] = self::$type["servicio"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
        $datos["fecha"] = fFechaSQL($datos["fecha"]);

        $columna=fCreaCamposInsert($arrayPDOParam);
        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$columna, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos)
    {
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam["operador"] = self::$type["operador"];
        $arrayPDOParam["maquinaria"] = self::$type["maquinaria"];
        $arrayPDOParam["ruta"] = self::$type["ruta"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["kmInicial"] = self::$type["kmInicial"];
        $arrayPDOParam["kmFinal"] = self::$type["kmFinal"];
        $arrayPDOParam["kmRecorrido"] = self::$type["kmRecorrido"];
        $arrayPDOParam["combustibleInicial"] = self::$type["combustibleInicial"];
        $arrayPDOParam["combustibleFinal"] = self::$type["combustibleFinal"];
        $arrayPDOParam["combustibleGastado"] = self::$type["combustibleGastado"];
        $arrayPDOParam["rendimientoTeorico"] = self::$type["rendimientoTeorico"];
        $arrayPDOParam["rendimientoReal"] = self::$type["rendimientoReal"];
        $arrayPDOParam["deposito"] = self::$type["deposito"];
        $arrayPDOParam["empresa"] = self::$type["empresa"];
        $arrayPDOParam["estatus"] = self::$type["estatus"];

        $datos["fecha"] = fFechaSQL($datos["fecha"]);

        $columna=fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET ".$columna." WHERE $this->keyName = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar($id)
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

}