<?php
chdir('/var/www/html/control-mantenimiento/app/Cron/');
require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/ConfiguracionCorreoElectronico.php";
require_once "../Models/ConfiguracionProgramacion.php";
require_once "../Controllers/MailController.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Models/Mensaje.php";
require_once "../Models/Alerta.php";
require_once "../Models/Usuario.php";

use App\Conexion;
use App\Route;
use App\Controllers\Validacion;

use App\Models\Usuario;
use App\Models\ConfiguracionCorreoElectronico;
use App\Models\Alerta;
use App\Models\ConfiguracionProgramacion;
use App\Controllers\Autorizacion;
use App\Controllers\MailController;
use App\Models\Mensaje;


$mensaje = New Mensaje;

$configuracionProgramacion = New ConfiguracionProgramacion;
$configuracionProgramacion->consultar(null , 1);

$servicioTipos = $configuracionProgramacion->servicioTipos;
$servicioTiposText = '';
foreach ($servicioTipos as $key => $value) {
    if ( $key > 0 ) $servicioTiposText .= ', ';
    $servicioTiposText .= $value;
}

$alerta = new Alerta;
$alertas = $alerta->consultar();

foreach ($alertas as $key => $value) {
    $empresa = $value["empresa"];
    $ubicacion = $value["ubicacion"];

    $usuario = new Usuario;

    $arrayUsuarios = json_decode($value["usuarios"]);
    $arrayDestinatarios = array();
    // SE OBTIENEN LOS CORREOS
    foreach ($arrayUsuarios as $key => $value) {
        $usuario->consultar(null,$value);
        $destinatario = [
            "usuarioId" => $usuario->id,
            "correo" => $usuario->correo
        ];

        array_push($arrayDestinatarios, $destinatario);
    }
    
    $query = "SELECT    P.horoOdometroUltimo, P.cantidadSiguienteServicio, UN.nombreCorto AS 'unidades.nombreCorto',
                    M.numeroEconomico AS 'maquinarias.numeroEconomico', M.serie AS 'maquinaria.serie',
                    MT.nombreCorto AS 'maquinaria.equipo', MO.descripcion AS 'maquinaria.modelo', MA.descripcion AS 'maquinaria.marca',
                    U.descripcion AS 'ubicaciones.descripcion',
                    ST.descripcion AS 'servicio_tipos.descripcion'
        FROM        programaciones P
        INNER JOIN  maquinarias M ON P.maquinariaId = M.id
        INNER JOIN	maquinaria_tipos MT ON MT.id = M.maquinariaTipoId
        INNER JOIN	modelos MO ON MO.id = M.modeloId
        INNER JOIN  empresas E ON M.empresaId = E.id
        INNER JOIN	marcas MA ON MA.id = MO.marcaId
        INNER JOIN  ubicaciones U ON M.ubicacionId = U.id
        INNER JOIN  estatus ES ON M.estatusId = ES.id
        INNER JOIN  servicio_tipos ST ON P.servicioTipoId = ST.id
        INNER JOIN unidades UN ON UN.id = ST.unidadId
        WHERE ST.id IN ( {$servicioTiposText} )";

    if ( $empresa > 0 ) $query .= " AND         E.id = {$empresa}";
    if ( $ubicacion > 0 ) $query .= " AND         U.id = {$ubicacion}";
    
    $query .= " ORDER BY     E.nombreCorto, M.numeroEconomico";
    
    $programaciones = Conexion::queryAll(CONST_BD_APP, $query, $error);
    include "../../reportes/programacion.php";

    $configuracionCorreoElectronico = New ConfiguracionCorreoElectronico;
    if ( $configuracionCorreoElectronico->consultar(null , 1) ) {
        
        $mensajeHTML = "<div style='width: 100%; background: #eee; position: relative; font-family: sans-serif; padding-top: 40px; padding-bottom: 40px'>
        
                <div style='position: relative; margin: auto; width: 600px; background: white; padding: 20px'>
        
                    <center>
        
                        <h3 style='font-weight: 100; color: #999'>Programacion de Mantenimiento</h3>
        
                        <hr style='border: 1px solid #ccc; width: 80%'>
                        
                        <br>
        
                        <hr style='border: 1px solid #ccc; width: 80%'>
        
                    </center>
        
                </div>
                    
            </div>";
        
        $datos = [ "mensajeTipoId" => 3,
                    "mensajeEstatusId" => 1,
                    "asunto" => "MANTENIMIENTO SEMANA ".date('W'),
                    "correo" => $configuracionCorreoElectronico->visualizacionCorreo,
                    "mensaje" => "Se ha enviado la programacion de mantenimiento.",
                    "liga" => '',
                    "destinatarios" => $arrayDestinatarios
        ];
        
        if ( $mensaje->crear($datos) ) {
            $mensaje->consultar(null , $mensaje->id);
            $mensaje->mensajeHTML = $mensajeHTML;
            $mensaje->attachment = true;
        
            $enviar = MailController::send($mensaje);
            if ( $enviar["error"] ) {$mensaje->noEnviado([ "error" => $enviar["errorMessage"] ]);
            echo 'si se pudo';}
            else $mensaje->enviado();
        }

    }

}




?>