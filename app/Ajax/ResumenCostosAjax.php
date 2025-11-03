<?php

namespace App\Ajax;

session_start();

ini_set('display_errors', 1);
ini_set('log_errors', 1);

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/ResumenCostos.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\ResumenCostos;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ResumenCostosAjax
{

    public $obraId;
    public $empresaId;
    public $month;
    public $year;

    public function mostrarTabla()
    {

        $datos = [
            "obraId" => $this->obraId,
            "empresaId" => $this->empresaId,
            "month" => $this->month,
            "year" => $this->year,
        ];

        $resumenCosto = new ResumenCostos;
        $resumenesCostos = $resumenCosto->consultar($datos);
        $totalGeneral = $resumenCosto->consultarTotalPorObra($this->obraId);

        // Ejemplo de meses en minúscula para evitar problemas
        $months = ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'];

        // $years debe venir definido, ejemplo:
        $years = [2023, 2024, 2025];

        // Definir columnas base fijas
        $columnas = [
            ["data" => "consecutivo", "title" => "#"],
            ["data" => "noEconomico", "title" => "Número Económico"],
            ["data" => "descripcion", "title" => "Descripción"]
        ];

        // Agregar columnas dinámicas según filtros
        if ($this->month === "all" && $this->year === "all") {
            // Agrega columnas por años
            foreach ($years as $y) {
                $columnas[] = [
                    "data" => (string)$y,
                    "title" =>  $y
                ];
            }
        } else if ($this->month === "all" && $this->year !== "all") {
            // Agrega columnas por meses si año está seleccionado pero mes es all
            foreach ($months as $m) {
                $columnas[] = [
                    "data" => $m,
                    "title" => ucfirst($m) ." ". $this->year
                ];
            }
        } else if ($this->month !== "all" && $this->year !== "all") {
            $columnas[] = [
                "data" => $this->month,
                "title" => ucfirst($months[$this->month - 1]) ." ". $this->year
            ];
        }
         else if ($this->month !== "all" && $this->year === "all") {
            foreach ($years as $y) {
                $columnas[] = [
                    "data" => $this->month.(string)$y,
                    "title" => ucfirst($months[$this->month - 1]) ." ". $y,
                ];
            }

    
        }

        // Construir los registros (datos) que coinciden con las columnas
        $registros = [];

        foreach ($resumenesCostos as $key => $value) {
            $registro = [
                "consecutivo" => ($key + 1),
                "noEconomico" =>  mb_strtoupper(fString($value["numeroEconomico"])),
                "descripcion" =>  mb_strtoupper(fString($value["descripcion"])),
            ];

            if ($this->month === "all" && $this->year === "all") {
                foreach ($years as $y) {
                    $campo = "total" . $y;
                    $registro[(string)$y] = formatearPrecio($value[$campo] ?? 0);
                }
            } else if ($this->month === "all" && $this->year !== "all") {
                foreach ($months as $i => $m) {
                    $numeroMes = str_pad($i + 1, 2, "0", STR_PAD_LEFT); // AJUSTE AQUÍ
                    $campo = "total" . $numeroMes . "_" . $this->year;
                    $registro[$m] =  formatearPrecio($value[$campo] ?? 0);
                }
            } else if ($this->month !== "all" && $this->year !== "all") {
                $numeroMes = str_pad($this->month, 2, "0", STR_PAD_LEFT); // AJUSTE AQUÍ
                $campo = "total" . $numeroMes . "_" . $this->year;
                $registro[$this->month] =formatearPrecio($value[$campo] ?? 0);
            } else if ($this->month !== "all" && $this->year === "all") {
                foreach ($years as $y) {
                    $numeroMes = str_pad($this->month, 2, "0", STR_PAD_LEFT); // AJUSTE AQUÍ
                    $campo = "total" . $numeroMes . "_" . $y;
                    $registro[$this->month . (string)$y] = formatearPrecio($value[$campo] ?? 0);
                }
            }

            $registros[] = $registro;
        }

        $respuesta = [
            'codigo' => 200,
            'error' => false,
            'datos' => [
                'columnas' => $columnas,
                'registros' => $registros,
                'totalGeneral' => formatearPrecio($totalGeneral)
            ]
        ];
        // Finalmente retornas el JSON con las columnas y datos
        echo json_encode($respuesta);
        exit;
    }

    public function consultarOrdenesDeCompra(){

        $resumenCosto = new ResumenCostos;
        $ordenesCompra = $resumenCosto->consultarOrdenesDeCompra($this->noEconomico,$this->fechaSeleccionada);

        $ordenesCompraMaq = [];
		
        
        foreach ($ordenesCompra as $key => $value) {
            $rutaEdit = Route::names('orden-compra.edit', $value['id']);
            $id = $value['id'];

            $ordenCompra = [
                "id" => $value["id"],
                "justificacion" => fString($value["justificacion"]),
                "fechaCreacion" => fString($value["fechaCreacion"]),
                "total" => fString($value["total"]),
                "verOrden" => "
                <a href='{$rutaEdit}' class=''>
                Orden # {$id} 
                 </a>"
            ];

            $ordenesCompraMaq[] = $ordenCompra;

        }

        $respuesta = [
            'codigo' => 200,
            'error' => false,
            'ordenesCompra' => $ordenesCompraMaq
        ];
        // Finalmente retornas el JSON con las columnas y datos
        echo json_encode($respuesta);
        exit;
    }
}

$ajax = new ResumenCostosAjax();

if ( isset($_POST["accion"]) ) {

        $respuesta["error"] = true;
        $respuesta["errorMessage"] = "Realizó una petición desconocida.";

        echo json_encode($respuesta);
}

 else {

    if (  isset($_GET["noEconomico"]) ) {
        $ajax->noEconomico = $_GET["noEconomico"];
        $ajax->fechaSeleccionada = $_GET["fechaSeleccionada"];

        $ajax->consultarOrdenesDeCompra();

    } else{
        /*=============================================
        BUSCAR RESUMEN COSTOS
        =============================================*/
        $ajax->obraId = $_GET["obraId"];
        $ajax->empresaId = $_GET["empresaId"];
        $ajax->month = $_GET["month"];
        $ajax->year = $_GET["year"];
        $ajax->mostrarTabla();
    }
}