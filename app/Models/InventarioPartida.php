<?php

namespace App\Models;

use App\Conexion;
use PDO;

class InventarioPartida
{
    static protected $fillable = [
        'id', 'inventarioId', 'numeroParte', 'cantidad','partidaId', 'unidad', 'concepto', 'costo_unitario'
    ];

    static protected $type = [
        'id' => 'integer',
        'inventarioId' => 'integer',
        'numeroParte' => 'varchar',
        'cantidad' => 'decimal',
        'partidaId' => 'integer',
        'unidad' => 'string',
        'concepto' => 'string',
        'costo_unitario' => 'decimal'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "inventario_partida";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR TODAS LAS PARTIDAS 
    =============================================*/
    public function consultarPartidas($permiso = ''){

        $resultadoLimpio = [];

        $respuesta = Conexion::queryAll($this->bdName, "SELECT  
                                                            IP.inventarioId AS id,
                                                            A.descripcion AS nombreAlmacen, 
                                                            IFNULL((SELECT SUM(cantidad) FROM inventario_salida_partida ISP
                                                            WHERE ISP.partidaId = IP.id),0) AS 'cantidadSalidas',
                                                            SUM(IP.cantidad) AS cantidad, 
                                                            IP.unidad, 
                                                            IP.concepto,
                                                            IP.numeroParte
                                                        FROM 
                                                            inventario_partida IP
                                                        INNER JOIN 
                                                            inventarios I ON IP.inventarioId = I.id
                                                        LEFT JOIN 
                                                            requisicion_detalles RD ON IP.partidaId = RD.id
                                                        INNER JOIN 
                                                            almacenes A ON I.almacenId = A.id
                                                        $permiso
                                                        GROUP BY 
                                                            IP.id,
                                                            A.descripcion, 
                                                            RD.unidad, 
                                                            RD.concepto,
                                                            IP.numeroParte;", $error);
            foreach ($respuesta as $item) {
                $resultadoLimpio[] = [
                    "cantidad"     => (int) $item["cantidad"],
                    "cantidadSalidas"     => (int) $item["cantidadSalidas"],
                    "id"           => (int) $item["id"],
                    "nombreAlmacen"=> trim($item["nombreAlmacen"]),
                    "unidad"       => trim($item["unidad"]),
                    "numeroParte"  => trim($item["numeroParte"]),
                    "concepto"     => trim($item["concepto"]),
                ];
                }

            return $resultadoLimpio;
        }

    
    /*=============================================
        CONSULTAR PARTIDAS POR ID DE INVENTARIO
    =============================================*/

    public function consultarPartidaPorId(){

        $respuesta = Conexion::queryAll($this->bdName, "SELECT IP.*,
        IFNULL((SELECT SUM(cantidad) FROM inventario_salida_partida ISP
                WHERE ISP.partidaId = IP.id),0) AS 'cantidadSalidas'
        FROM inventario_partida IP 
        WHERE IP.inventarioId   = $this->id", $error);

        $puestos_limpios = [];
        foreach ($respuesta as $fila) {
            $puestos_limpios[] = [
                "id" => $fila["id"],
                "inventarioId" => $fila["inventarioId"],
                "numeroParte" => $fila["numeroParte"],
                "cantidad" => $fila["cantidad"],
                "costo_unitario" => $fila["costo_unitario"],
                "partidaId" => $fila["partidaId"],
                "concepto" => $fila["concepto"],
                "unidad" => $fila["unidad"],
                "cantidadSalidas" => $fila["cantidadSalidas"]
            ];
        }
         // Retornar como JSON
        return $puestos_limpios;
    }


    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["inventarioId"] = self::$type["inventarioId"];
        $arrayPDOParam["numeroParte"] = self::$type["numeroParte"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["costo_unitario"] = self::$type["costo_unitario"];
        $arrayPDOParam["partidaId"] = self::$type["partidaId"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["concepto"] = self::$type["concepto"];

        $datos["cantidad"] = (float) str_replace(',','',$datos["cantidad"]);
        $datos["costo_unitario"] = (float) preg_replace('/[^\d.\-]/', '', $datos["costo_unitario"]);

        $campos = fCreaCamposInsert($arrayPDOParam);
        
        return Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error);

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["nombreCorto"] = self::$type["nombreCorto"];
        $arrayPDOParam["orden"] = self::$type["orden"];
        $arrayPDOParam["perfilesCrearRequis"] = self::$type["perfilesCrearRequis"];


        if (!isset($datos["perfilesCrearRequis"])) {
            $datos["perfilesCrearRequis"] = "[]";
        }else{
            $datos["perfilesCrearRequis"] = json_encode($datos["perfilesCrearRequis"]);
        }

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET descripcion = :descripcion, nombreCorto = :nombreCorto, orden = :orden, perfilesCrearRequis = :perfilesCrearRequis WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function insertarImagen($id, $archivos) {


        for ($i = 0; $i < count($archivos['name']); $i++) {

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN                
                $directorio = "../../vistas/uploaded-files/inventarios/detalle-imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );

            }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["inventario_detalle"] = $id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;

            $arrayPDOParam = array();        
            $arrayPDOParam["inventario_detalle"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO inventario_partida_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                // move_uploaded_file($tmp_name, $ruta);
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }
    }

    public function consultarImagenes($id) {
        return Conexion::queryAll($this->bdName, "SELECT * FROM inventario_partida_imagenes WHERE inventario_detalle = $id", $error);
    }
}
