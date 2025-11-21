<?php

namespace App\Models;

if ( file_exists ( "app/Policies/RequisicionGastoPolicy.php" ) ) {
    require_once "app/Policies/RequisicionGastoPolicy.php";
} else {
    require_once "../Policies/RequisicionGastoPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\RequisicionGastoPolicy;

class RequisicionGasto extends RequisicionGastoPolicy
{
    static protected $fillable = [
        'empresa', 'numero', 'folio', 'servicioEstatusId', 'detalles', 'detalle_imagenes', 'comprobanteArchivos', 'ordenesArchivos', 'observacion', 'facturaArchivos', 'cotizacionArchivos', 'valeArchivos', 'partidasEliminadas', 'actualServicioEstatusId'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresa' => 'integer',
        'numero' => 'integer',
        'folio' => 'string',
        'gasto' => 'integer',
        'estatus' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'usuarioIdResponsable' => 'integer',
        'usuarioIdAlmacen' => 'integer',
        'servicioEstatusId' => 'integer',
        'observacion' => 'string' // Para insertar en la tabla requisicion_observaciones
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "requisicion_gastos";

    protected $keyName = "id";

    public $id = null;
    public $servicioId;
    public $numero;
    public $folio;
    public $servicioEstatusId;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    CONSULTAR ULTIMO VALOR DEL CAMPO numero
    =============================================*/
    public function consultarLastId($empresaId) {

        $query = "SELECT    MAX(R.numero) AS 'numero'
                FROM        {$this->tableName} R
                WHERE       R.empresa = {$empresaId}";

        $respuesta = Conexion::queryUnique($this->bdName, $query, $error);

        return $respuesta;

    }

    /*=============================================
    MOSTRAR REQUISICIONES CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT    distinct R.*, E.nombreCorto AS 'empresas.nombreCorto',
                            SC.descripcion AS 'servicio_centros.descripcion',
                            M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinarias.serie',
                            U.descripcion AS 'ubicaciones.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno',
                            SE.descripcion AS 'servicio_estatus.descripcion', SE.colorTexto AS 'servicio_estatus.colorTexto', SE.colorFondo AS 'servicio_estatus.colorFondo'
                FROM        {$this->tableName} R
                INNER JOIN  servicios S ON R.servicioId = S.id
                INNER JOIN  empresas E ON S.empresaId = E.id
                INNER JOIN requisicion_detalles rd ON rd.requisicionId = R.id 
                INNER JOIN  servicio_centros SC ON S.servicioCentroId = SC.id
                INNER JOIN  maquinarias M ON S.maquinariaId = M.id
                INNER JOIN  ubicaciones U ON S.ubicacionId = U.id
                INNER JOIN  usuarios US ON R.usuarioIdCreacion = US.id
                INNER JOIN  servicio_estatus SE ON R.servicioEstatusId = SE.id";

        if ( count($arrayFiltros) == 0 ) {
            $query .= " WHERE       R.servicioEstatusId <> 4";
        } else {
            $filtroEstatus = false;
            foreach ($arrayFiltros as $key => $value) {
                if ( $value['campo'] == 'R.servicioEstatusId' ) $filtroEstatus = true;

                if ( $key == 0 ) $query .= " WHERE";
                if ( $key > 0 ) $query .= " AND";
                // $query .= " {$value['campo']} = {$value['valor']}";
                $query .= " {$value['campo']} {$value['operador']} {$value['valor']}";
            }
            if ( !$filtroEstatus ) $query .= " AND R.servicioEstatusId <> 4";
        }

        $query .= " ORDER BY    R.fechaCreacion DESC, E.id, SC.id, S.numero DESC, R.numero DESC";
        // return $query;
        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR REQUISICIONES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {
            $fechaActual = date('Y-m-d', strtotime('+1 days'));
            // Calcular la fecha de dos meses
            $fechaInicio = date('Y-m-d', strtotime('-2 months'));
            $query = "SELECT    R.*, E.nombreCorto AS 'empresas.nombreCorto',
                            O.nombreCorto AS 'obra.descripcion',
                            CONCAT(US.nombre, ' ', US.apellidoPaterno, ' ', IFNULL(US.apellidoMaterno, '')) AS 'nombreCompleto',
                            SE.descripcion AS 'servicio_estatus.descripcion', SE.colorTexto AS 'servicio_estatus.colorTexto', SE.colorFondo AS 'servicio_estatus.colorFondo'
                    FROM        {$this->tableName} R
                    INNER JOIN  gastos G ON R.gasto = G.id
                    INNER JOIN  empresas E ON G.empresa = E.id
                    INNER JOIN  obras O ON O.id = G.obra
                    INNER JOIN  usuarios US ON G.encargado = US.id
                    INNER JOIN  servicio_estatus SE ON R.estatus = SE.id
                    WHERE       R.estatus <> 4 AND R.fechaCreacion BETWEEN '$fechaInicio' AND '$fechaActual'
                    ORDER BY    R.fechaCreacion DESC, E.id, R.numero DESC";
            return Conexion::queryAll($this->bdName, $query, $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresa = $respuesta["empresa"];
                $this->numero = $respuesta["numero"];
                $this->folio = $respuesta["folio"];
                $this->servicioEstatusId = $respuesta["estatus"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->usuarioIdResponsable = $respuesta["usuarioIdResponsable"];
                $this->usuarioIdActualizacion = $respuesta["usuarioIdActualizacion"];
                $this->usuarioIdAlmacen = $respuesta["usuarioIdAlmacen"];
                $this->fechaCreacion = $respuesta["fechaCreacion"];

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

    public function consultarObservaciones()
    {
        $query = "SELECT    RO.*, SE.descripcion AS 'servicio_estatus.descripcion',
                            US.nombre AS 'usuarios.nombre', US.apellidoPaterno AS 'usuarios.apellidoPaterno', US.apellidoMaterno AS 'usuarios.apellidoMaterno'
                FROM        requisicion_gasto_observaciones RO
                INNER JOIN  servicio_estatus SE ON RO.servicioEstatusId = SE.id
                INNER JOIN  usuarios US ON RO.usuarioIdCreacion = US.id
                WHERE       RO.requisicionId = {$this->id}
                ORDER BY    RO.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);

        $this->observaciones = $resultado;
    }

    public function consultarDetalles()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT RD.* FROM requisicion_gasto_detalles RD WHERE RD.requisicionId = $this->id ORDER BY RD.id", $error);
        
        $this->detalles = $resultado;
    }

    public function consultarComprobantes() {

        $resultado = Conexion::queryAll($this->bdName, "SELECT RA.* FROM requisicion_gasto_archivos RA WHERE RA.requisicionId = $this->id ORDER BY RA.id", $error);
        
        $this->comprobantesPago = $resultado;

    }

    public function crear($datos) {

        // Buscar el último folio según el campo servicioId
        $lastId = $this->consultarLastId($datos["empresa"]);


        // Agregar al request para especificar el usuario que creó la Requisición
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"] ?? 1;

        // Agregar al request para especificar numero y folio del Servicio
        $datos["numero"] = (int) ($lastId["numero"]??0) + 1;
        $datos["folio"] .= "-".$datos["numero"];

        
        $arrayPDOParam = array();
        $arrayPDOParam["empresa"] = self::$type["empresa"];
        $arrayPDOParam["numero"] = self::$type["numero"];
        $arrayPDOParam["folio"] = self::$type["folio"];
        $arrayPDOParam["gasto"] = self::$type["gasto"];
        $arrayPDOParam["estatus"] = self::$type["estatus"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];
        if(isset($datos["usuarioIdAlmacen"])) $arrayPDOParam["usuarioIdAlmacen"] = self::$type["usuarioIdAlmacen"];
        if(isset($datos["usuarioIdResponsable"])) $arrayPDOParam["usuarioIdResponsable"] = self::$type["usuarioIdResponsable"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $requisicionId = 0;
        
        $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $requisicionId);
        
        if ( $respuesta ) {

            $this->id = $requisicionId;
            $this->folio = $datos["folio"];
            
            $respuesta = $this->insertarDetalles($datos['partidas']);

        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        // Agregar al request para especificar el usuario que actualizó la Requisición
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        // var_dump($datos);
        // var_dump($datos['detalles']);
        // var_dump($datos['detalle_imagenes']);
        // die();

        $arrayPDOParam = array();
        $datos["estatus"] = $datos["servicioEstatusId"];
        if ( isset($datos["servicioEstatusId"]) ) $arrayPDOParam["estatus"] = self::$type["estatus"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        
        //Si el estatus no cambia, no se hace nada
        if ( $datos['actualServicioEstatusId'] !== $datos['servicioEstatusId'] ){
            if ($datos["servicioEstatusId"] == 19) {
                $arrayPDOParam["usuarioIdResponsable"] = self::$type["usuarioIdResponsable"];
                $datos["usuarioIdResponsable"] = usuarioAutenticado()["id"];
            }
    
            if ($datos["servicioEstatusId"] == 23) {
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
                $insertar["requisicionId"] = $this->id;
                $insertar["servicioEstatusId"] = $datos["servicioEstatusId"];
                $insertar["observacion"] = $datos["observacion"];
                $insertar["usuarioIdCreacion"] = $datos["usuarioIdActualizacion"];

                $insertarPDOParam = array();
                $insertarPDOParam["requisicionId"] = self::$type["id"];
                $insertarPDOParam["servicioEstatusId"] = self::$type["servicioEstatusId"];
                $insertarPDOParam["observacion"] = self::$type["observacion"];
                $insertarPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

                $campos = fCreaCamposInsert($insertarPDOParam);

                $respuesta =  Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_gasto_observaciones " . $campos, $insertar, $insertarPDOParam, $error);
            }

            if ( isset($datos['comprobanteArchivos']) && $datos['comprobanteArchivos']['name'][0] != '' ) $respuesta = $this->insertarArchivos($datos['comprobanteArchivos'], 1);
            
        }

        return $respuesta;

    }

    function insertarDetalles(array $arrayDetalles) {

        $respuesta = false;
    
        if ( $arrayDetalles ) {

            $insertarPDOParam = array();
            $insertarPDOParam["requisicionId"] = self::$type[$this->keyName];
            $insertarPDOParam["cantidad"] = "decimal";
            $insertarPDOParam["costo"] = "decimal";
            $insertarPDOParam["unidad"] = "string";
            $insertarPDOParam["numeroParte"] = "string";
            $insertarPDOParam["concepto"] = "string";

            for ($i = 0; $i < count($arrayDetalles["cantidad"]); $i++) { 

                $insertar = array();
                $insertar["requisicionId"] = $this->id;
                $insertar["cantidad"] = $arrayDetalles["cantidad"][$i];
                $insertar["costo"] = $arrayDetalles["costo"][$i];
                $insertar["unidad"] = $arrayDetalles["unidad"][$i];
                $insertar["numeroParte"] = $arrayDetalles["numeroParte"][$i];
                $insertar["concepto"] = $arrayDetalles["concepto"][$i];

                // Quitar las comas de los campos decimal
                $insertar["cantidad"] = str_replace(',', '', $insertar["cantidad"]);
                $campos = fCreaCamposInsert($insertarPDOParam);

                $requisicionDetalleId = 0;
                $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_gasto_detalles ".$campos, $insertar, $insertarPDOParam, $error, $requisicionDetalleId);

            }
            
        }

        return $respuesta;

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
                $directorio = "vistas/uploaded-files/requisiciones/vales-almacen/";
                // $aleatorio = mt_rand(10000000,99999999);
                $extension = '';

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
            $insertar["requisicionId"] = $this->id;
            $insertar["titulo"] = $archivo;
            $insertar["archivo"] = $archivo;
            $insertar["formato"] = $tipo;
            $insertar["ruta"] = $ruta;
            $insertar["usuarioIdCreacion"] = usuarioAutenticado()["id"];

            $arrayPDOParam = array();        
            $arrayPDOParam["requisicionId"] = self::$type[$this->keyName];
            $arrayPDOParam["titulo"] = "string";
            $arrayPDOParam["archivo"] = "string";
            $arrayPDOParam["formato"] = "string";
            $arrayPDOParam["ruta"] = "string";
            $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

            $campos = fCreaCamposInsert($arrayPDOParam);

            $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO requisicion_gasto_archivos " . $campos, $insertar, $arrayPDOParam, $error);

            if ( $respuesta && $ruta != "" ) {
                move_uploaded_file($tmp_name, $ruta);
            }

        }

        return $respuesta;

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;
        // $datos["empresaId"] = $this->empresaId;
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        // $arrayPDOParam["empresaId"] = self::$type["empresaId"];

        // return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id AND empresaId = :empresaId", $datos, $arrayPDOParam, $error);
        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }
}
