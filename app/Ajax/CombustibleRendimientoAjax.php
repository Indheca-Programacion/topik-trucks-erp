<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
// require_once "../Models/Combustible.php";
require_once "../Controllers/Autorizacion.php";

use App\Conexion;
use App\Route;
use App\Models\Usuario;
// use App\Models\Combustible;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class CombustibleRendimientoAjax
{
    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $ubicacionId;
    public $fechaInicial;
    public $fechaFinal;

    public function consultarFiltros()
    {
        $this->fechaInicial = fFechaSQL($this->fechaInicial);
        // $this->fechaFinal = fFechaSQL($this->fechaFinal);
        $this->fechaFinal = date('Y-m-d', strtotime($this->fechaInicial.' + 6 days'));

        $arrayFechas = array();
        $queryFechas = "";
        $fecha = $this->fechaInicial;
        $numeroDia = 1;
        do {
            $queryFechas .= ", SUM( IF (C.fecha = '{$fecha}', CD.litros, 0) ) AS 'dia{$numeroDia}'";

            $diaSemana = date('w', strtotime($fecha));
            $diaMes = date('d', strtotime($fecha));
            $mes = date('n', strtotime($fecha));            
            switch ($diaSemana) {
                case 0:
                    $nombreDia = 'Domingo';
                    break;
                case 1:
                    $nombreDia = 'Lunes';
                    break;
                case 2:
                    $nombreDia = 'Martes';
                    break;
                case 3:
                    $nombreDia = 'Miércoles';
                    break;
                case 4:
                    $nombreDia = 'Jueves';
                    break;
                case 5:
                    $nombreDia = 'Viernes';
                    break;
                case 6:
                    $nombreDia = 'Sábado';
            }

            array_push($arrayFechas, [ 'nombreDia' => $nombreDia, 'fecha' => $fecha, 'diaSemana' => $diaSemana, 'diaMes' => $diaMes, 'mes' => $mes ]);

            $fecha = date('Y-m-d', strtotime($fecha.' + 1 days'));
            $numeroDia++;
        } while ( $numeroDia <= 7 );

        $query = "SELECT E.nombreCorto AS 'empresas.nombreCorto', M.numeroEconomico,
                        U.descripcion AS 'ubicaciones.descripcion', COUNT(CD.horoOdometro) AS 'countHoroOdometro',
                        MIN(CD.horoOdometro) AS 'minHoroOdometro', MAX(CD.horoOdometro) AS 'maxHoroOdometro',
                        SUM(CD.litros) AS 'sumLitros'";
        $query .= $queryFechas;
        $query .= " FROM        combustible_detalles CD
            INNER JOIN  combustibles C ON CD.combustibleId = C.id
            INNER JOIN  empresas E ON C.empresaId = E.id
            INNER JOIN  maquinarias M ON CD.maquinariaId = M.id
            INNER JOIN  ubicaciones U ON CD.ubicacionId = U.id
            WHERE       C.fecha BETWEEN '{$this->fechaInicial}' AND '{$this->fechaFinal}'";

        if ( $this->empresaId > 0 ) $query .= " AND          E.id = {$this->empresaId}";
        if ( $this->ubicacionId > 0 ) $query .= " AND          U.id = {$this->ubicacionId}";

        $query .= " GROUP BY    E.nombreCorto, M.numeroEconomico, U.descripcion";

        $rendimientos = Conexion::queryAll(CONST_BD_APP, $query, $error);

        $columnas = array();
        // array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "equipo" ]);
        array_push($columnas, [ "data" => "horoOdoInicial" ]);
        array_push($columnas, [ "data" => "horoOdoFinal" ]);
        array_push($columnas, [ "data" => "dia1" ]);
        array_push($columnas, [ "data" => "dia2" ]);
        array_push($columnas, [ "data" => "dia3" ]);
        array_push($columnas, [ "data" => "dia4" ]);
        array_push($columnas, [ "data" => "dia5" ]);
        array_push($columnas, [ "data" => "dia6" ]);
        array_push($columnas, [ "data" => "dia7" ]);
        array_push($columnas, [ "data" => "litros" ]);
        
        $registros = array();
        foreach ($rendimientos as $key => $value) {
            array_push( $registros, [ 
                // "consecutivo" => ($key + 1),
                "empresa" => mb_strtoupper(fString($value["empresas.nombreCorto"])),
                "ubicacion" => mb_strtoupper(fString($value['ubicaciones.descripcion'])),
                "equipo" => mb_strtoupper(fString($value['numeroEconomico'])),
                "horoOdoInicial" => $value["minHoroOdometro"],
                "horoOdoFinal" => ( $value["countHoroOdometro"] == 1 ) ? null : $value["maxHoroOdometro"],
                "dia1" => ( $value["dia1"] == 0 ) ? null : $value["dia1"],
                "dia2" => ( $value["dia2"] == 0 ) ? null : $value["dia2"],
                "dia3" => ( $value["dia3"] == 0 ) ? null : $value["dia3"],
                "dia4" => ( $value["dia4"] == 0 ) ? null : $value["dia4"],
                "dia5" => ( $value["dia5"] == 0 ) ? null : $value["dia5"],
                "dia6" => ( $value["dia6"] == 0 ) ? null : $value["dia6"],
                "dia7" => ( $value["dia7"] == 0 ) ? null : $value["dia7"],
                "litros" => $value["sumLitros"] ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;
        $respuesta['datos']['arrayFechas'] = $arrayFechas;

        echo json_encode($respuesta);
    }
}

$combustibleRendimientoAjax = New CombustibleRendimientoAjax;

if ( isset($_GET["empresaId"]) ) {

    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    $combustibleRendimientoAjax->empresaId = $_GET["empresaId"];
    $combustibleRendimientoAjax->ubicacionId = $_GET["ubicacionId"];
    $combustibleRendimientoAjax->fechaInicial = $_GET["fechaInicial"];
    // $combustibleRendimientoAjax->fechaFinal = $_GET["fechaFinal"];
    $combustibleRendimientoAjax->consultarFiltros();

}
