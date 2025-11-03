<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ComprobacionGastoPolicy.php" ) ) {
    require_once "app/Policies/ComprobacionGastoPolicy.php";
} else {
    require_once "../Policies/ComprobacionGastoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ComprobacionGastoPolicy;

class ComprobacionGasto extends ComprobacionGastoPolicy
{
    static protected $fillable = [
        'servicioEstatusId', 'empresaId', 'maquinariaId', 'monto', 'justificacion', 'obraId', 'actualServicioEstatusId', 'fechaRequerida', 'detalles', 'comprobanteArchivos', 'soporteArchivos', 'observacion', 'partidasEliminadas'
    ];

    static protected $type = [
        'id' => 'integer',
        'servicioEstatusId' => 'string',
        'empresaId' => 'integer',
        'maquinariaId' => 'integer',
        'monto' => 'decimal',
        'justificacion' => 'string',
        'obraId' => 'integer',
        'actualServicioEstatusId' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdResponsable' => 'integer',
        'usuarioIdAlmacen' => 'integer',
        'observacion' => 'string'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "comprobacion_gastos";

    protected $keyName = "id";

    public $id = null;    
    public $descripcion;
    public $nombreCorto;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR COLORES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT CG.*, concat('GC-', CG.id) as folio,
                                    concat(usuarios.nombre,' ', usuarios.apellidoPaterno,' ', ifnull(usuarios.apellidoMaterno,'')) as creo,
                                    SE.descripcion AS 'estatus.descripcion', SE.colorTexto AS 'estatus.colorTexto', SE.colorFondo AS 'estatus.colorFondo'
                                    FROM $this->tableName CG
                                    inner join servicio_estatus SE on SE.id = CG.servicioEstatusId
                                    inner join empresas on empresas.id = CG.empresaId
                                    inner join usuarios on usuarios.id = CG.usuarioIdCreacion
                                    order by CG.id desc", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->folio = 'GC-'.$respuesta["id"];
                $this->servicioEstatusId = $respuesta["servicioEstatusId"];
                $this->empresaId = $respuesta["empresaId"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->monto = $respuesta["monto"];
                $this->justificacion = $respuesta["justificacion"];
                $this->obraId = $respuesta["obraId"];
                $this->fechaCreacion = $respuesta["fechaCreacion"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdResponsable = $respuesta["usuarioIdResponsable"];
                $this->usuarioIdAlmacen = $respuesta["usuarioIdAlmacen"];


                if ( file_exists ( "app/Models/Maquinaria.php" ) ) {
                    require_once "app/Models/Maquinaria.php";
                } else {
                    require_once "../Models/Maquinaria.php";
                }
                $maquinaria = New Maquinaria;
                $this->maquinaria = $maquinaria->consultar(null, $this->maquinariaId);

                if ( file_exists ( "app/Models/ServicioEstatus.php" ) ) {
                    require_once "app/Models/ServicioEstatus.php";
                } else {
                    require_once "../Models/ServicioEstatus.php";
                }
                $servicioEstatus = New ServicioEstatus;
                $this->estatus = $servicioEstatus->consultar(null, $this->servicioEstatusId);
            }

            return $respuesta;

        }

    }

    public function consultarPorUsuario( ) {

        $usuarioId = usuarioAutenticado()["id"];

        return Conexion::queryAll($this->bdName, "SELECT CG.*, concat('GC-', CG.id) as folio,
                                    concat(usuarios.nombre,' ', usuarios.apellidoPaterno,' ', ifnull(usuarios.apellidoMaterno,'')) as creo,
                                    SE.descripcion AS 'estatus.descripcion', SE.colorTexto AS 'estatus.colorTexto', SE.colorFondo AS 'estatus.colorFondo'
                                    FROM $this->tableName CG
                                    inner join servicio_estatus SE on SE.id = CG.servicioEstatusId
                                    inner join empresas on empresas.id = CG.empresaId
                                    inner join usuarios on usuarios.id = CG.usuarioIdCreacion
                                    WHERE CG.usuarioIdCreacion = $usuarioId
                                    order by CG.id desc", $error);

    }

    public function consultarDetalles() {

        $respuesta = Conexion::queryAll($this->bdName, "SELECT * FROM comprobacion_gastos_detalles WHERE comprobacionId = $this->id", $error);
        $this->detalles = $respuesta;

    }

    public function consultarObservaciones() {
        $respuesta = Conexion::queryAll($this->bdName, "SELECT RO.*, SE.descripcion AS 'servicio_estatus.descripcion', US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno' FROM comprobacion_gastos_observaciones RO
                                                        inner join servicio_estatus SE on SE.id = RO.servicioEstatusId
                                                        INNER JOIN  usuarios US ON RO.usuarioIdCreacion = US.id
                                                        WHERE RO.comprobacionId = $this->id ORDER BY RO.fechaCreacion ASC", $error);
        $this->observaciones = $respuesta;
    }

    public function consultarComprobantes() {
        $respuesta = Conexion::queryAll($this->bdName, "SELECT * FROM comprobacion_gastos_archivos WHERE comprobacionId = $this->id AND tipo = 1", $error);
        $this->comprobantesPago = $respuesta;
    }

    public function consultarSoportes() {
        $respuesta = Conexion::queryAll($this->bdName, "SELECT * FROM comprobacion_gastos_archivos WHERE comprobacionId = $this->id AND tipo = 6", $error);
        $this->soportes = $respuesta;
    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["monto"] = self::$type["monto"];
        $arrayPDOParam["justificacion"] = self::$type["justificacion"];
        // $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {

            $this->id = $lastId;

            // $this->sendMailCreacion($requisicion);
            $arrayDetalles = isset($datos["detalles"]) ? $datos["detalles"] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles);

            if ( isset($datos['comprobanteArchivos']) && $datos['comprobanteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteArchivos'], 1);
            
            if ( isset($datos['soporteArchivos']) && $datos['soporteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['soporteArchivos'], 6);


        }
        
        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();
        if ( isset($datos["servicioEstatusId"]) ) $arrayPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        
        //Si el estatus no cambia, no se hace nada
        if ( $datos['actualServicioEstatusId'] !== $datos['servicioEstatusId'] ){
            if ($datos["servicioEstatusId"] == 11) {
                $arrayPDOParam["usuarioIdResponsable"] = self::$type["usuarioIdResponsable"];
                $datos["usuarioIdResponsable"] = usuarioAutenticado()["id"];
            }
    
            if ($datos["servicioEstatusId"] == 10) {
                $arrayPDOParam["usuarioIdAlmacen"] = self::$type["usuarioIdAlmacen"];
                $datos["usuarioIdAlmacen"] = usuarioAutenticado()["id"];
            }
        }

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        $respuesta = Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $respuesta ) {

            if ( isset($datos["servicioEstatusId"]) ) $this->servicioEstatusId = $datos["servicioEstatusId"]; 

            if ( isset($datos["observacion"]) ) {
                $insertar = array();
                $insertar["comprobacionId"] = $this->id;
                $insertar["servicioEstatusId"] = $datos["servicioEstatusId"];
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = $datos["usuarioIdActualizacion"];

                $insertarPDOParam = array();
                $insertarPDOParam["comprobacionId"] = self::$type["id"];
                $insertarPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO comprobacion_gastos_observaciones " . $campos, $insertar, $insertarPDOParam, $error);
            }

            $arrayDetalles = isset($datos["detalles"]) ? $datos["detalles"] : null;
            if ( $arrayDetalles ) $respuesta = $this->insertarDetalles($arrayDetalles);

            if ( isset($datos["partidasEliminadas"]) ) $respuesta = $this->eliminarDetalles($datos["partidasEliminadas"]);

            if ( isset($datos['comprobanteArchivos']) && $datos['comprobanteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteArchivos'], 1);

            if ( isset($datos['soporteArchivos']) && $datos['soporteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['soporteArchivos'], 6);

        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    function insertarArchivos($archivos, $tipoArchivo) {

        for ($i = 0; $i < count($archivos['name']); $i++) { 
        
            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                if ( $tipoArchivo == 1 ) $directorio = "vistas/uploaded-files/requisiciones/comprobantes-pago/";
                elseif ( $tipoArchivo == 2 ) $directorio = "vistas/uploaded-files/requisiciones/ordenes-compra/";
                elseif ( $tipoArchivo == 3 ) $directorio = "vistas/uploaded-files/requisiciones/facturas/";
                elseif ( $tipoArchivo == 4 ) $directorio = "vistas/uploaded-files/requisiciones/cotizaciones/";
                elseif ( $tipoArchivo == 5 ) $directorio = "vistas/uploaded-files/requisiciones/vales-almacen/";
                elseif ( $tipoArchivo == 6 ) $directorio = "vistas/uploaded-files/requisiciones/soportes/";
                else $directorio = "vistas/uploaded-files/requisiciones/vales-almacen/"; // Valor por defecto
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';

                if ( $archivos["type"][$i] == "application/pdf" ) $extension = ".pdf";
                elseif ( $archivos["type"][$i] == "text/xml" ) $extension = ".xml";
                elseif ( $archivos["type"][$i] == "image/jpeg" ) $extension = ".jpg";
                elseif ( $archivos["type"][$i] == "image/png" ) $extension = ".png";
                elseif ( $archivos["type"][$i] == "image/gif" ) $extension = ".gif";
                elseif ( $archivos["type"][$i] == "image/webp" ) $extension = ".webp";
                elseif ( $archivos["type"][$i] == "image/svg+xml" ) $extension = ".svg";
                else $extension = "";

                if ( $extension != '') {
                    // $ruta = $directorio.$aleatorio.$extension;
                    do {
                        $ruta = fRandomNameFile($directorio, $extension);
                    } while ( file_exists($ruta) );
                }

            }

            $insertar = array();
            // Request con el nombre del archivo
            $insertar["comprobacionId"] = $this->id;
            $insertar["tipo"] = $tipoArchivo; // 1: Comprobante de Pago, 2: Orden de Compra
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["comprobacionId"] = self::$type[$this->keyName];
            $arrayPDOParam["tipo"] = "integer";
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO comprobacion_gastos_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }

    function insertarDetalles(array $arrayDetalles = null) {

        $respuesta = false;
    
        if ( $arrayDetalles ) {

            $insertarPDOParam = array();
            $insertarPDOParam["comprobacionId"] = self::$type[$this->keyName];
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["unidad"] = "string";
            $insertarPDOParam["numeroParte"] = "string";
            $insertarPDOParam["concepto"] = "string";
            $insertarPDOParam["costo"] = "decimal";

            for ($i = 0; $i < count($arrayDetalles["cantidad"]); $i++) { 

                $insertar = array();
                $insertar["comprobacionId"] = $this->id;
                $insertar["cantidad"] = $arrayDetalles["cantidad"][$i];
                $insertar["unidad"] = $arrayDetalles["unidad"][$i];
                $insertar["numeroParte"] = $arrayDetalles["numeroParte"][$i];
                $insertar["concepto"] = $arrayDetalles["concepto"][$i];
                $insertar["costo"] = $arrayDetalles["costo"][$i] ?? 0;

                // Quitar las comas de los campos decimal
                $insertar["cantidad"] = str_replace(',', '', $insertar["cantidad"]);

                $requisicionDetalleId = 0;
                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO comprobacion_gastos_detalles (comprobacionId, cantidad, unidad, numeroParte, concepto, costo) VALUES (:comprobacionId, :cantidad, :unidad, :numeroParte, :concepto, :costo)", $insertar, $insertarPDOParam, $error, $requisicionDetalleId);

                if ( $respuesta ) {

                    $partida = $arrayDetalles["partida"][$i];

                }

            }
            
        }

        return $respuesta;

    }

    function eliminarDetalles(array $arrayDetalles = null)
    {
        $respuesta = false;

        if ( $arrayDetalles ) {

            $eliminarPDOParam = array();
            $eliminarPDOParam["id"] = "integer";
            $eliminarPDOParam["comprobacionId"] = self::$type[$this->keyName];

            for ($i = 0; $i < count($arrayDetalles); $i++) {

                $eliminar = array();
                $eliminar["id"] = $arrayDetalles[$i];
                $eliminar["comprobacionId"] = $this->id;

                $respuesta = Conexion::queryExecute($this->bdName, "DELETE FROM comprobacion_gastos_detalles WHERE id = :id AND comprobacionId = :comprobacionId", $eliminar, $eliminarPDOParam, $error);

            }

        }

        return $respuesta;
    }
}
