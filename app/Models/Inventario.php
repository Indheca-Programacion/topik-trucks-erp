<?php

namespace App\Models;

if ( file_exists ( "app/Policies/InventarioPolicy.php" ) ) {
    require_once "app/Policies/InventarioPolicy.php";
} else {
    require_once "../Policies/InventarioPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\InventarioPolicy;

class Inventario extends InventarioPolicy
{
    static protected $fillable = [
       'id','almacenId', 'ordenCompra', 'observaciones', 'requisicionId', 'entrego', 'usuarioRecibioId','fechaCreacion','firma','fechaEntrega',
    ];

    static protected $type = [
        'id' => 'integer',        
        'ordenCompra' => 'string',
        'almacenId' => 'integer',
        'firma' => 'string',
        'observaciones' => 'string',
        'entrego' => 'string',
        'usuarioRecibioId' => 'integer',
        'requisicionId' => 'integer',
        'fechaCreacion' => 'date',
        'fechaEntrega' => 'date',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "inventarios";

    protected $keyName = "id";

    public $id = null;
    public $observaciones;
    public $ordenCompra;
    public $entrego;
    public $almacenId;
    public $firma;
    public $usuarioRecibioId;
    public $requisicionId;
    public $fechaEntrega;

    static public function fillable() {
        return self::$fillable;
    }

    public function obtenerEntradasPorOrdenCompra($id){
        return Conexion::queryAll($this->bdName, "SELECT * FROM $this->tableName WHERE ordenCompra = $id", $error);
    }

    /*=============================================
    MOSTRAR INVENTARIOS
    =============================================*/
    public function consultarInventarios($permiso = ''){

        $respuesta = Conexion::queryAll($this->bdName, "SELECT 
            I.id as folio,
            A.descripcion as nombreAlmacen,
            I.entrego as entrego, 
            I.ordenCompra,
            CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', U.apellidoMaterno) as recibio
        FROM 
            inventarios I
        INNER JOIN 
            almacenes A ON I.almacenId = A.id
        INNER JOIN 
            usuarios U ON I.usuarioRecibioId = U.id $permiso", $error);

        $resultadoLimpio = [];
        foreach ($respuesta as $item) {
            $resultadoLimpio[] = [
                "folio"           => (int) $item["folio"],
                "nombreAlmacen"=> trim($item["nombreAlmacen"]),
                "entrego"  => $item["entrego"],
                "ordenCompra"  => $item["ordenCompra"],
                "recibio"  => $item["recibio"]
            ];
            }

        return $resultadoLimpio;
    }

    /*=============================================
    MOSTRAR INVENTARIO POR ID
    =============================================*/
    public function consultarInventarioPorId( $valor = null)
    {

            $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE id = '$valor'", $error);

            if ( $respuesta ) {

                $this->id = $respuesta["id"];
                $this->observaciones = $respuesta["observaciones"];
                $this->ordenCompra = $respuesta["ordenCompra"];
                $this->entrego = $respuesta["entrego"];
                $this->almacenId = $respuesta["almacenId"];
                $this->firma = $respuesta["firma"];                
                $this->usuarioRecibioId = $respuesta["usuarioRecibioId"];                
                $this->requisicionId = $respuesta["requisicionId"];                
                $this->fechaEntrega = fFechaLarga($respuesta["fechaEntrega"]);         
            }

            return $respuesta;
    }

    /*=============================================
    CREAR INVENTARIO DE REQUISICION
    =============================================*/
    public function crear($datos)
    {

        $arrayPDOParam = array();
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        if(isset($datos["ordenCompra"])) $arrayPDOParam["ordenCompra"] = self::$type["ordenCompra"];
        $arrayPDOParam["almacenId"] = self::$type["almacenId"];
        $arrayPDOParam["entrego"] = self::$type["entrego"];
        $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["usuarioRecibioId"] = self::$type["usuarioRecibioId"];
        if( isset($datos["requisicionId"]) && $datos["requisicionId"] > 0 ) $arrayPDOParam["requisicionId"] = self::$type["requisicionId"];
        $arrayPDOParam["fechaEntrega"] = self::$type["fechaEntrega"];

        $datos["usuarioRecibioId"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId=0;


        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error,$lastId);

        if ($respuesta) {
            return $this->id = $lastId;
        } else {
            echo "Error: " . $error;
        }

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

    public function eliminar()
    {
        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

}
