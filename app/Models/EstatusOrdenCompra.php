<?php

namespace App\Models;

if ( file_exists ( "app/Policies/EstatusOrdenCompraPolicy.php" ) ) {
    require_once "app/Policies/EstatusOrdenCompraPolicy.php";
} else {
    require_once "../Policies/EstatusOrdenCompraPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\EstatusOrdenCompraPolicy;

class EstatusOrdenCompra extends EstatusOrdenCompraPolicy
{
    static protected $fillable = [
        'descripcion', 'nombreCorto', 'colorTexto', 'colorFondo', 'obraAbierta', 'obraCerrada', 'requisicionAbierta', 'requisicionCerrada', 'requisicionOrden', 'requisicionAgregarPartidas', 'requisicionUsuarioCreacion', 'ordenCompraAbierta', 'ordenCompraCerrada'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string',
        'colorTexto' => 'string',
        'colorFondo' => 'string',
        'obraAbierta' => 'integer',
        'obraCerrada' => 'integer',
        'requisicionAbierta' => 'integer',
        'requisicionCerrada' => 'integer',
        'requisicionOrden' => 'integer',
        'requisicionAgregarPartidas' => 'integer',
        'requisicionUsuarioCreacion' => 'integer',
        'ordenCompraAbierta' => 'integer',
        'ordenCompraCerrada' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "estatus_orden_compra";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;
    public $colorTexto;
    public $colorFondo;
    public $obraAbierta;
    public $obraCerrada;
    public $requisicionAbierta;
    public $requisicionCerrada;
    public $requisicionOrden;
    public $requisicionAgregarPartidas;
    public $requisicionUsuarioCreacion;
    public $ordenCompraAbierta;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR ESTATUS DE SERVICIO
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT SE.* FROM $this->tableName SE ORDER BY SE.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);
            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"] ?? null;
                $this->descripcion = $respuesta["descripcion"] ?? "";
                $this->nombreCorto = $respuesta["nombreCorto"] ?? "";
                $this->colorTexto = $respuesta["colorTexto"] ?? "";
                $this->colorFondo = $respuesta["colorFondo"] ?? "";
                $this->obraAbierta = $respuesta["obraAbierta"] ?? 0;
                $this->obraCerrada = $respuesta["obraCerrada"] ?? 0;
                $this->requisicionAbierta = $respuesta["requisicionAbierta"] ?? 0;
                $this->requisicionCerrada = $respuesta["requisicionCerrada"] ?? 0;
                $this->requisicionOrden = $respuesta["requisicionOrden"] ?? 0;
                $this->ordenCompraAbierta = $respuesta["ordenCompraAbierta"] ?? 0;
                $this->requisicionAgregarPartidas = $respuesta["requisicionAgregarPartidas"] ?? 0;
                $this->requisicionUsuarioCreacion = $respuesta["requisicionUsuarioCreacion"] ?? "";
            }

            return $respuesta;
        }

    }

    public function crear($datos) {

        // Modificar el contenido de los checkboxes
        $datos["obraAbierta"] = ( isset($datos["obraAbierta"]) && mb_strtolower($datos["obraAbierta"]) == "on" ) ? "1" : "0";
        $datos["obraCerrada"] = ( isset($datos["obraCerrada"]) && mb_strtolower($datos["obraCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAbierta"] = ( isset($datos["requisicionAbierta"]) && mb_strtolower($datos["requisicionAbierta"]) == "on" ) ? "1" : "0";
        $datos["requisicionCerrada"] = ( isset($datos["requisicionCerrada"]) && mb_strtolower($datos["requisicionCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAgregarPartidas"] = ( isset($datos["requisicionAgregarPartidas"]) && mb_strtolower($datos["requisicionAgregarPartidas"]) == "on" ) ? "1" : "0";
        $datos["requisicionUsuarioCreacion"] = ( isset($datos["requisicionUsuarioCreacion"]) && mb_strtolower($datos["requisicionUsuarioCreacion"]) == "on" ) ? "1" : "0";
        $datos["ordenCompraAbierta"] = ( isset($datos["ordenCompraAbierta"]) && mb_strtolower($datos["ordenCompraAbierta"]) == "on" ) ? "1" : "0";

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["colorTexto"] = self::$type["colorTexto"];
        $arrayPDOParam["colorFondo"] = self::$type["colorFondo"];
        $arrayPDOParam["obraAbierta"] = self::$type["obraAbierta"];
        $arrayPDOParam["obraCerrada"] = self::$type["obraCerrada"];
        $arrayPDOParam["requisicionAbierta"] = self::$type["requisicionAbierta"];
        $arrayPDOParam["requisicionCerrada"] = self::$type["requisicionCerrada"];
        $arrayPDOParam["requisicionOrden"] = self::$type["requisicionOrden"];
        $arrayPDOParam["requisicionAgregarPartidas"] = self::$type["requisicionAgregarPartidas"];
        $arrayPDOParam["requisicionUsuarioCreacion"] = self::$type["requisicionUsuarioCreacion"];
        $arrayPDOParam["ordenCompraAbierta"] = self::$type["ordenCompraAbierta"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Modificar el contenido de los checkboxes
        $datos["obraAbierta"] = ( isset($datos["obraAbierta"]) && mb_strtolower($datos["obraAbierta"]) == "on" ) ? "1" : "0";
        $datos["obraCerrada"] = ( isset($datos["obraCerrada"]) && mb_strtolower($datos["obraCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAbierta"] = ( isset($datos["requisicionAbierta"]) && mb_strtolower($datos["requisicionAbierta"]) == "on" ) ? "1" : "0";
        $datos["requisicionCerrada"] = ( isset($datos["requisicionCerrada"]) && mb_strtolower($datos["requisicionCerrada"]) == "on" ) ? "1" : "0";
        $datos["requisicionAgregarPartidas"] = ( isset($datos["requisicionAgregarPartidas"]) && mb_strtolower($datos["requisicionAgregarPartidas"]) == "on" ) ? "1" : "0";
        $datos["requisicionUsuarioCreacion"] = ( isset($datos["requisicionUsuarioCreacion"]) && mb_strtolower($datos["requisicionUsuarioCreacion"]) == "on" ) ? "1" : "0";
        $datos["ordenCompraAbierta"] = ( isset($datos["ordenCompraAbierta"]) && mb_strtolower($datos["ordenCompraAbierta"]) == "on" ) ? "1" : "0";
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["colorTexto"] = self::$type["colorTexto"];
        $arrayPDOParam["colorFondo"] = self::$type["colorFondo"];
        $arrayPDOParam["obraAbierta"] = self::$type["obraAbierta"];
        $arrayPDOParam["obraCerrada"] = self::$type["obraCerrada"];
        $arrayPDOParam["requisicionAbierta"] = self::$type["requisicionAbierta"];
        $arrayPDOParam["requisicionCerrada"] = self::$type["requisicionCerrada"];
        $arrayPDOParam["requisicionOrden"] = self::$type["requisicionOrden"];
        $arrayPDOParam["requisicionAgregarPartidas"] = self::$type["requisicionAgregarPartidas"];
        $arrayPDOParam["requisicionUsuarioCreacion"] = self::$type["requisicionUsuarioCreacion"];
        $arrayPDOParam["ordenCompraAbierta"] = self::$type["ordenCompraAbierta"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

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
