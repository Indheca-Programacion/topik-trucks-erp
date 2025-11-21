<?php

namespace App\Models;

use App\Conexion;
use PDO;

class TrasladoDetalle
{
    static protected $fillable = [
        'traslado', 'gasto', 'proveedor', 'folio', 'total', 'descripcion', 'archivos'
    ];

    static protected $type = [
        'id' => 'integer',
        'traslado' => 'integer',
        'gasto' => 'integer',
        'proveedor' => 'string',
        'folio' => 'string',
        'total' => 'decimal',
        'descripcion' => 'string',
        'usuarioIdCreacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "traslado_detalles";

    protected $keyName = "id";

    public $id;
    public $traslado;

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
                                        INNER JOIN servicios S on S.id = T.servicio", $error);

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
                
                $this->creo = $respuesta["usuarioIdCreacion"];
            }

            return $respuesta;

        }

    }

    public function consultarPorTraslado() {

        return Conexion::queryAll($this->bdName,
                                        "SELECT TD.* 
                                        FROM $this->tableName TD
                                        WHERE TD.traslado = $this->traslado
                                        ORDER BY gasto", $error);

    }

    public function consultarArchivos() {

        return Conexion::queryAll($this->bdName,
                                        "SELECT TA.* 
                                        FROM traslado_archivos TA
                                        WHERE TA.trasladoDetalleId = $this->id", $error);

    }

    public function consultarArchivoPorTraslado() {

        return Conexion::queryAll($this->bdName,
                                        "SELECT TA.*, TD.gasto
                                        FROM traslado_archivos TA
                                        INNER JOIN traslado_detalles TD ON TD.id = TA.trasladoDetalleId
                                        WHERE TD.traslado = $this->traslado", $error);

    }

    public function crear($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["traslado"] = self::$type["traslado"];
        $arrayPDOParam["gasto"] = self::$type["gasto"];
        $arrayPDOParam["proveedor"] = self::$type["proveedor"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["total"] = self::$type["total"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];

        $datos["total"] = str_replace(',', '', $datos["total"]);

        $campos=fCreaCamposInsert($arrayPDOParam);
        $lastId= 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error,$lastId);
        
        if ($respuesta &&  isset($datos['archivos'])) {
            
            $this->id = $lastId;
            $respuesta = $this->insertarArchivos($datos['archivos']);
        }

        return $respuesta;
    }

    public function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                $directorio = "../../vistas/uploaded-files/traslados/";//Esta sobrando los ../../ ya que como se usa esta funcion en ajax, hay problemas con las rutas
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';
                if (!is_dir($directorio)) {
                    // Crear el directorio si no existe
                    mkdir($directorio, 0777, true);
                }
                
                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["trasladoDetalleId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = substr($ruta,6);
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["trasladoDetalleId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO traslado_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);//Estoy haciendo un substring por que como se usa esta funcion en ajax, hay problemas con las rutas
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

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
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

        $datos["fecha"] = fFechaSQL($datos["fecha"]);

        $columna=fCreaCamposUpdate($arrayPDOParam);
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET ".$columna." WHERE $this->keyName = :id", $datos, $arrayPDOParam, $error);

    }
}