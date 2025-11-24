<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PresupuestoPolicy.php" ) ) {
    require_once "app/Policies/PresupuestoPolicy.php";
} else {
    require_once "../Policies/PresupuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PresupuestoPolicy;

class Presupuesto extends PresupuestoPolicy
{
    static protected $fillable = [
        'maquinariaId', 'clienteId', 'fuente', 'fechaSolicitud', 'ubicacion', 'horasProyectadas', 'estatusId', 'mantenimientoTipoId', 'servicioTipoId', 'descripcion'
    ];

    static protected $type = [
        'id' => 'integer',
        'maquinariaId' => 'integer',
        'clienteId' => 'integer',
        'fuente' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "presupuestos";

    protected $keyName = "id";

    public $id = null;
    public $maquinariaId = null;
    public $clienteId = null;
    public $fuente = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PRESUPUESTOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->clienteId = $respuesta["clienteId"];
                $this->fuente = $respuesta["fuente"];

            }

            return $respuesta;

        }

    }

    public function crear($datos,$imagenes = null) {

        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["clienteId"] = self::$type["clienteId"];
        $arrayPDOParam["fuente"] = self::$type["fuente"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos , $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {
            $this->id = $lastId;
            $this->maquinariaId = $datos["maquinariaId"];
            $this->crearServicios($datos, $imagenes);

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["clienteId"] = self::$type["clienteId"];
        $arrayPDOParam["fuente"] = self::$type["fuente"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    function crearServicios($datos, $imagenes = null) {

        if ( isset($datos["mantenimientoTipoId"]) && is_array($datos["mantenimientoTipoId"]) ) {

            foreach ( $datos["mantenimientoTipoId"] as $index => $mantenimientoTipoId ) {
                
                $arrayPDOParam = array();
                $arrayPDOParam["presupuestoId"] = 'integer';
                $arrayPDOParam["mantenimientoTipoId"] = 'integer';
                $arrayPDOParam["servicioTipoId"] = 'integer';
                $arrayPDOParam["descripcion"] = 'string';
                $arrayPDOParam["servicioEstatusId"] = 'integer';
                $arrayPDOParam["maquinariaId"] = 'integer';
                $arrayPDOParam["usuarioIdCreacion"] = 'integer';
                
                $datosInsert = array();
                $datosInsert["mantenimientoTipoId"] = $mantenimientoTipoId;
                $datosInsert["servicioTipoId"] = $datos["servicioTipoId"][$index];
                $datosInsert["descripcion"] = $datos["descripcion"][$index];
                
                $datosInsert["presupuestoId"] = $this->id;
                $datosInsert["servicioEstatusId"] = 1;
                $datosInsert["maquinariaId"] = $this->maquinariaId;
                $datosInsert["usuarioIdCreacion"] = usuarioAutenticado()["id"];
                $campos = fCreaCamposInsert($arrayPDOParam);
                
                $lastId = 0;
                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO servicios ".$campos , $datosInsert, $arrayPDOParam, $error, $lastId);

                if ( $respuesta ) {

                    // Insertar las imágenes asociadas a este servicio
                    if ( isset($imagenes["imagenes_" . ($index+1)]) ) {
                        $this->insertarImagenes($imagenes["imagenes_" . ($index+1)], $lastId);
                    }

                }

            }

        }

    }

    function insertarImagenes($archivos, $servicioId) {

        for ($i = 0; $i < count($archivos['name']); $i++) {

            if ( $archivos["tmp_name"][$i] == "" ) continue;

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            // if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/servicios/imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );

            // }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["servicioId"] = $servicioId;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();
            $arrayPDOParam["servicioId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO servicio_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            $respuesta = true;
            if ( $respuesta && $ruta != "" ) {
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }

        return $respuesta;

    }
}
