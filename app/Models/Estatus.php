<?php

namespace App\Models;

if ( file_exists ( "app/Policies/EstatusPolicy.php" ) ) {
    require_once "app/Policies/EstatusPolicy.php";
} else {
    require_once "../Policies/EstatusPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\EstatusPolicy;

class Estatus extends EstatusPolicy
{
    static protected $fillable = [
        'descripcion', 'nombreCorto'
    ];

    static protected $type = [
        'id' => 'integer',
        'descripcion' => 'string',
        'nombreCorto' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "estatus";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR ESTATUS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT E.* FROM $this->tableName E ORDER BY E.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
            }

            return $respuesta;

        }

    }

    public function consultarEstatusOrdenCompra($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT EOC.* FROM estatus_orden_compra EOC ORDER BY EOC.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM estatus_orden_compra WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM estatus_orden_compra WHERE $item = '$valor'", $error);

            }            
            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->descripcion = $respuesta["descripcion"];
                $this->nombreCorto = $respuesta["nombreCorto"];
                $this->colorTexto = $respuesta["colorTexto"];
                $this->colorFondo = $respuesta["colorFondo"];
                $this->obraAbierta = $respuesta["obraAbierta"];
                $this->obraCerrada = $respuesta["obraCerrada"];
                $this->requisicionAbierta = $respuesta["requisicionAbierta"];
                $this->requisicionCerrada = $respuesta["requisicionCerrada"];
                $this->requisicionOrden = $respuesta["requisicionOrden"];
                $this->requisicionAgregarPartidas = $respuesta["requisicionAgregarPartidas"];
                $this->requisicionUsuarioCreacion = $respuesta["requisicionUsuarioCreacion"];
            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (descripcion, nombreCorto) VALUES (:descripcion, :nombreCorto)", $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto WHERE id = :id", $datos, $arrayPDOParam, $error);

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
