<?php

namespace App\Models;

use App\Conexion;
use PDO;

    class ResumenCostos
{
    static protected $fillable = [
        'id'
    ];

    static protected $type = [
        'id' => 'integer',
    ];

    protected $bdName = CONST_BD_SECURITY;
    protected $tableName = "ResumenCostos";    

    protected $keyName = "id";

    public $id = null;

    static public function fillable() {
        return self::$fillable;
    }

    public function consultar($datos) {

        $empresaId = $datos["empresaId"];
        $obraId =  $datos["obraId"];
        $month =  $datos["month"];
        $year =  $datos["year"];

        // Inicializamos la parte del SELECT dinámico
        $selectExtras = "";

        if ($month === "all" && $year === "all") {
            $selectExtras = ",
                SUM(CASE WHEN YEAR(OC.fechaRequerida) = 2023 THEN OCD.cantidad * OCD.importeUnitario ELSE 0 END) AS total2023,
                SUM(CASE WHEN YEAR(OC.fechaRequerida) = 2024 THEN OCD.cantidad * OCD.importeUnitario ELSE 0 END) AS total2024,
                SUM(CASE WHEN YEAR(OC.fechaRequerida) = 2025 THEN OCD.cantidad * OCD.importeUnitario ELSE 0 END) AS total2025";
                
        }else if ($month === "all" && $year !== "all") {

            // Array de números de mes con padding para mantener formato 01-12
            for ($i = 1; $i <= 12; $i++) {
                $mesPadded = str_pad($i, 2, "0", STR_PAD_LEFT); // "01", "02", ..., "12"
                $selectExtras .= ",
                    SUM(CASE WHEN YEAR(OC.fechaRequerida) = {$year} AND MONTH(OC.fechaRequerida) = {$i} 
                        THEN OCD.cantidad * OCD.importeUnitario ELSE 0 END) AS total{$mesPadded}_{$year}";
            }

        }else if ($month !== "all" && $year !== "all") {
            $mesPadded = str_pad($month, 2, "0", STR_PAD_LEFT); // Asegura formato "01", "02", ..., "12"
            $selectExtras .= ",
                SUM(CASE WHEN YEAR(OC.fechaRequerida) = {$year} AND MONTH(OC.fechaRequerida) = {$month} 
                    THEN OCD.cantidad * OCD.importeUnitario ELSE 0 END) AS total{$mesPadded}_{$year}";
        }else if ($month !== "all" && $year === "all") {
            $currentYear = date('Y');
            $mesPadded = str_pad($month, 2, "0", STR_PAD_LEFT); // "01"..."12"

            for ($y = $currentYear - 2; $y <= $currentYear; $y++) {
                $selectExtras .= ",
                    SUM(CASE WHEN YEAR(OC.fechaRequerida) = {$y} AND MONTH(OC.fechaRequerida) = {$month} 
                        THEN OCD.cantidad * OCD.importeUnitario ELSE 0 END) AS total{$mesPadded}_{$y}";
            }
        }

        $sql = "SELECT 
            M.numeroEconomico,
            M.descripcion
            $selectExtras
        FROM servicios S
        INNER JOIN maquinarias M ON M.id = S.maquinariaId
        LEFT JOIN requisiciones R ON S.id = R.servicioId
        LEFT JOIN ordencompra OC ON R.id = OC.requisicionId
        LEFT JOIN ordencompra_detalles OCD ON OC.id = OCD.ordenId
        LEFT JOIN requisicion_detalles RD ON RD.id = OCD.partidaId
        WHERE
            S.obraId = $obraId
        AND 
            ($empresaId = 0 OR S.empresaId = $empresaId)
        GROUP BY M.numeroEconomico, M.descripcion
        ";

        return Conexion::queryAll($this->bdName, $sql, $error);
    }

    public function consultarTotalPorObra($id){

        $sql = "SELECT 
                    SUM(OCD.cantidad * OCD.importeUnitario) AS totalGeneral
                FROM 
                    servicios S
                INNER JOIN 
                    maquinarias M ON M.id = S.maquinariaId
                LEFT JOIN 
                    requisiciones R ON S.id = R.servicioId
                LEFT JOIN 
                    ordencompra OC ON R.id = OC.requisicionId
                LEFT JOIN 
                    ordencompra_detalles OCD ON OC.id = OCD.ordenId
                WHERE 
                S.obraId  = $id
        ";

       $respuesta = Conexion::queryUnique($this->bdName, $sql, $error);

       return $respuesta["totalGeneral"];

    }


    public function crear($datos) {



    }

    public function actualizar($datos) {


    }

    public function eliminar() {
    }

    public function consultarOrdenesDeCompra($noEconomico,$fechaSeleccionada){

        $mes = null;
        $anio = null;

        // Separar por espacio
        $partes = explode(" ", $fechaSeleccionada);

        if (count($partes) == 2) {
            $mesTexto = strtolower($partes[0]); // Para manejar mayúsculas
            $anio = $partes[1];
            
            // Convertir mes a número
            $meses = [
                "enero" => "01",
                "febrero" => "02",
                "marzo" => "03",
                "abril" => "04",
                "mayo" => "05",
                "junio" => "06",
                "julio" => "07",
                "agosto" => "08",
                "septiembre" => "09",
                "octubre" => "10",
                "noviembre" => "11",
                "diciembre" => "12"
            ];

            $mes = $meses[$mesTexto] ?? null;

            if ($mes === null) {
                exit;
            }
            } elseif (count($partes) == 1) {
                $anio = $partes[0];
            } else {
                exit;
            }

        $sql = "
            SELECT 
                OC.*,
                OCD.cantidad * OCD.importeUnitario AS total
            FROM 
                maquinarias M
            LEFT JOIN 
                servicios S ON M.id = S.maquinariaId
            LEFT JOIN 
                requisiciones R ON R.servicioId = S.id
            LEFT JOIN 
                ordencompra OC ON OC.requisicionId = R.id
            LEFT JOIN 
                ordencompra_detalles OCD ON OCD.ordenId = OC.id
            WHERE 
                M.numeroEconomico = '$noEconomico'
                AND YEAR(OC.fechaRequerida) = '$anio'
        ";

        if ($mes !== null) {
            $sql .= " AND MONTH(OC.fechaRequerida) = '$mes'";
        }

        $respuesta = Conexion::queryAll($this->bdName, $sql, $error); // query() si esperas varios resultados

        return $respuesta;

    }
}
