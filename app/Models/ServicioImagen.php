<?php

namespace App\Models;

use App\Conexion;
use App\Route;
use PDO;

class ServicioImagen
{
	static protected $fillable = [
        'id', 'servicioId', 'titulo', 'archivo', 'formato', 'ruta'
    ];

    static protected $type = [
        'id' => 'integer',
        'servicioId' => 'integer',
        'titulo' => 'string',
        'archivo' => 'string',
        'formato' => 'string',
        'ruta' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "servicio_imagenes";

    protected $keyName = "id";

    public $id = null;
    public $servicioId;
    public $titulo;
    public $archivo;
    public $formato;
    public $ruta;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR SERVICIO IMAGEN
    =============================================*/
    public function consultar()
    {
        $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $this->id AND servicioId = $this->servicioId", $error);

        if ( $respuesta ) {
            $this->id = $respuesta["id"];
            $this->servicioId = $respuesta["servicioId"];
            $this->titulo = $respuesta["titulo"];
            $this->archivo = $respuesta["archivo"];
            $this->formato = $respuesta["formato"];
            $this->ruta = $respuesta["ruta"];
        }

        return $respuesta;
    }

    public function crear($datos)
    {
        return; // No se utiliza
    }

    public function actualizar($datos)
    {
        return; // No se utiliza
    }

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos["id"] = $this->id;
        $datos["servicioId"] = $this->servicioId;
        
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type[$this->keyName];
        $arrayPDOParam["servicioId"] = self::$type["servicioId"];

        $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id AND servicioId = :servicioId", $datos, $arrayPDOParam, $error);

        if ( $respuesta && !is_null($this->ruta) ) {
            // Eliminar físicamente el archivo (si tiene)
            // unlink($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
            fDeleteFile($_SERVER['DOCUMENT_ROOT'].CONST_APP_FOLDER.$this->ruta); // Ruta absoluta al ser llamado desde JS
        }

        return $respuesta;
    }
}
