<?php

namespace App\Models;

use App\Conexion;
use PDO;

class InventarioSalida
{
    static protected $fillable = [
        'id', 'inventarioId', 'numeroParte', 'cantidad','partidaId','almacenId','usuarioRecibioId','entradaId', 'firma','status'
    ];

    static protected $type = [
        'id' => 'integer',
        'inventarioId' => 'integer',
        'numeroParte' => 'varchar',
        'cantidad' => 'integer',
        'concepto' => 'varchar',
        'unidad' => 'varchar',
        'partidaId' => 'integer',
        'entradaId' => 'integer',
        'firma' => 'string',
        'almacenId' => 'integer',
        'usuarioEntregoId' => 'integer',
        'usuarioRecibioId' => 'integer',
        'status' => 'string'

    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "inventario_salida";

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR INVENTARIOS CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $id = usuarioAutenticado()["id"];
        $query = "SELECT IV.*,
                    CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'nombreCompleto', A.nombre as 'almacen.descripcion'
                    FROM $this->tableName  IV
                    INNER JOIN usuarios U ON U.id = IV.usuarioIdCreacion
                    INNER JOIN almacenes A ON A.id = IV.almacen
                    WHERE A.usuarioIdCreacion = $id";

        foreach ($arrayFiltros as $key => $value) {
            $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " ORDER BY IV.fechaCreacion desc";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR INVENTARIO SALIDA POR ID
    =============================================*/
    public function consultarInventarioPorId( $valor = null)
    {
            $respuesta = Conexion::queryAll($this->bdName, "SELECT INS.* ,  CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', U.apellidoMaterno) AS nombreEntrego FROM inventario_salida INS
            LEFT JOIN 	usuarios U ON INS.usuarioEntregoId = U.id
            WHERE INS.entradaId = '$valor'", $error);

            return $respuesta;
    }

    /*=============================================
    MOSTRAR LAS PARTIDAS DE LA TABLA SALIDAS POR ID
    =============================================*/
    public function consultarPartidaSalidaPorId($id){

        $respuesta = Conexion::queryAll($this->bdName, "SELECT ISP.*,
        CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', U.apellidoMaterno) AS entrego,
        INS.fechaSalida,
        IFNULL((
            SELECT 
                SUM(SRP.cantidad)
            FROM salida_resguardo_partida SRP
            INNER JOIN salida_resguardo SR ON SR.id = SRP.salidaResguardoId
            WHERE 
                SR.salidaId = ISP.inventarioId
        ),0) AS 'cantidadSalidas'
        FROM inventario_salida_partida ISP 
        INNER JOIN inventario_salida INS on ISP.inventarioId = INS.id 
        LEFT JOIN	usuarios U on INS.usuarioEntregoId = U.id
        WHERE ISP.inventarioId = $id", $error);

         // Retornar como JSON
        return $respuesta;
    }
    
    /*=============================================
    MOSTRAR INVENTARIOS
    =============================================*/
    public function consultar($item = null, $valor = null,$permiso = '')
    {
        if ( is_null($valor) ) {
            return Conexion::queryAll($this->bdName, "SELECT 
                                                        INS.id,
                                                        A.descripcion AS nombreAlmacen,
                                                        INS.fechaSalida,
                                                        INS.status,
                                                        INS.entradaId,
                                                        CONCAT(UE.nombre, ' ',UE.apellidoPaterno,' ', UE.apellidoMaterno) AS nombreEntrego,
                                                        CONCAT(UR.nombre, ' ',UR.apellidoPaterno,' ', UR.apellidoMaterno) AS nombreRecibio,
                                                        CONCAT(UA.nombre, ' ', UA.apellidoPaterno, ' ', UA.apellidoMaterno) AS autorizo 
                                                    from inventario_salida INS
                                                    INNER JOIN almacenes A ON INS.almacenId = A.id
                                                    LEFT JOIN usuarios UE ON INS.usuarioEntregoId = UE.id
                                                    LEFT JOIN	usuarios UA on INS.usuarioAutorizoId = UA.id
                                                    LEFT JOIN usuarios UR ON INS.usuarioRecibioId = UR.id
                                                    inner join inventarios I ON INS.entradaId = I.id
                                                    $permiso", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                    INVS.*, 
                                                                    IFNULL(CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')), INVS.usuarioRecibioId) AS 'recibe',
                                                                    CONCAT(UA.nombre, ' ', UA.apellidoPaterno, ' ', UA.apellidoMaterno) AS autorizo 
                                                                    FROM $this->tableName INVS 
                                                                    LEFT JOIN usuarios U ON U.id = INVS.usuarioRecibioId 
                                                                    LEFT JOIN	usuarios UA on INVS.usuarioAutorizoId = UA.id
                                                                    WHERE INVS.$this->keyName = $valor", $error);

            } else {
                
                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->folio = $respuesta["id"];
                $this->inventario = $respuesta["almacenId"];
                $this->ordenCompra = $respuesta["ordenCompra"];
                $this->usuarioRecibe = $respuesta["recibe"];
                $this->usuarioAutorizo = $respuesta["autorizo"];
                $this->usuarioAutorizoId = $respuesta["usuarioAutorizoId"];
                $this->usuarioIdCreacion = $respuesta["usuarioEntregoId"];
                $this->firma = $respuesta["firma"];

                //ERROR PAGINA NO FUNCIONA AL ENTRAR A LA FUNCION FECHA
                
                $this->fechaCreacion =   !empty($respuesta["fechaSalida"]) ?fFechaLargaHora($respuesta["fechaSalida"]) : "SIN FECHA DE SALIDA";         
                $this->fechaActualizacion =  !empty($respuesta["fechaActualizacion"]) ?fFechaLargaHora($respuesta["fechaActualizacion"]) : "SIN FECHA DE SALIDA";            

            }

            return $respuesta;

        }
    }
    
    public function consultarLastFolio(){
        return Conexion::queryUnique($this->bdName,"SELECT folio 
                                                    FROM $this->tableName
                                                    WHERE folio = (SELECT MAX(folio) FROM $this->tableName)");
    }

    public function consultarSalidas($id)
    {
        return Conexion::queryAll($this->bdName, "SELECT INVS.*, 
            IFNULL(CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')), INVS.usuarioRecibe) AS 'recibe'
        FROM $this->tableName  INVS
        LEFT JOIN usuarios U ON U.id = INVS.usuarioIdCreacion
        INNER JOIN inventarios INV ON INV.id = INVS.inventario
        WHERE INVS.inventario = $id
        ", $error);
    }

    public function consultarSalidasPendientes()
    {
        return Conexion::queryAll($this->bdName, "SELECT INVS.*, 
            CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'entrego',
            A.descripcion AS 'almacen.descripcion'
        FROM $this->tableName  INVS
        LEFT JOIN usuarios U ON U.id = INVS.usuarioEntregoId
        INNER JOIN almacenes A ON A.id = INVS.almacenId
        WHERE INVS.status = 'NO AUTORIZADO'
        ", $error);
    }

    public function consultarDetalles($id)
    {
        return Conexion::queryAll($this->bdName, 
        "SELECT ISD.cantidad, U.descripcion as 'unidad.descripcion', 
        ID.numeroParte , IFNULL(I.descripcion, INS.descripcion) AS descripcion, IFNULL( P.concepto , '') AS concepto
                            FROM inventario_salida_partida ISD 
                            INNER JOIN inventario_detalles ID ON ID.id = ISD.partida
                            left JOIN partidas P ON P.id = ID.partida
                            LEFT JOIN indirectos I ON I.id = ID.indirecto
                            LEFT JOIN insumos INS ON INS.id = ID.directo
                            LEFT JOIN unidades U ON U.id = I.unidadId OR U.id = INS.unidadId
                            WHERE ISD.inventario = $id", $error);
    }

    public function crear($datos)
    {   

        $arrayPDOParam = array();
        $arrayPDOParam["almacenId"] = self::$type["almacenId"];
        $arrayPDOParam["entradaId"] = self::$type["entradaId"];
        $arrayPDOParam["status"] = 'varchar';

        $datos["status"] = "NO AUTORIZADO";

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId=0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error,$lastId);
        if ( $respuesta ){

            $this->id = $lastId;
        } 

        return $respuesta;
    }

    public function actualizar($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];        
        $arrayPDOParam["genero"] = self::$type["genero"];
        $arrayPDOParam["subgenero"] = self::$type["subgenero"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["numeroParte"] = self::$type["numeroParte"];
        $arrayPDOParam["marca"] = self::$type["marca"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["fechaAdquisicion"] = self::$type["fechaAdquisicion"];
        $arrayPDOParam["costo"] = self::$type["costo"];
        $arrayPDOParam["almacen"] = self::$type["almacen"];
        $arrayPDOParam["estante"] = self::$type["estante"];
        $arrayPDOParam["pasillo"] = self::$type["pasillo"];
        $arrayPDOParam["ordenCompra"] = self::$type["ordenCompra"];
        $arrayPDOParam["nivel"] = self::$type["nivel"];
        
        $datos["fechaAdquisicion"] = fFechaSQL($datos["fechaAdquisicion"]);
        $datos["costo"] =   str_replace(',', '', $datos["costo"]);
        $datos["cantidad"] =  str_replace(',', '', $datos["cantidad"]);

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ($respuesta) {
            
            if ( isset($datos["observacion"]) ) {

                $insertarPDOParam = array();

                $insertar["inventarioId"] = $this->id;
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

                $insertarPDOParam["inventarioId"] = self::$type["id"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO inventario_observaciones " . $campos, $insertar, $insertarPDOParam, $error);

            }
        }

        return $respuesta;


    }

    // ACTUALIZA EL ESTATUS DE LA SALIDA A AUTORIZADO
    public function actualizarStatus()
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["status"] = 'string';
        $arrayPDOParam["usuarioAutorizoId"] = 'integer';        
        
        $datos["status"] = "AUTORIZADO";
        $datos["usuarioAutorizoId"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        return $respuesta;


    }

    // FIRMA LA SALIDA
    public function firmarSalida($datos)
    {
        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam["status"] = 'string';        
        $arrayPDOParam["firma"] = 'string';        
        $arrayPDOParam["usuarioEntregoId"] = 'integer';        
        $arrayPDOParam["usuarioRecibioId"] = 'integer';        
        $arrayPDOParam["fechaSalida"] = 'date';        
        
        $datos["status"] = "SALIDA FIRMADA";
        $datos["usuarioEntregoId"] = usuarioAutenticado()["id"];
        $datos["fechaSalida"] =  gmdate("Y-m-d H:i:s", time() - 6 * 3600);


        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        
        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

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

    public function insertarDetalles($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["inventarioId"] = self::$type["inventarioId"];
        $arrayPDOParam["partidaId"] = self::$type["partidaId"];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["numeroParte"] = self::$type["numeroParte"];
        $arrayPDOParam["concepto"] = self::$type["concepto"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO inventario_salida_partida " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

}
