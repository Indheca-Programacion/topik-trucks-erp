<?php

namespace App\Models;

if ( file_exists ( "app/Policies/MarcaPolicy.php" ) ) {
    require_once "app/Policies/MarcaPolicy.php";
} else {
    require_once "../Policies/MarcaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\MarcaPolicy;

class MensajeRequisicion extends MarcaPolicy
{
    static protected $fillable = [
        'id',
        'mensaje',
        'id_requisicion',
        'usuario_id',
        'fecha_enviado'
    ];

    static protected $type = [
        'id' => 'integer',
        'mensaje' => 'string',
        'id_requisicion' => 'integer',
        'usuario_id' => 'integer',
        'fecha_enviado' => 'string',

    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "chat_requisiciones";

    protected $keyName = "id";

    public $id = null;
    public $mensaje;
    public $id_requisicion;
    public $usuario_id;
    public $fecha_enviado;



    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MENSAJES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        // Si se recibe un valor para filtrar por id_requisicion
        $whereClause = '';
        if ($item !== null && $valor !== null) {
            $whereClause = " WHERE C.id_requisicion = :valor";
        }
    
        // Consulta con cláusula WHERE añadida si corresponde
        $respuesta =  Conexion::queryAll($this->bdName, 
            "    SELECT 
                        C.*, 
                        CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS nombreCompleto
                FROM 
                        $this->tableName  C
                LEFT JOIN 
                        usuarios U ON C.usuario_id = U.id
                WHERE 
                    C.id_requisicion = $valor
                ", 
                $error,
        );

        return $respuesta;
    }
    

    public function crear($datos) {

        
        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuario_id"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();        
        $arrayPDOParam["mensaje"] = self::$type["mensaje"];
        $arrayPDOParam["id_requisicion"] = self::$type["id_requisicion"];
        $arrayPDOParam["usuario_id"] = self::$type["usuario_id"];

         $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName (id_requisicion, mensaje, usuario_id) VALUES (:id_requisicion ,:mensaje, :usuario_id)", $datos, $arrayPDOParam, $error);

    }


}
