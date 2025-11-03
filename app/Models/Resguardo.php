<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ResguardoPolicy.php" ) ) {
    require_once "app/Policies/ResguardoPolicy.php";
} else {
    require_once "../Policies/ResguardoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ResguardoPolicy;

class Resguardo extends ResguardoPolicy
{
    static protected $fillable = [
        'usuarioRecibioId', 'usuarioEntregoId', 'firma', 'observaciones','salidaId','fecha','partidaId','resguardoId','fechaTransferencia','usuarioRecibioTransferencia'
    ];

    static protected $type = [
        'id' => 'integer',
        'usuarioRecibioId' => 'integer',
        'usuarioEntregoId' => 'integer',
        'observaciones' => 'string',
        'salidaId' => 'integer',
        'firma' => 'string',   
        'fechaEntrego' => 'date',   
        'partidaId' => 'integer',   
        'resguardoOriginalId' => 'integer',   
        'resguardoNuevoId' => 'integer',   

        'cantidad' => 'string',   
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "salida_resguardo";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR UNIDADES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                "SELECT 
                    SR.*,
                    CONCAT(UR.nombre, ' ', UR.apellidoPaterno, ' ', IFNULL(UR.apellidoMaterno, '')) AS 'nombreRecibio',
                    CONCAT(UE.nombre, ' ', UE.apellidoPaterno, ' ', IFNULL(UE.apellidoMaterno, '')) AS 'nombreEntrego',
                    A.descripcion AS nombreAlmacen
                FROM salida_resguardo SR
                INNER JOIN usuarios UR ON SR.usuarioRecibioId = UR.id
                INNER JOIN usuarios UE ON SR.usuarioEntregoId = UE.id
                INNER JOIN inventario_salida INS ON SR.salidaId = INS.id
                INNER JOIN almacenes A ON INS.almacenId = A.id;", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT SR.*,
                    CONCAT(UR.nombre, ' ', UR.apellidoPaterno, ' ', IFNULL(UR.apellidoMaterno, '')) AS 'nombreRecibio',
                    CONCAT(UE.nombre, ' ', UE.apellidoPaterno, ' ', IFNULL(UE.apellidoMaterno, '')) AS 'nombreEntrego',
                INS.almacenId
                FROM salida_resguardo SR 
                INNER JOIN inventario_salida INS ON SR.salidaId = INS.id
                INNER JOIN usuarios UR ON SR.usuarioRecibioId = UR.id
                INNER JOIN usuarios UE ON SR.usuarioEntregoId = UE.id
                WHERE SR.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT R.*, IV.unidad, IV.descripcion FROM $this->tableName R INNER JOIN inventarios IV ON IV.id = R.inventario WHERE R.$item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->usuarioEntregoId = $respuesta["usuarioEntregoId"];
                $this->usuarioRecibioId = $respuesta["usuarioRecibioId"];
                $this->nombreRecibio = $respuesta["nombreRecibio"];
                $this->nombreEntrego = $respuesta["nombreEntrego"];

                $this->almacenId = $respuesta["almacenId"];
                $this->fechaEntrego = $respuesta["fechaEntrego"];
                $this->observaciones = $respuesta["observaciones"];
                $this->salidaId = $respuesta["salidaId"];
                $this->firma = $respuesta["firma"];

            }

            return $respuesta;

        }

    }

    public function crear($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["usuarioRecibioId"] = self::$type["usuarioRecibioId"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["usuarioEntregoId"] = self::$type["usuarioEntregoId"];
        $arrayPDOParam["salidaId"] = self::$type["salidaId"];
        $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["fechaEntrego"] = self::$type["fechaEntrego"];

        $datos["fechaEntrego"] = $datos['fecha'];

        $datos["usuarioEntregoId"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO salida_resguardo ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->id = $lastId;
        }

        return $respuesta;
    }

    public function crearResguardoPorTransferencia($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["usuarioRecibioId"] = self::$type["usuarioRecibioId"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["usuarioEntregoId"] = self::$type["usuarioEntregoId"];
        $arrayPDOParam["salidaId"] = self::$type["salidaId"];
        $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["fechaEntrego"] = self::$type["fechaEntrego"];

        $datos["fechaEntrego"] = $datos['fechaTransferencia'];

        $datos["usuarioEntregoId"] = usuarioAutenticado()["id"];
        $datos["usuarioRecibioId"] = $datos["usuarioRecibioTransferencia"];
        $datos["observaciones"] = "";

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO salida_resguardo ".$campos, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {
            // Asignamos el ID creado al momento de crear el usuario
            $this->resguardoNuevoId = $lastId;
        }

        return $respuesta;
    }

    
    public function crearTransferencia($datos) {

        $arrayPDOParam = array();        
        $arrayPDOParam["usuarioRecibioId"] = self::$type["usuarioRecibioId"];
        $arrayPDOParam["usuarioEntregoId"] = self::$type["usuarioEntregoId"];
        $arrayPDOParam["firma"] = self::$type["firma"];
        $arrayPDOParam["resguardoOriginalId"] = self::$type["resguardoOriginalId"];
        $arrayPDOParam["resguardoNuevoId"] = self::$type["resguardoNuevoId"];
        
        $arrayPDOParam["fechaEntrego"] = self::$type["fechaEntrego"];
        
        $datos["fechaEntrego"] = $datos['fechaTransferencia'];
        $datos["resguardoOriginalId"] = $datos['resguardoId'];
        $datos["resguardoNuevoId"] = $datos['resguardoNuevoId'];
        $datos["usuarioEntregoId"] = usuarioAutenticado()["id"];
        $datos["usuarioRecibioId"] = $datos["usuarioRecibioTransferencia"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO transferencia_resguardo ".$campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario
            $this->transferenciaId = $lastId;
        }

        return $respuesta;
    }

    

    /*=============================================
    MOSTRAR PARTIDAS DEL RESGUARDO
    =============================================*/

    public function partidasResguardo(){

        return Conexion::queryAll($this->bdName, 
        "SELECT 
            *
        FROM salida_resguardo_partida SRP
        WHERE  SRP.salidaResguardoId = $this->id
        ", $error);
    }

    public function insertarDetalles($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["salidaResguardoId"] = 'integer';
        $arrayPDOParam["cantidad"] = 'integer';
        $arrayPDOParam["numeroParte"] = 'string';
        $arrayPDOParam["concepto"] = 'string';
        $arrayPDOParam["unidad"] = 'string';
        $arrayPDOParam["partidaId"] = 'integer';

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO salida_resguardo_partida " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function insertarDetallesTransferencia($datos)
    {
        $arrayPDOParam = array();
        $arrayPDOParam["transferenciaResguardoId"] = 'integer';
        $arrayPDOParam["cantidad"] = 'integer';
        $arrayPDOParam["numeroParte"] = 'string';
        $arrayPDOParam["concepto"] = 'string';
        $arrayPDOParam["unidad"] = 'string';
        $arrayPDOParam["partidaId"] = 'integer';

        $datos["transferenciaResguardoId"] = $this->transferenciaId;

        $campos = fCreaCamposInsert($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO transferencia_resguardo_partida " . $campos, $datos, $arrayPDOParam, $error);

        return $respuesta;
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            // Asignamos el ID creado al momento de crear el usuario

            if (isset($datos["archivos"]) && $datos['archivos']['name'][0] != '') {
                
                $respuesta = $this->insertarArchivos($datos['archivos']);
            }

        }

        return $respuesta;
    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM salida_resguardo WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function consultarArchivos(){
        $respuesta = Conexion::queryAll($this->bdName,"SELECT * FROM resguardo_archivos where resguardo = $this->id");
        $this->archivos = $respuesta;
    }

    function insertarArchivos($archivos) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/resguardos/";
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = ".pdf";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["resguardo"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["resguardo"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = "integer";

            $campos = fCreaCamposInsert($arrayPDOParam);
            
            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO resguardo_archivos " . $campos, $insertar, $arrayPDOParam, $error);
            
            if ( $respuesta && $ruta != "" ) {

                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }

    public function modificarPartidaResguardo($datos) {

        // OBTENER PARTIDA
        $partidaId = $datos["id"];
        $partidaOriginal =  Conexion::queryUnique($this->bdName,"SELECT cantidad FROM salida_resguardo_partida SRP WHERE  SRP.id = $partidaId", $error);
        
        $cantidadTotal = (int) $partidaOriginal["cantidad"] - (int) $datos["cantidad"];

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $datos["id"];
        $datos["cantidad"] = $cantidadTotal;

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return  Conexion::queryExecute($this->bdName, "UPDATE salida_resguardo_partida SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    // OBTENER TRANFERENCIA DE LOS RESGUARDOS
    public function consultarTransferenciasDeResguardo(){
        return  Conexion::queryAll($this->bdName, "SELECT 
                                                            TR.*,
                                                            TRP.concepto,
                                                            TRP.cantidad,
                                                            CONCAT(UR.nombre, ' ', UR.apellidoPaterno, ' ', IFNULL( UR.apellidoMaterno, '')) as 'nombreUsuarioRecibio',
                                                            CONCAT(UE.nombre, ' ', UE.apellidoPaterno, ' ', IFNULL( UE.apellidoMaterno, '')) as 'nombreUsuarioEntrego'
                                                        FROM 
                                                            transferencia_resguardo_partida TRP
                                                        INNER JOIN transferencia_resguardo TR ON TRP.transferenciaResguardoId = TR.id
                                                        INNER JOIN usuarios UR ON UR.id = TR.usuarioRecibioId
                                                        INNER JOIN usuarios UE ON UE.id = TR.usuarioEntregoId
                                                        WHERE 
                                                        TR.resguardoOriginalId = $this->id;", $error);

    }
    
}
