<?php

namespace App\Models;

use App\Conexion;
use App\Route;
use PDO;

class MaquinariaHorometro
{
    static protected $fillable = [
        'maquinariaId', 'fecha', 'horometroInicial', 'kilometrajeInicial', 'horometroFinal', 'kilometrajeFinal', 'archivo', 'formato', 'ruta'
    ];

    static protected $type = [
        // 'id' => 'integer',
        'maquinariaId' => 'integer',
        'fecha' => 'date',
        'horometroInicial' => 'integer',
        'kilometrajeInicial' => 'integer',
        'horometroFinal' => 'integer',
        'kilometrajeFinal' => 'integer',
        'archivo' => 'string',
        'formato' => 'string',
        'ruta' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "maquinaria_horometros";

    // protected $keyName = "id";

    // public $id = null;
    public $maquinariaId;
    public $fecha;
    public $horometroInicial;
    public $kilometrajeInicial;
    public $horometroFinal;
    public $kilometrajeFinal;
    public $archivo;
    public $formato;
    public $ruta;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MAQUINARIA HOROMETRO
    =============================================*/
    public function consultar($fechaFormatoSQL = false) {

        // Convertir los campos date (fechaLarga) a formato SQL
        $fecha = ( $fechaFormatoSQL ) ? $this->fecha : fFechaSQL($this->fecha);

        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE maquinariaId = $this->maquinariaId AND fecha = '$fecha'", $error);

        if ( $respuesta ) {
            $this->maquinariaId = $respuesta["maquinariaId"];
            $this->fecha = $respuesta["fecha"];
            $this->horometroInicial = $respuesta["horometroInicial"];
            $this->kilometrajeInicial = $respuesta["kilometrajeInicial"];
            $this->horometroFinal = $respuesta["horometroFinal"];
            $this->kilometrajeFinal = $respuesta["kilometrajeFinal"];
            $this->archivo = $respuesta["archivo"];
            $this->formato = $respuesta["formato"];
            $this->ruta = $respuesta["ruta"];
        }

        return $respuesta;

    }

    public function crear($datos) {

        // Agregar al request para crear el registro
        $datos["maquinariaId"] = $this->maquinariaId;

        // Agregar al request el nombre, formato y ruta final del archivo
        $ruta = "";
        if ( $datos["archivo"]["tmp_name"] != "" ) {

            $archivo = $datos["archivo"]["name"];
            $tipo = $datos["archivo"]["type"];
            $tmp_name = $datos["archivo"]["tmp_name"];

            // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
            $directorio = "vistas/uploaded-files/maquinaria-horometros/";
            // $aleatorio = mt_rand(10000000,99999999);
            $extension = '';

            // if ( $datos["archivo"]["type"] == "application/msword" ) $extension = ".doc";
            // elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ) $extension = ".docx";
            // elseif ( $datos["archivo"]["type"] == "application/vnd.ms-excel" ) $extension = ".xls";
            // elseif ( $datos["archivo"]["type"] == "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" ) $extension = ".xlsx";
            // else
            if ( $datos["archivo"]["type"] == "application/pdf" ) $extension = ".pdf";
            // elseif ( $datos["archivo"]["type"] == "image/jpeg" ) $extension = ".jpg";
            // elseif ( $datos["archivo"]["type"] == "image/png" ) $extension = ".png";

            if ( $extension != '') {
                // $ruta = $directorio.$aleatorio.$extension;
                do {
                    $ruta = fRandomNameFile($directorio, $extension);
                } while ( file_exists($ruta) );
                // move_uploaded_file($tmp_name, $ruta);
            }

        }
        // Request con el nombre del archivo
        $datos["archivo"] = $archivo;
        $datos["formato"] = $tipo;
        $datos["ruta"] = $ruta;

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fecha"] = fFechaSQL($datos["fecha"]);

        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["horometroInicial"] = self::$type["horometroInicial"];
        $arrayPDOParam["kilometrajeInicial"] = self::$type["kilometrajeInicial"];
        $arrayPDOParam["horometroFinal"] = self::$type["horometroFinal"];
        $arrayPDOParam["kilometrajeFinal"] = self::$type["kilometrajeFinal"];
        $arrayPDOParam["archivo"] = self::$type["archivo"];
        $arrayPDOParam["formato"] = self::$type["formato"];
        $arrayPDOParam["ruta"] = self::$type["ruta"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error);

        if ( $respuesta && $ruta != "" ) {
            // move_uploaded_file($tmp_name, $ruta);
            move_uploaded_file($tmp_name, $_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        return; // No se utiliza

        // Agregar al request para actualizar el registro
        $datos["maquinariaId"] = $this->maquinariaId;

        // Convertir los campos date (fechaLarga) a formato SQL
        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        
        $arrayPDOParam = array();
        $arrayPDOParam["horometroInicial"] = self::$type["horometroInicial"];
        $arrayPDOParam["kilometrajeInicial"] = self::$type["kilometrajeInicial"];
        $arrayPDOParam["horometroFinal"] = self::$type["horometroFinal"];
        $arrayPDOParam["kilometrajeFinal"] = self::$type["kilometrajeFinal"];

        // return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, orden = :orden WHERE id = :id", $datos, $arrayPDOParam, $error);

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE maquinariaId = :maquinariaId AND fecha = :fecha", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos["maquinariaId"] = $this->maquinariaId;

        // Convertir los campos date (fechaLarga) a formato SQL
        // $datos["fecha"] = fFechaSQL($datos["fecha"]);
        $datos["fecha"] = $this->fecha;
        
        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE maquinariaId = :maquinariaId AND fecha = :fecha", $datos, $arrayPDOParam, $error);

        if ( $respuesta && !is_null($this->ruta) ) {
            // Eliminar físicamente el archivo (si tiene)
            // unlink($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
            fDeleteFile($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;

    }
}
