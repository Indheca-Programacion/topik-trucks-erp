<?php

chdir('/home/atiberna/public_html/control-mantenimiento/app/Cron/');
require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

use App\Conexion;
use App\Route;

$query="SELECT D.id, D.hmr AS 'horas_motor', D.rr AS 'horas_ralenti' , MA.numeroEconomico
FROM desempeno D
INNER JOIN generador_detalles GD ON GD.id = D.generador_detalle
INNER JOIN generadores G ON G.id = GD.fk_generador
INNER JOIN maquinarias MA ON MA.id = GD.fk_maquinaria
WHERE G.mes = '" . date('Y-m-01') . "'";

$respuesta = Conexion::queryAll(CONST_BD_APP, $query, $error);
if (!$respuesta || count($respuesta) === 0) {
    exit;
}

$url = "https://api.service24gps.com/api/v1/gettoken"; // Cambia por la URL de la API
$data = [
    'apikey' => 'db5d473dcc762a75a885c7dd75d430b4',
    'token' => 'O1KoW3DhAeGpPwouUH3TgdWs51DZRzQg7rys6dOhCyEio6MsIeq/5VX5odsRHgQg',
    'username' => 'ArrendadoraTibernal', // Cambia por el ID que necesites
    'password' => 'Arrendadora123', // Cambia por el valor de horas_motor
];

// Inicializa cURL
$ch = curl_init($url);

// Configura opciones de cURL
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true); // Usa POST, cambia a false si es GET
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

// Ejecuta la petición
$response = curl_exec($ch);

// Maneja errores
if (curl_errno($ch)) {
    echo 'Error en cURL: ' . curl_error($ch);
} else {
    // Procesa la respuesta
    $apiResult = json_decode($response, true);

    $data["report_id"] = "70201";
    $data["token"] = $apiResult["data"];

    $hm = curl_init("https://api.service24gps.com/api/v1/onreports/getScheduledReportResult");
    curl_setopt($hm, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($hm, CURLOPT_POST, true); // Usa POST, cambia a false si es GET
    curl_setopt($hm, CURLOPT_POSTFIELDS, http_build_query($data));

    $response = curl_exec($hm);
    
    $data["report_id"] = "70285";
    $tr = curl_init("https://api.service24gps.com/api/v1/onreports/getScheduledReportResult");
    curl_setopt($tr, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($tr, CURLOPT_POST, true); // Usa POST, cambia a false si es GET
    curl_setopt($tr, CURLOPT_POSTFIELDS, http_build_query($data));

    // Obtener Tiempo ralenti
    $response_ralenti = curl_exec($tr); 

    // Ejecuta la petición

    $tiempo_ralenti = json_decode($response_ralenti, true);

    $horas_motor = json_decode($response, true);

    $resultadosHoras_motor = $horas_motor["data"]["result"];
    $resultadosHoras_ralenti = $tiempo_ralenti["data"]["result"];

    $arrayFinal = [];

    foreach ($resultadosHoras_motor as $key => $value) {
        $maquinariaTTS = strtolower(explode(' ', $value["idvehiculo"])[0]);
        foreach ($respuesta as $key => $maquinaria) {
            $maquinariaDesempeno = strtolower(explode(' ', $maquinaria["numeroEconomico"])[0]);
            if ($maquinariaTTS == $maquinariaDesempeno) {

                if ($value["fecha"] == "Total") {
                    // Crear nuevo registro si no existe
                    $arrayFinal[$maquinariaDesempeno] = [
                        "id" => $maquinaria["id"],
                        "numeroEconomico" => $maquinaria["numeroEconomico"],    
                        "horas_motor" => convertirHorasANumero($value["horas"])
                    ];
                }

                break;
            }
        }
    }

    foreach ($resultadosHoras_ralenti as $key => $value) {
        $maquinariaTTS = strtolower(explode(' ', $value["idvehiculo"])[0]);
        foreach ($respuesta as $key => $maquinaria) {
            $maquinariaDesempeno = strtolower(explode(' ', $maquinaria["numeroEconomico"])[0]);
            if ($maquinariaTTS == $maquinariaDesempeno) {

                if ($value["estado"] == "Inmovil/Encendido") {
                    // Crear nuevo registro si no existe
                    $arrayFinal[$maquinariaDesempeno]["horas_ralenti"] = convertirHorasANumeroFormato($value["tiempoif"]);
                }

                break;
            }
        }
    }

    $respuesta = false;
    if (empty($arrayFinal)) {
        echo "No se encontraron coincidencias entre las maquinarias de desempeño y las de la API.";
    } else {
        // Actualizar la base de datos con los resultados
        foreach ($arrayFinal as $maquinaria) {
            $arrayPDOParam = array();
            $arrayPDOParam["horas_motor"] = "decimal";
            $arrayPDOParam["horas_ralenti"] = "decimal";
            $arrayPDOParam["id"] = "integer";

            $queryUpdate = "UPDATE desempeno SET hmr = :horas_motor, rr = :horas_ralenti WHERE id = :id";
            $respuesta = Conexion::queryExecute(CONST_BD_APP, $queryUpdate,$maquinaria, $arrayPDOParam, $error);
        }
    }

    if ($respuesta) {
        echo "Actualización exitosa.";
    } else {
        echo "Error al actualizar la base de datos: " . $error;
    }

}

function convertirHorasANumeroFormato ($cadena) {
    // Extrae horas, minutos y segundos usando expresiones regulares
    preg_match('/(?:(\d+)h)?\s*(?:(\d+)m)?\s*(?:(\d+)s)?/', $cadena, $matches);

    $horas = isset($matches[1]) ? (int)$matches[1] : 0;
    $minutos = isset($matches[2]) ? (int)$matches[2] : 0;
    $segundos = isset($matches[3]) ? (int)$matches[3] : 0;

    // Convierte todo a horas decimales
    $resultado = $horas + ($minutos / 60) + ($segundos / 3600);

    // Redondea a un decimal
    return round($resultado, 1);
}

function convertirHorasANumero($cadena) {
    // Espera formato HH:MM:SS
    list($horas, $minutos, $segundos) = array_map('intval', explode(':', $cadena));

    // Convierte todo a horas decimales
    $resultado = $horas + ($minutos / 60) + ($segundos / 3600);

    // Redondea a dos decimales
    return round($resultado, 2);
}

// Cierra la sesión cURL
curl_close($ch);
curl_close($hm);

?>