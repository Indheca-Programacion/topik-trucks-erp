<?php

namespace App\Models;

if ( file_exists ( "app/Policies/MaquinariaPolicy.php" ) ) {
    require_once "app/Policies/MaquinariaPolicy.php";
} else {
    require_once "../Policies/MaquinariaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\MaquinariaPolicy;

class Maquinaria extends MaquinariaPolicy
{
    static protected $fillable = [
        'empresaId', 'numeroEconomico', 'numeroFactura', 'maquinariaTipoId', 'modeloId', 'year', 'descripcion', 'serie', 'colorId', 'estatusId', 'ubicacion', 'almacenId', 'observaciones','obraId', 'fugas', 'transmision', 'sistema', 'motor', 'pintura', 'seguridad'
    ];

    static protected $type = [
        'id' => 'integer',
        'empresaId' => 'integer',
        'numeroEconomico' => 'string',
        'numeroFactura' => 'string',
        'maquinariaTipoId' => 'integer',
        'modeloId' => 'integer',
        'year' => 'integer',
        'descripcion' => 'string',
        'serie' => 'string',
        'colorId' => 'integer',
        'estatusId' => 'integer',
        'ubicacion' => 'string',
        'almacenId' => 'integer',
        'obraId' => 'integer',
        'observaciones' => 'string',
        'fugas' => 'integer',
        'transmision' => 'integer',
        'sistema' => 'integer',
        'motor' => 'integer',
        'pintura' => 'integer',
        'seguridad' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "maquinarias";

    protected $keyName = "id";

    public $id = null;
    public $empresaId;
    public $numeroEconomico;
    public $numeroFactura;
    public $maquinariaTipoId;
    public $modeloId;
    public $year;
    public $descripcion;
    public $serie;
    public $colorId;
    public $estatusId;
    public $ubicacionId;
    public $almacenId;
    public $observaciones;
    public $checklist;
    public $kits;
    public $obraId;
    public $fugas;
    public $transmision;
    public $sistema;
    public $motor;
    public $pintura;
    public $seguridad;
    public $modelo;
    public $horometros;
    public $consumibles;
    public $servicios;

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR MAQUINARIAS CON FILTRO
    =============================================*/
    public function consultarFiltros($arrayFiltros = array())
    {
        $query = "SELECT    M.*, EM.nombreCorto AS 'empresas.nombreCorto',
                            MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion',
                            MA.descripcion AS 'marcas.descripcion', C.descripcion AS 'colores.descripcion',
                            E.descripcion AS 'estatus.descripcion', U.descripcion AS 'ubicaciones.descripcion',
                            A.descripcion AS 'almacenes.descripcion'
                FROM        maquinarias M
                INNER JOIN  empresas EM ON M.empresaId = EM.id
                INNER JOIN  maquinaria_tipos MT ON M.maquinariaTipoId = MT.id
                INNER JOIN  modelos MO ON M.modeloId = MO.id
                INNER JOIN  marcas MA ON MO.marcaId = MA.id
                LEFT JOIN   colores C ON M.colorId = C.id
                INNER JOIN  estatus E ON M.estatusId = E.id
                INNER JOIN  almacenes A ON M.almacenId = A.id";

        foreach ($arrayFiltros as $key => $value) {
            if ( $key == 0 ) $query .= " WHERE";
            if ( $key > 0 ) $query .= " AND";
            $query .= " {$value['campo']} = {$value['valor']}";
        }

        $query .= " ORDER BY        M.maquinariaTipoId, M.descripcion";

        $respuesta = Conexion::queryAll($this->bdName, $query, $error);

        return $respuesta;
    }

    /*=============================================
    MOSTRAR MAQUINARIAS
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
            "SELECT M.*, EM.nombreCorto AS 'empresas.nombreCorto', MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion', MA.descripcion AS 'marcas.descripcion', C.descripcion AS 'colores.descripcion', E.descripcion AS 'estatus.descripcion'
            FROM $this->tableName M 
            INNER JOIN empresas EM ON M.empresaId = EM.id 
            INNER JOIN maquinaria_tipos MT ON M.maquinariaTipoId = MT.id 
            INNER JOIN modelos MO ON M.modeloId = MO.id 
            INNER JOIN marcas MA ON MO.marcaId = MA.id 
            LEFT JOIN colores C ON M.colorId = C.id 
            INNER JOIN estatus E ON M.estatusId = E.id 
            ORDER BY M.maquinariaTipoId, M.descripcion", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, 
                "SELECT M.*, MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion', MA.descripcion AS 'marcas.descripcion', ( SELECT        CD.horoOdometro
                        FROM            combustible_detalles CD
                        INNER JOIN  combustibles C ON CD.combustibleId = C.id
                        WHERE           CD.maquinariaId = M.id
                        ORDER BY        CONCAT(C.fecha, ' ', C.hora) DESC, CD.id DESC
                        LIMIT           1 ) AS horoOdometroActual
                FROM $this->tableName M 
                INNER JOIN maquinaria_tipos MT ON M.maquinariaTipoId = MT.id 
                INNER JOIN modelos MO ON M.modeloId = MO.id 
                INNER JOIN marcas MA ON MO.marcaId = MA.id 
                WHERE M.$this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->empresaId = $respuesta["empresaId"];
                $this->numeroEconomico = $respuesta["numeroEconomico"];
                $this->maquinariaTipoId = $respuesta["maquinariaTipoId"];
                $this->modeloId = $respuesta["modeloId"];
                $this->year = $respuesta["year"];
                $this->descripcion = $respuesta["descripcion"];
                $this->serie = $respuesta["serie"];
                $this->colorId = $respuesta["colorId"];
                $this->estatusId = $respuesta["estatusId"];
                $this->ubicacion = $respuesta["ubicacion"];
                $this->observaciones = $respuesta["observaciones"];
                $this->fugas = $respuesta["fugas"];
                $this->transmision = $respuesta["transmision"];
                $this->sistema = $respuesta["sistema"];
                $this->motor = $respuesta["motor"];
                $this->pintura = $respuesta["pintura"];
                $this->seguridad = $respuesta["seguridad"];
                $this->horoOdometroActual = $respuesta["horoOdometroActual"];

                if ( file_exists ( "app/Models/Modelo.php" ) ) {
                    require_once "app/Models/Modelo.php";
                } else {
                    require_once "../Models/Modelo.php";
                }
                // require_once "app/Models/Modelo.php";
                $modelo = New Modelo;
                $this->modelo = $modelo->consultar(null, $this->modeloId);
            }

            return $respuesta;

        }

    }

    public function consultarEmpresa($empresaId)
    {
        return Conexion::queryAll($this->bdName, "SELECT M.*, EM.nombreCorto AS 'empresas.nombreCorto', MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion', MA.descripcion AS 'marcas.descripcion', C.descripcion AS 'colores.descripcion', E.descripcion AS 'estatus.descripcion', U.descripcion AS 'ubicaciones.descripcion', A.descripcion AS 'almacenes.descripcion' FROM $this->tableName M INNER JOIN empresas EM ON M.empresaId = EM.id INNER JOIN maquinaria_tipos MT ON M.maquinariaTipoId = MT.id INNER JOIN modelos MO ON M.modeloId = MO.id INNER JOIN marcas MA ON MO.marcaId = MA.id LEFT JOIN colores C ON M.colorId = C.id INNER JOIN estatus E ON M.estatusId = E.id INNER JOIN ubicaciones U ON M.ubicacionId = U.id INNER JOIN almacenes A ON M.almacenId = A.id WHERE M.empresaId = {$empresaId} ORDER BY M.maquinariaTipoId, M.descripcion", $error);
    }

    public function consultarHorometros()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT MH.* FROM maquinaria_horometros MH WHERE MH.maquinariaId = $this->id ORDER BY MH.fecha DESC", $error);
        
        $this->horometros = $resultado;
    }

    public function consultarConsumibles()
    {
        $query = "SELECT E.nombreCorto AS 'empresas.nombreCorto', C.fecha, C.hora,
                        U.descripcion AS 'ubicaciones.descripcion',
                        CONCAT(EM.nombre, ' ', EM.apellidoPaterno, ' ', IFNULL(EM.apellidoMaterno, '')) AS 'empleados.nombreCompleto', CD.*
            FROM        combustible_detalles CD
            INNER JOIN  combustibles C ON CD.combustibleId = C.id
            INNER JOIN  empresas E ON C.empresaId = E.id
            INNER JOIN  ubicaciones U ON CD.ubicacionId = U.id
            INNER JOIN  empleados EM ON CD.empleadoId = EM.id
            WHERE       CD.maquinariaId = {$this->id}
            ORDER BY    C.fecha DESC, C.hora DESC, CD.id DESC";

        $resultado = Conexion::queryAll($this->bdName, $query, $error);
        
        $this->consumibles = $resultado;
    }

    public function consultarServicios()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT S.*, ST.descripcion AS 'servicio_tipos.descripcion', SE.descripcion AS 'servicio_estatus.descripcion' FROM servicios S INNER JOIN servicio_tipos ST ON S.servicioTipoId = ST.id INNER JOIN servicio_estatus SE ON S.servicioEstatusId = SE.id  WHERE S.maquinariaId = $this->id ORDER BY S.fechaSolicitud DESC", $error);
        
        $this->servicios = $resultado;
    }

    public function consultarMaquinasGenerador($generadorId)
    {
        return Conexion::queryAll($this->bdName, "SELECT M.*, EM.nombreCorto AS 'empresas.nombreCorto', MT.descripcion AS 'maquinaria_tipos.descripcion', MO.descripcion AS 'modelos.descripcion', MA.descripcion AS 'marcas.descripcion', C.descripcion AS 'colores.descripcion', E.descripcion AS 'estatus.descripcion', U.descripcion AS 'ubicaciones.descripcion', A.descripcion AS 'almacenes.descripcion' FROM $this->tableName M INNER JOIN empresas EM ON M.empresaId = EM.id INNER JOIN maquinaria_tipos MT ON M.maquinariaTipoId = MT.id INNER JOIN modelos MO ON M.modeloId = MO.id INNER JOIN marcas MA ON MO.marcaId = MA.id LEFT JOIN colores C ON M.colorId = C.id INNER JOIN estatus E ON M.estatusId = E.id INNER JOIN ubicaciones U ON M.ubicacionId = U.id INNER JOIN almacenes A ON M.almacenId = A.id WHERE M.id NOT IN (SELECT GD.fk_maquinaria FROM generador_detalles GD INNER JOIN generadores G ON G.id = GD.fk_generador WHERE G.id = $generadorId) ORDER BY M.maquinariaTipoId, M.descripcion", $error);
    }

    public function consultarChecklist()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT CM.*, CONCAT(U.nombre, ' ', U.apellidoPaterno, ' ', IFNULL(U.apellidoMaterno, '')) AS 'creo',
                                                        case
                                                            when CM.estatus = 1 then 'Completado'
                                                            when CM.estatus = 0 then 'Por atender'
                                                            else 'Sin estatus'
                                                        end as estatus
                                                        FROM checklist_maquinaria CM
                                                        INNER JOIN usuarios U ON CM.usuarioIdCreacion = U.id
                                                        WHERE CM.maquinariaId = $this->id", $error);
        $this->checklist = $resultado;
    }

    public function consultarKits()
    {
        $resultado = Conexion::queryAll($this->bdName, "SELECT K.id as kitId, KM.*, MAT.descripcion as tipoMaquinaria, MO.descripcion as modelo,
                                                            CASE 
                                                                    WHEN P.personaFisica = 1 THEN TRIM(CONCAT(nombre, ' ', IFNULL(P.apellidoPaterno, ''), ' ', IFNULL(P.apellidoMaterno, '')))
                                                                    WHEN P.personaFisica = 0 THEN P.razonSocial 
                                                            END AS proveedor 
                                                      FROM kit_mantenimiento KM
                                                      INNER JOIN maquinaria_tipos MAT ON KM.tipoMaquinaria = MAT.id
                                                      INNER JOIN modelos MO ON KM.modelo = MO.id
                                                      left JOIN proveedores P ON KM.proveedorId = P.id
                                                      INNER JOIN kits_maquinarias K ON KM.id = K.kitId
                                                      WHERE K.maquinariaId = $this->id", $error);
        $this->kits = $resultado;
    }

    public function crear($datos) {

        $arrayPDOParam = array();
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["numeroEconomico"] = self::$type["numeroEconomico"];
        $arrayPDOParam["maquinariaTipoId"] = self::$type["maquinariaTipoId"];
        $arrayPDOParam["modeloId"] = self::$type["modeloId"];
        $arrayPDOParam["year"] = self::$type["year"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["serie"] = self::$type["serie"];
        $arrayPDOParam["colorId"] = self::$type["colorId"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["ubicacion"] = self::$type["ubicacion"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];

        $campos = fCreaCamposInsert($arrayPDOParam);

        $lastId = 0;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName " . $campos, $datos, $arrayPDOParam, $error, $lastId);
        if ( $respuesta ) {
            $this->id = $lastId;
        }
        
        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;

        $arrayPDOParam = array();
        $arrayPDOParam["empresaId"] = self::$type["empresaId"];
        $arrayPDOParam["numeroEconomico"] = self::$type["numeroEconomico"];
        $arrayPDOParam["maquinariaTipoId"] = self::$type["maquinariaTipoId"];
        $arrayPDOParam["modeloId"] = self::$type["modeloId"];
        $arrayPDOParam["year"] = self::$type["year"];
        $arrayPDOParam["descripcion"] = self::$type["descripcion"];
        $arrayPDOParam["serie"] = self::$type["serie"];
        $arrayPDOParam["colorId"] = self::$type["colorId"];
        $arrayPDOParam["estatusId"] = self::$type["estatusId"];
        $arrayPDOParam["ubicacion"] = self::$type["ubicacion"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["fugas"] = self::$type["fugas"]; 
        $arrayPDOParam["transmision"] = self::$type["transmision"];
        $arrayPDOParam["sistema"] = self::$type["sistema"];
        $arrayPDOParam["motor"] = self::$type["motor"]; 
        $arrayPDOParam["pintura"] = self::$type["pintura"]; 
        $arrayPDOParam["seguridad"] = self::$type["seguridad"]; 
        
        $datos["fugas"] = isset($datos["fugas"]) && $datos["fugas"] == "on" ? 1 : 0;
        $datos["transmision"] = isset($datos["transmision"]) && $datos["transmision"] == "on" ? 1 : 0;
        $datos["sistema"] = isset($datos["sistema"]) && $datos["sistema"] == "on" ? 1 : 0;
        $datos["motor"] = isset($datos["motor"]) && $datos["motor"] == "on" ? 1 : 0;
        $datos["pintura"] = isset($datos["pintura"]) && $datos["pintura"] == "on" ? 1 : 0;
        $datos["seguridad"] = isset($datos["seguridad"]) && $datos["seguridad"] == "on" ? 1 : 0;

        $campos = fCreaCamposUpdate($arrayPDOParam);

        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

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

    public function consultarFotos()
    {
        return Conexion::queryAll($this->bdName, "SELECT * FROM maquinaria_fotos WHERE maquinariaId = $this->id", $error);
    }

    public function guardarImagenes($detalle,$fecha)
    {
        $respuesta = array();
        $respuesta["exito"] = false;
        $respuesta["mensaje"] = "No se pudo guardar las imágenes";

        if ( isset($_FILES["images"]) ) {
            $files = $_FILES["images"];
            $total = count($files["name"]);
            $respuesta["total"] = $total;
            $respuesta["archivos"] = array();

            for ($i = 0; $i < $total; $i++) {
                $file = array();
                $file["name"] = $files["name"][$i];
                $file["type"] = $files["type"][$i];
                $file["tmp_name"] = $files["tmp_name"][$i];
                $file["error"] = $files["error"][$i];
                $file["size"] = $files["size"][$i];

                $respuesta["archivos"][$i] = $file;

                $directorio = "/vistas/uploaded-files/maquinaria-fotos/";
                
                do {
                    $ruta = fRandomNameImageFile($directorio, $file["type"]);
                } while ( file_exists($ruta) );

                $nombre = $file["name"];
                
                if ( move_uploaded_file($file["tmp_name"], "../..".$ruta) ) {
                    $datos = array();
                    $datos["maquinariaId"] = $this->id;
                    $datos["ruta"] = $ruta;
                    $datos["nombre"] = $nombre;
                    $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];
                    $datos["fechaCreacion"] = $fecha;
                    $datos["detalle"] = $detalle;

                    $arrayPDOParam = array();
                    $arrayPDOParam["maquinariaId"] = self::$type["id"];
                    $arrayPDOParam["ruta"] = "string";
                    $arrayPDOParam["nombre"] = "string";
                    $arrayPDOParam["usuarioIdCreacion"] = "integer";
                    $arrayPDOParam["fechaCreacion"] = "date";
                    $arrayPDOParam["detalle"] = "integer";

                    $campos = fCreaCamposInsert($arrayPDOParam);

                    $resultado = Conexion::queryExecute($this->bdName, "INSERT INTO maquinaria_fotos " . $campos, $datos, $arrayPDOParam, $error);

                    if ( $resultado ) {
                        $respuesta["exito"] = true;
                        $respuesta["mensaje"] = "Imágenes guardadas correctamente";
                    }
                }
            }
        }

        return $respuesta;
    }

    public function eliminarImagen($id)
    {
        $respuesta = array();
        $respuesta["exito"] = false;
        $respuesta["mensaje"] = "No se pudo eliminar la imagen";

        $datos = array();
        $datos["id"] = $id;

        $arrayPDOParam = array();
        $arrayPDOParam["id"] = self::$type["id"];

        $resultado = Conexion::queryExecute($this->bdName, "DELETE FROM maquinaria_fotos WHERE id = :id", $datos, $arrayPDOParam, $error);

        if ( $resultado ) {
            
            $respuesta["exito"] = true;
            $respuesta["mensaje"] = "Imagen eliminada correctamente";
        }

        return $respuesta;
    }

    /*=============================================
    CONSULTAR IMAGENES POR ID REQUICISION
    =============================================*/
    public function consultarMaquinariaPorRequisicion($valor = null) {

        $respuesta = Conexion::queryUnique($this->bdName, "SELECT 
                                                                S.empresaId,
                                                                S.maquinariaId,
                                                                M.numeroEconomico,
                                                                M.serie,
                                                                M.descripcion AS descripcionMaquinaria,
                                                                MO.descripcion AS modeloMaquinaria,
                                                                MA.descripcion AS marcaMaquinaria
                                                            FROM 
                                                                requisiciones R
                                                            INNER JOIN servicios S ON R.servicioId = S.id
                                                            INNER JOIN maquinarias M ON M.id = S.maquinariaId
                                                            INNER JOIN modelos MO ON MO.id = M.modeloId
                                                            INNER JOIN marcas MA ON MA.id= MO.marcaId
                                                            WHERE 
                                                            R.$this->keyName = $valor", $error);
        if ( $respuesta ) {

            $this->empresaId = $respuesta["empresaId"];
            $this->maquinariaId = $respuesta["maquinariaId"];
            $this->numeroEconomico = $respuesta["numeroEconomico"];
            $this->serie = $respuesta["serie"];
            $this->descripcionMaquinaria = $respuesta["descripcionMaquinaria"];
            $this->modeloMaquinaria = $respuesta["modeloMaquinaria"];
            $this->marcaMaquinaria = $respuesta["marcaMaquinaria"];
        }
        return $respuesta;
    }
}


