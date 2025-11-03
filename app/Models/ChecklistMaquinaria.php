<?php

namespace App\Models;

if ( file_exists ( "app/Policies/ChecklistMaquinariaPolicy.php" ) ) {
    require_once "app/Policies/ChecklistMaquinariaPolicy.php";
} else {
    require_once "../Policies/ChecklistMaquinariaPolicy.php";
}

use App\Conexion;
use PDO;
use App\Policies\ChecklistMaquinariaPolicy;

class ChecklistMaquinaria extends ChecklistMaquinariaPolicy
{
    static protected $fillable = [
        'obraId', 'ubicacionId', 'horometroInicial', 'maquinariaId', 'fecha', 'horometroFinal', 'usuarioIdCreacion', 'observaciones', 'combustibleInicial', 'combustibleFinal', 'acMotor', 'acHidraulico', 'acTransmision', 'anticongelante', 'acMalacatePrinc', 'acMalacateAux'
    ];

    static protected $type = [
        'id' => 'integer',
        'obraId' => 'integer',
        'ubicacionId' => 'integer',
        'horometroInicial' => 'decimal',
        'maquinariaId' => 'integer',
        'usuarioIdCreacion' => 'integer',
        'horometroFinal' => 'decimal',
        'observaciones' => 'string',
        'estatus' => 'integer',
        'usuarioIdActualizacion' => 'integer',
        'combustibleInicial' => 'integer',
        'combustibleFinal' => 'integer',
        'fecha' => 'date',
        'acMotor' => 'integer',
        'acHidraulico' => 'integer',
        'acTransmision' => 'integer',
        'anticongelante' => 'integer',
        'acMalacatePrinc' => 'integer',
        'acMalacateAux' => 'integer',
        'usuarioIdAutorizacion' => 'integer',
        'usuarioIdAutorizacionCliente' => 'integer'
    ];

    protected $bdName = CONST_BD_APP;
    protected $tableName = "checklist_maquinaria";

    protected $keyName = "id";

    public $id = null;    
    public $obraId;
    public $ubicacionId;
    public $horometroInicial;
    public $maquinariaId;
    public $fecha;
    public $usuarioIdCreacion;
    public $horometroFinal;
    public $observaciones;
    

    static public function fillable() {
        return self::$fillable;
    }

