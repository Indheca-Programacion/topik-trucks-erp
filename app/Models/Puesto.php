<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PuestoPolicy.php" ) ) {
    require_once "app/Policies/PuestoPolicy.php";
} else {
    require_once "../Policies/PuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PuestoPolicy;

class Puesto extends PuestoPolicy
{
    static protected $fillable = [
        'id', 'nombre'
    ];

    static protected $type = [
        'id' => 'integer',
        'nombre' => 'string',
        'id_puesto' => 'integer',
        'id_usuario' => 'integer',
        'id_zona' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "puesto";

    protected $keyName = "id";

    public $id = null;    
    public $nombre;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PUESTOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT P.* FROM $this->tableName P", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->nombre = $respuesta["nombre"];
            }

            return $respuesta;

        }

    }

    public function consultarPuesto($item = null, $valor = null) {

        $respuesta = Conexion::queryAll(
            $this->bdName,
            "SELECT 
                PU.id_puesto, 
                PU.id_puesto_usuario, 
                PU.id_usuario, 
                P.nombre AS puesto_usuario,
                PU.id_zona, 
                SC.descripcion AS nombre_zona
            FROM puesto_usuario PU
            INNER JOIN puesto P ON PU.id_puesto = P.id
            INNER JOIN servicio_centros SC ON PU.id_zona = SC.id
            WHERE PU.$item = $valor;",
            $error
        );

        $puestos_limpios = [];
        foreach ($respuesta as $fila) {
            $puestos_limpios[] = [
                "id_puesto_usuario" => $fila["id_puesto_usuario"],
                "id_puesto" => $fila["id_puesto"],
                "id_usuario" => $fila["id_usuario"],
                "id_zona" => $fila["id_zona"],
                "nombre_zona" => $fila["nombre_zona"],
                "puesto_usuario" => $fila["puesto_usuario"]
            ];
        }
         // Retornar como JSON
        return $puestos_limpios;


    }

    

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["nombre"] = self::$type["nombre"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (nombre) VALUES ( :nombre)", $datos, $arrayPDOParam, $error);

    }

    public function crearPuestoUsuario($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["id_puesto"] = self::$type["id_puesto"];
        $arrayPDOParam["id_usuario"] = self::$type["id_usuario"];
        $arrayPDOParam["id_zona"] = self::$type["id_zona"];

        return Conexion::queryExecute($this->bdName, "INSERT INTO puesto_usuario (id_puesto,id_usuario,id_zona) VALUES (:id_puesto, :id_usuario, :id_zona)", $datos, $arrayPDOParam, $error);

    }

    // VERIFICAR SI YA EXISTE EL PUESTO ASIGNADO
    public function verificarPuestoUsuario($datos) {
        

        $id_puesto = $datos['id_puesto'];
        $id_usuario = $datos['id_usuario'];
        $id_zona = $datos['id_zona'];

        $respuesta = Conexion::queryAll(
            $this->bdName,
            "SELECT 
            *
            FROM puesto_usuario PU
            WHERE PU.id_puesto = $id_puesto
            AND PU.id_usuario = $id_usuario
            AND PU.id_zona = $id_zona",
            $error
        );

        return $respuesta;

    }



    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["nombre"] = self::$type["nombre"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET  nombre = :nombre WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar($deletePuestoUsuario) {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];


        if($deletePuestoUsuario){

            $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM puesto_usuario WHERE id_puesto_usuario = :id", $datos, $arrayPDOParam, $error);
        }

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;
    }
}
