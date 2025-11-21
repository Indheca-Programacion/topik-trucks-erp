<?php

namespace App\Models;

use App\Conexion;
use PDO;

class MensajeOrdenCompra 
{
    static protected $fillable = [
        'id',
        'observacion',
        'ordenCompraId',
    ];

    static protected $type = [
        'id' => 'integer',
        'observacion' => 'string',
        'ordenCompraId' => 'integer',
        'usuarioIdCreacion' => 'integer',
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "mensaje_orden_compra_pago";
    
    protected $keyName = "id";

    public $id = null;
    public $observacion;
    public $ordenCompraId;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MENSAJES
    =============================================*/
    public function consultar($item = null, $valor = null)
    {
        $query = "SELECT    MOCP.*, 
                            US.nombre AS 'usuarios.nombre', 
                            US.apellidoPaterno AS 'usuarios.apellidoPaterno', 
                            US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        mensaje_orden_compra_pago MOCP
                INNER JOIN  usuarios US ON MOCP.usuarioIdCreacion = US.id
                WHERE       MOCP.ordenCompraId = {$valor}
                ORDER BY    MOCP.id DESC";

        return Conexion::queryAll($this->bdName, $query, $error);
    }


    
    public function crear($datos) {
        
        $arrayPDOParam = array();        
        $arrayPDOParam["observacion"] = self::$type["observacion"];
        $arrayPDOParam["ordenCompraId"] = self::$type["ordenCompraId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName,"INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error);
    }

}