    /*=============================================
    MOSTRAR COLORES
    =============================================*/
    public function consultar($item = null, $valor = null) {

        if ( is_null($valor) ) {

            return Conexion::queryAll($this->bdName, 
                                                    "SELECT CM.*, M.numeroEconomico as numeroEconomico, M.descripcion as descripcionMaquinaria, concat(US.nombre, ' ', US.apellidoPaterno, ' ', US.apellidoMaterno) as usuarioCreacion,
                                                    CASE
                                                        WHEN CM.estatus = 1 THEN 'Completado'
                                                        WHEN CM.estatus = 0 THEN 'Por atender'
                                                        ELSE 'Sin estatus'
                                                    END as estatus,
                                                    U.descripcion as ubicacion, O.descripcion as obra
                                                    
                                                    FROM $this->tableName CM
                                                    inner join maquinarias M on M.id = CM.maquinariaId
                                                    INNER JOIN usuarios US on US.id = CM.usuarioIdCreacion
                                                    INNER JOIN ubicaciones U on U.id = CM.ubicacionId
                                                    INNER JOIN obras O on O.id = CM.obraId
                                                    order by CM.estatus desc", $error);

        } else {

            if ( is_null($item) ) {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $this->keyName = $valor", $error);

            } else {

                $respuesta = Conexion::queryUnique($this->bdName, "SELECT * FROM $this->tableName WHERE $item = '$valor'", $error);

            }            

            if ( $respuesta ) {
                $this->id = $respuesta["id"];
                $this->obraId = $respuesta["obraId"];
                $this->ubicacionId = $respuesta["ubicacionId"];
                $this->horometroInicial = $respuesta["horometroInicial"];
                $this->horometroFinal = $respuesta["horometroFinal"];
                $this->maquinariaId = $respuesta["maquinariaId"];
                $this->fecha = $respuesta["fecha"];
                $this->usuarioIdCreacion = $respuesta["usuarioIdCreacion"];
                $this->observaciones = $respuesta["observaciones"];
                $this->combustibleInicial = $respuesta["combustibleInicial"];
                $this->combustibleFinal = $respuesta["combustibleFinal"];
                $this->acMotor = $respuesta["acMotor"];
                $this->acHidraulico = $respuesta["acHidraulico"];
                $this->acTransmision = $respuesta["acTransmision"];
                $this->anticongelante = $respuesta["anticongelante"];
                $this->acMalacatePrinc = $respuesta["acMalacatePrinc"];
                $this->acMalacateAux = $respuesta["acMalacateAux"];
                $this->usuarioIdAutorizacion = $respuesta["usuarioIdAutorizacion"];
                $this->usuarioIdAutorizacionCliente = $respuesta["usuarioIdAutorizacionCliente"];
                
            }

            return $respuesta;

        }

    }

    /*=============================================
    CONSULTAR OPERADORES
    =============================================*/
    public function consultarOperadores() {
        $id = usuarioAutenticado()["id"];
        return Conexion::queryAll($this->bdName, 
                                                    "SELECT CM.*, M.numeroEconomico as numeroEconomico, M.descripcion as descripcionMaquinaria, concat(US.nombre, ' ', US.apellidoPaterno, ' ', US.apellidoMaterno) as usuarioCreacion,
                                                    CASE
                                                        WHEN CM.estatus = 1 THEN 'Completado'
                                                        WHEN CM.estatus = 0 THEN 'Por atender'
                                                        ELSE 'Sin estatus'
                                                    END as estatus,
                                                    U.descripcion as ubicacion, O.descripcion as obra
                                                    
                                                    FROM $this->tableName CM
                                                    inner join maquinarias M on M.id = CM.maquinariaId
                                                    INNER JOIN usuarios US on US.id = CM.usuarioIdCreacion
                                                    INNER JOIN ubicaciones U on U.id = CM.ubicacionId
                                                    INNER JOIN obras O on O.id = CM.obraId
                                                    WHERE CM.usuarioIdCreacion = $id
                                                    order by CM.estatus desc", $error);

    }

    public function crear($datos) {

        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        $datos["horometroInicial"] = str_replace(',', '', $datos["horometroInicial"]);
        $datos["usuarioIdCreacion"] = usuarioAutenticado()["id"];

        $arrayPDOParam = array();        
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["horometroInicial"] = self::$type["horometroInicial"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["usuarioIdCreacion"] = self::$type["usuarioIdCreacion"];

        $campos = fCreaCamposInsert($arrayPDOParam);
        $lastInsertId = 0;
        $sql = "INSERT INTO $this->tableName ".$campos;
        $respuesta = Conexion::queryExecute($this->bdName, "INSERT INTO $this->tableName ".$campos, $datos, $arrayPDOParam, $error, $lastInsertId);

        if($respuesta) {
            $this->id = $lastInsertId;
        }

        return $respuesta;

    }

    public function actualizar($datos) {

        // Agregar al request para actualizar el registro
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam["maquinariaId"] = self::$type["maquinariaId"];
        $arrayPDOParam["obraId"] = self::$type["obraId"];
        $arrayPDOParam["ubicacionId"] = self::$type["ubicacionId"];
        $arrayPDOParam["horometroInicial"] = self::$type["horometroInicial"];
        $arrayPDOParam["fecha"] = self::$type["fecha"];
        $arrayPDOParam["usuarioIdActualizacion"] = self::$type["usuarioIdActualizacion"];
        $arrayPDOParam["horometroFinal"] = self::$type["horometroFinal"];
        $arrayPDOParam["observaciones"] = self::$type["observaciones"];
        $arrayPDOParam["combustibleInicial"] = self::$type["combustibleInicial"];
        $arrayPDOParam["combustibleFinal"] = self::$type["combustibleFinal"];
        $arrayPDOParam["acMotor"] = self::$type["acMotor"];
        $arrayPDOParam["acHidraulico"] = self::$type["acHidraulico"];
        $arrayPDOParam["acTransmision"] = self::$type["acTransmision"];
        $arrayPDOParam["anticongelante"] = self::$type["anticongelante"];
        $arrayPDOParam["acMalacatePrinc"] = self::$type["acMalacatePrinc"];
        $arrayPDOParam["acMalacateAux"] = self::$type["acMalacateAux"];
        
        $datos["horometroInicial"] = str_replace(',', '', $datos["horometroInicial"]);
        $datos["horometroFinal"] = str_replace(',', '', $datos["horometroFinal"]);
        $datos["fecha"] = fFechaSQL($datos["fecha"]);
        $datos["usuarioIdActualizacion"] = usuarioAutenticado()["id"];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    public function eliminar() {

        // Agregar al request para eliminar el registro
        $datos = array();
        $datos[$this->keyName] = $this->id;        
        
        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];

        return Conexion::queryExecute($this->bdName, "DELETE FROM $this->tableName WHERE id = :id", $datos, $arrayPDOParam, $error);

    }

    /*=============================================
    GUARDAR RESULTADOS DE CHECKLIST
    =============================================*/
    public function guardar($datos){
        $arrayPDOParam = array();
        $arrayPDOParam["tareaId"] = "integer";
        $arrayPDOParam["respuesta"] = "integer";
        $arrayPDOParam["checklist_maquinaria"] = "integer";

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO checklist_respuestas ".$campos, $datos, $arrayPDOParam, $error);
    }

    /*=============================================
    GUARDAR OBSERVACIONES
    =============================================*/
    public function guardarObservaciones($datos) {
        $arrayPDOParam = array();
        $arrayPDOParam["sectionId"] = "integer";
        $arrayPDOParam["observaciones"] = "string";
        $arrayPDOParam["checklist_maquinaria"] = "integer";

        $campos = fCreaCamposInsert($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "INSERT INTO checklist_observaciones ".$campos, $datos, $arrayPDOParam, $error);
    }

    /*=============================================
    AUTORIZAR CHECKLIST
    =============================================*/
    public function cambiarEstatus() {
        $datos = array();
        $datos["id"] = $this->id;
        $datos["estatus"] = 1;
        $arrayPDOParam = array();
        $arrayPDOParam["id"] = "integer";
        $arrayPDOParam["estatus"] = "integer";

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }

    /*=============================================
    GUARDAR IMAGENES
    =============================================*/
    public function guardarImagenes($checklistId) {
        $respuesta = array();
        $respuesta["exito"] = false;
        $respuesta["mensaje"] = "No se pudo guardar las imágenes";

        if ( isset($_FILES["file"]) ) {
            
            $files = $_FILES["file"];
            $respuesta["archivos"] = array();

            $file = array();
            $file["name"] = $files["name"];
            $file["type"] = $files["type"];
            $file["tmp_name"] = $files["tmp_name"];
            $file["error"] = $files["error"];
            $file["size"] = $files["size"];

            $directorio = "/vistas/uploaded-files/checklist-fotos/";
            if (!is_dir("../.." . $directorio)) {
                mkdir("../.." . $directorio, 0777, true);
            }
            
            do {
                $ruta = fRandomNameImageFile($directorio, $file["type"]);
            } while ( file_exists($ruta) );

            $nombre = $file["name"];
            
            if ( move_uploaded_file($file["tmp_name"], "../..".$ruta) ) {
                $datos = array();
                $datos["checklist_maquinariaId"] = $checklistId;
                $datos["titulo"] = $nombre;
                $datos["ruta"] = $ruta;

                $arrayPDOParam = array();
                $arrayPDOParam["checklist_maquinariaId"] = self::$type["id"];
                $arrayPDOParam["titulo"] = "string";
                $arrayPDOParam["ruta"] = "string";

                $campos = fCreaCamposInsert($arrayPDOParam);

                $resultado = Conexion::queryExecute($this->bdName, "INSERT INTO checklist_imagenes " . $campos, $datos, $arrayPDOParam, $error);

                if ( $resultado ) {
                    $respuesta["exito"] = true;
                    $respuesta["mensaje"] = "Imágenes guardadas correctamente";
                }
            }
            
        }

        return $respuesta;
    }

    /*=============================================
    OBTENER IMAGENES
    =============================================*/
    public function obtenerImagenes($checklistId) {
        $respuesta = array();
        $respuesta["exito"] = false;
        $respuesta["imagenes"] = array();

        return Conexion::queryAll($this->bdName, "SELECT * FROM checklist_imagenes WHERE checklist_maquinariaId = $checklistId", $error);

    }

    /*=============================================
    AUTORIZAR CHECKLIST
    =============================================*/
    public function autorizar($checklistId, $auth = "indheca") {
        $datos = array();
        $datos[$this->keyName] = $checklistId;
        $usuarioId = $auth == "indheca" ? "usuarioIdAutorizacion" : "usuarioIdAutorizacionCliente";
        $datos[$usuarioId] = usuarioAutenticado()["id"]; // Asignar el ID del usuario que autoriza

        $arrayPDOParam = array();
        $arrayPDOParam[$this->keyName] = self::$type[$this->keyName];
        $arrayPDOParam[$usuarioId] = self::$type[$usuarioId];

        $campos = fCreaCamposUpdate($arrayPDOParam);

        return Conexion::queryExecute($this->bdName, "UPDATE $this->tableName SET " . $campos . " WHERE id = :id", $datos, $arrayPDOParam, $error);
    }
}
