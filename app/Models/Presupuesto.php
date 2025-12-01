<?php

namespace App\Models;

if ( file_exists ( "app/Policies/PresupuestoPolicy.php" ) ) {
    require_once "app/Policies/PresupuestoPolicy.php";
} else {
    require_once "../Policies/PresupuestoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\PresupuestoPolicy;

class Presupuesto extends PresupuestoPolicy
{
    static protected $fillable = [
        'maquinariaId', 'clienteId', 'fuente', 'fechaSolicitud', 'ubicacion', 'horasProyectadas', 'estatusId', 'mantenimientoTipoId', 'servicioTipoId', 'descripcion'
    ];

    static protected $type = [
        'id' => 'integer',
        'maquinariaId' => 'integer',
        'clienteId' => 'integer',
        'fuente' => 'string',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "presupuestos";

    protected $keyName = "id";

    public $id = null;
    public $maquinariaId = null;
    public $clienteId = null;
    public $fuente = null;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR PRESUPUESTOS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, "SELECT P.* ,
                                                        M.descripcion AS maquinaria,
                                                        C.nombreCompleto AS cliente,
                                                        (SELECT GROUP_CONCAT(id SEPARATOR ', ') 
                                                         FROM servicios 
                                                         WHERE presupuestoId = P.id) AS folio_servicios,
                                                        concat(U.nombre,' ',U.apellidoMaterno, ' ', ifnull(U.apellidoPaterno, '')) AS creo
                                                        FROM $this->tableName P
                                                        inner join maquinarias M on P.maquinariaId = M.id
                                                        inner join clientes C on P.clienteId = C.id
                                                        inner join usuarios U on P.usuarioIdCreacion = U.id
                                                        ORDER BY P.id DESC", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->clienteId = $respuesta["clienteId"];
                $this->fuente = $respuesta["fuente"];

                require_once "app/Models/ServicioPartida.php";
                $servicioPartida = new ServicioPartida;
                $this->partidas = $servicioPartida->obtenerPartidasPresupuesto($this->id);

            }

            return $respuesta;

        }

    }

    public function obtenerServiciosPresupuesto($presupuestoId) {
        $respuesta = Conexion::queryAll($this->bdName, "SELECT S.* ,
                                                    MT.descripcion AS mantenimientoTipo,
                                                    ST.descripcion AS servicioTipo,
                                                    SE.descripcion AS servicioEstatus
                                                    FROM servicios S
                                                    inner join mantenimiento_tipos MT on S.mantenimientoTipoId = MT.id
                                                    inner join servicio_tipos ST on S.servicioTipoId = ST.id
                                                    inner join servicio_estatus SE on S.servicioEstatusId = SE.id
                                                    WHERE S.presupuestoId = $presupuestoId", $error);
        
        foreach ( $respuesta as $key => $servicio ) {
            $partidas = Conexion::queryAll($this->bdName, "SELECT * FROM servicio_partidas WHERE servicioId = " . $servicio["id"] . " ORDER BY id", $error);
            $precioTotal = 0;
            foreach ( $partidas as $pKey => $partidasItem ) {
                $partidasItem["costoTotal"] = $partidasItem["costo_base"] * $partidasItem["cantidad"]; // Calculo de costo de la partida
                $partidasItem["precioTotal"] = $partidasItem["costoTotal"] + $partidasItem["logistica"] + $partidasItem["mantenimiento"] + $partidasItem["utilidad"]; // El calculo total de la partida
                $partidas[$pKey] = $partidasItem; // Asignacion de partidas a los servicios
                $precioTotal += $partidasItem["precioTotal"]; // Suma del precio total de las partidas
            }
            $respuesta[$key]["subtotal"] = $precioTotal;
            $respuesta[$key]["comisiones"] = $respuesta[$key]["subtotal"] * 0.05; // Ejemplo: 5% de comisiones
            $respuesta[$key]["total"] = $precioTotal + $respuesta[$key]["comisiones"];
            $respuesta[$key]["partidas"] = $partidas;
            $respuesta[$key]["personalAsignado"] = [1];
        }

        return $respuesta;
    }

    public function crear($datos,$imagenes = null) {

        $arrayPDOParam = array();
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["clienteId"] = self::$type["clienteId"];
        $arrayPDOParam["fuente"] = self::$type["fuente"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos , $datos, $arrayPDOParam, $error, $lastId);

        if ( $respuesta ) {
            $this->id = $lastId;
            $this->maquinariaId = $datos["maquinariaId"];
            $this->crearServicios($datos, $imagenes);

        }

        return $respuesta;

    }

    /*=============================================
    Agregar partidas a los servicios del presupuesto
    =============================================*/
    public function agregarPartidaServicio($datos) {
        $arrayPDOParam = array();
        $arrayPDOParam["cantidad"] = self::$type["cantidad"];
        $arrayPDOParam["unidad"] = self::$type["unidad"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["costo_base"] = self::$type["costo_base"];
        $arrayPDOParam["logistica"] = self::$type["logistica"];
        $arrayPDOParam["mantenimiento"] = self::$type["mantenimiento"];
        $arrayPDOParam["utilidad"] = self::$type["utilidad"];
        $arrayPDOParam["presupuestoId"] = self::$type["presupuestoId"];
        $arrayPDOParam["servicioId"] = self::$type["servicioId"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO servicio_partidas ".$campos, $datos, $arrayPDOParam, $error);
    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["clienteId"] = self::$type["clienteId"];
        $arrayPDOParam["fuente"] = self::$type["fuente"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];

        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET $campos WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    function crearServicios($datos, $imagenes = null) {

        if ( isset($datos["mantenimientoTipoId"]) && is_array($datos["mantenimientoTipoId"]) ) {

            foreach ( $datos["mantenimientoTipoId"] as $index => $mantenimientoTipoId ) {
                
                $arrayPDOParam = array();
                $arrayPDOParam["presupuestoId"] = 'integer';
                $arrayPDOParam["mantenimientoTipoId"] = 'integer';
                $arrayPDOParam["servicioTipoId"] = 'integer';
                $arrayPDOParam["descripcion"] = 'string';
                $arrayPDOParam["ubicacion"] = 'string';
                $arrayPDOParam["servicioEstatusId"] = 'integer';
                $arrayPDOParam["maquinariaId"] = 'integer';
                $arrayPDOParam["horasProyectadas"] = 'integer';
                $arrayPDOParam["usuarioIdCreacion"] = 'integer';
                
                $datosInsert = array();
                $datosInsert["horasProyectadas"] = str_replace(',', '', $datos["horasProyectadas"][$index]);
                $datosInsert["mantenimientoTipoId"] = $mantenimientoTipoId;
                $datosInsert["servicioTipoId"] = $datos["servicioTipoId"][$index];
                $datosInsert["descripcion"] = $datos["descripcion"][$index];
                $datosInsert["ubicacion"] = $datos["ubicacion"][$index];
                
                $datosInsert["presupuestoId"] = $this->id;
                $datosInsert["servicioEstatusId"] = 1;
                $datosInsert["maquinariaId"] = $this->maquinariaId;
                $datosInsert["usuarioIdCreacion"] = usuarioAutenticado()["id"];
                $campos = fCreaCamposInsert($arrayPDOParam);
                
                $lastId = 0;
                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO servicios ".$campos , $datosInsert, $arrayPDOParam, $error, $lastId);

                if ( $respuesta ) {

                    // Insertar las imágenes asociadas a este servicio
                    if ( isset($imagenes["imagenes_" . ($index+1)]) ) {
                        if ( isset($imagenes["imagenes_" . ($index+1)]['name'][0]) && $imagenes["imagenes_" . ($index+1)]['name'][0] != '' ) $respuesta = $this->insertarImagenes($imagenes["imagenes_" . ($index+1)], $lastId);
                    }

                }

            }

        }

    }

    function insertarImagenes($archivos, $servicioId) {

        for ($i = 0; $i < count($archivos['name']); $i++) {

            if ( $archivos["tmp_name"][$i] == "" ) continue;

            // Agregar al request el nombre, formato y ruta final del archivo
            $ruta = "";
            // if ( $archivos["tmp_name"][$i] != "" ) {

                $archivo = $archivos["name"][$i];
                $tipo = $archivos["type"][$i];
                $tmp_name = $archivos["tmp_name"][$i];

                // DEFINIR EL DIRECTORIO DONDE VAMOS A GUARDAR LA IMÁGEN
                $directorio = "vistas/uploaded-files/servicios/imagenes/";

                do {
                    $ruta = fRandomNameImageFile($directorio, $tipo);
                } while ( file_exists($ruta) );

            // }
            // Request con el nombre del archivo
            $insertar = array();
            $insertar["servicioId"] = $servicioId;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();
            $arrayPDOParam["servicioId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO servicio_imagenes " . $campos, $insertar, $arrayPDOParam, $error);

            $respuesta = true;
            if ( $respuesta && $ruta != "" ) {
                fSaveImageFile($tmp_name, $tipo, $ruta);
            }

        }

        return $respuesta;

    }
}
