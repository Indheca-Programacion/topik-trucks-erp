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

class GastoDetalles extends GastosPolicy
{
    static protected $fillable = [
        'fecha', 'tipoGasto', 'maquinaria', 'ubicacion', 'numeroParte', 'costo', 'cantidad', 'unidad', 'proveedor', 'factura', 'observaciones', 'gastoId', 'archivos', 'obra', 'solicito'
    ];

    static protected $type = [
        'id' => 'integer',
        'fecha' => 'date',
        'tipoGasto' => 'integer',
        'maquinaria' => 'integer',
        'ubicacion' => 'integer',
        'numeroParte' => 'string',
        'costo' => 'float',
        'cantidad' => 'integer',
        'unidad' => 'string',
        'proveedor' => 'string',
        'factura' => 'string',
        'observaciones' => 'string',
        'gastoId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'obra' => 'integer',
        'observaciones_detalles' => 'string',
        'cancelada' => 'integer',
        'solicito' => 'string',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "gasto_detalles";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($item = null, $valor = null)
    {
        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT G.*,  CONCAT(E.nombre, ' ', E.apellidoPaterno, ' ', IFNULL(E.apellidoMaterno, '')) AS nombreCompleto, G.fecha_inicio
                                                    FROM  $this->tableName G
                                                    INNER JOIN empleados E ON E.id = G.encargado", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->obra = $respuesta["obra"];
                $this->tipoGasto = $respuesta["tipoGasto"];
                $this->banco = $respuesta["banco"];
                $this->cuenta = $respuesta["cuenta"];
                $this->clave = $respuesta["clave"];
                $this->encargado = $respuesta["encargado"];
                $this->fecha_inicio = $respuesta["fecha_inicio"];
                $this->fecha_fin = $respuesta["fecha_fin"];
                $this->fecha_envio = $respuesta["fecha_envio"];
                
            }

            return $respuesta;

        }
    }

    public function consultarPorGasto($id)
    {
        return Conexion::queryAll($this->bdName,
            "SELECT GD.*, M.numeroEconomico,
                case 
                    when tipoGasto = 1 then 'Herrmienta Menor'
                    when tipoGasto = 2 then 'Mantto. Preventivo'
                    when tipoGasto = 3 then 'Mantto. Correctivo'
                    when tipoGasto = 4 then 'Material de Limpieza'
                    when tipoGasto = 5 then 'Material Primeros Auxilios'
                    when tipoGasto = 6 then 'Mobiliario de Oficina'
                    when tipoGasto = 7 then 'Gastos Generales'
                    when tipoGasto = 8 then 'Materiales'
                END AS tipoGasto,
                O.nombreCorto as 'descripcion'
                FROM  $this->tableName GD
                INNER JOIN maquinarias M ON M.id = GD.maquinaria
                left join obras O on O.id = GD.obra
                WHERE GD.gastoId = $id");
    }

    public function consultarArchivos($id){
        $query = "SELECT    SA.*
                FROM        gasto_archivos SA
                WHERE       SA.gastoDetalleId = {$id}
                ORDER BY    SA.id";

        return Conexion::queryAll($this->bdName, $query, $error);
    }

    public function crear($datos) {

        // Agregar al request para especificar los segmentos
        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        $datos["costo"] = floatval(str_replace(",", "",$datos["costo"]));
        $datos["cantidad"] = floatval(str_replace(",", "",$datos["cantidad"]));

        $arrayPDOParam = array();        
        $arrayPDOParam["gastoId"] = self::$type["gastoId"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["tipoGasto"] = self::$type["tipoGasto"];
        $arrayPDOParam["maquinaria"] = self::$type["maquinaria"];
        $arrayPDOParam["ubicacion"] = self::$type["ubicacion"];
        $arrayPDOParam["numeroParte"] = self::$type["numeroParte"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["proveedor"] = self::$type["proveedor"];
        $arrayPDOParam["factura"] = self::$type["factura"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["obra"] = self::$type["obra"];
        $arrayPDOParam["solicito"] = self::$type["solicito"];

        $campos = fCreaCamposInsert($arrayPDOParam);

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

                $directorio = "../../vistas/uploaded-files/gastos/";//Esta sobrando los ../../ ya que como se usa esta funcion en ajax, hay problemas con las rutas
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
            $insertar["gastoDetalleId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = substr($ruta,6);
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["gastoDetalleId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO gasto_archivos " . $campos, $insertar, $arrayPDOParam, $error);

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

    public function agregarObservacion()
    {
        $datos[$this->keyName] = $this->id;
        $datos["observaciones_detalles"] = $this->observaciones_detalles;
        $datos["cancelada"] = 1;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["observaciones_detalles"] = self::$type["observaciones_detalles"];
        $arrayPDOParam["cancelada"] = self::$type["cancelada"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function habilitarPartidas()
    {
        $datos["gastoId"] = $this->gastoId;
        $datos["cancelada"] = 0;
        $datos["observaciones_detalles"] = null;
        
        $arrayPDOParam = array();
        $arrayPDOParam["cancelada"] = self::$type["cancelada"];
        $arrayPDOParam["observaciones_detalles"] = self::$type["observaciones_detalles"];
        $arrayPDOParam["gastoId"] = self::$type["gastoId"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE gastoId = :gastoId", $datos, $arrayPDOParam, $error);
    }

}