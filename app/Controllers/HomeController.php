<?php

namespace App\Controllers;

require_once "app/Models/Indicador.php";
require_once "app/Models/Usuario.php";
require_once "app/Models/Maquinaria.php";
require_once "app/Models/Servicio.php";
require_once "app/Models/Requisicion.php";
require_once "app/Models/OrdenCompra.php";
require_once "app/Models/RequisicionGasto.php";
require_once "app/Models/Tarea.php";
require_once "app/Models/Gastos.php";
require_once "app/Models/ConfiguracionProgramacion.php";
require_once "app/Controllers/Autorizacion.php";

use App\Conexion;
use App\Route;

use App\Models\Indicador;
use App\Models\Usuario;
use App\Models\Maquinaria;
use App\Models\Servicio;
use App\Models\Requisicion;
use App\Models\OrdenCompra;
use App\Models\RequisicionGasto;
use App\Models\Tarea;
use App\Models\Gastos;
use App\Models\ConfiguracionProgramacion;

class HomeController
{
    public function index()
    {
        if ( !usuarioAutenticado() ) {
            include "vistas/modulos/plantilla.php"; // plantilla.php redireccionará a la página de ingreso
            return;
        }

        // Validar Autorizacion
        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

        if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "indicadores", "ver") ) {

            $contenido = array('modulo' => 'vistas/modulos/inicio.php');
            include "vistas/modulos/plantilla.php";
            return;

        }

        $indicador = New Indicador;
        $indicador->consultar(null, 1);

        $maquinaria = New Maquinaria;
        $maquinarias = $maquinaria->consultar();
        $cantidadMaquinarias = count($maquinarias);

        $servicio = New Servicio;
        $servicios = $servicio->consultar();
        $cantidadServicios = count($servicios);

        $requisicion = New Requisicion;
        $requisiciones = $requisicion->consultar();
        $cantidadRequisiciones = count($requisiciones);

        $requisicionGasto = New RequisicionGasto;
        $requisicionGastos = $requisicionGasto->consultar();
        $cantidadRequisicionGastos = count($requisicionGastos);

        $gasto = New Gastos;
        $gastos = $gasto->consultarCerrados();
        $cantidadGastos = count($gastos);

        $tarea = new Tarea;
        $tareas = $tarea->consultarPendientes($usuario->id);
        $arrayTareas = array();
        foreach ($tareas as $key => $value) {
            $rutaEdit = Route::names('tareas.edit', $value['id']);
            array_push($arrayTareas,[
                "descripcion" => $value["descripcion"],
                "fecha_limite" => $value["fecha_limite"],
                "ruta" => $rutaEdit,
            ]);
        }

        $query = "SELECT    AD.fecha, SUM(AD.horas) AS 'horas'
                FROM        actividad_detalles AD
                INNER JOIN  actividades A ON AD.actividadId = A.id
                INNER JOIN  servicios S ON AD.servicioId = S.id
                GROUP BY    AD.fecha
                ORDER BY    AD.fecha";

        $horasTrabajadas = Conexion::queryAll(CONST_BD_APP, $query, $error);

        $query = "SELECT    SUM(AD.horas) AS 'actividad_detalles.horas'
                FROM        actividad_detalles AD
                INNER JOIN  actividades A ON AD.actividadId = A.id
                INNER JOIN  servicios S ON AD.servicioId = S.id
                GROUP BY    AD.fecha
                ORDER BY    AD.fecha";

        $horasTrabajadasCentro = Conexion::queryAll(CONST_BD_APP, $query, $error);

        $diasSinCargaTitulo = $indicador->diasSinCargaTitulo;
        $diasSinCargaNumero = $indicador->diasSinCargaNumero - 1;
        $diasSinCargaMaquinariaEstatus = implode( ",", $indicador->diasSinCargaMaquinariaEstatus );
        $diasSinCargaMaquinariaTipos = implode( ",", $indicador->diasSinCargaMaquinariaTipos );

        $query = "SELECT    EM.razonSocial AS 'empresas.razonSocial', EM.nombreCorto AS 'empresas.nombreCorto',
                            MT.descripcion AS 'maquinariaTipos.descripcion', MT.nombreCorto AS 'maquinariaTipos.nombreCorto',
                            ES.descripcion AS 'estatus.descripcion', ES.nombreCorto AS 'estatus.nombreCorto',
                            U.descripcion AS 'ubicaciones.descripcion', U.nombreCorto AS 'ubicaciones.nombreCorto',
                            M.numeroEconomico
                FROM        maquinarias M
                INNER JOIN  empresas EM ON M.empresaId = EM.id
                INNER JOIN  maquinaria_tipos MT ON M.maquinariaTipoId = MT.id
                INNER JOIN  estatus ES ON M.estatusId = ES.id
                INNER JOIN  ubicaciones U ON M.ubicacionId = U.id
                WHERE       MT.id IN ( {$diasSinCargaMaquinariaTipos} )
                AND         ES.id IN ( {$diasSinCargaMaquinariaEstatus} )
                AND         (SELECT     COUNT(C.fecha)
                            FROM        combustible_detalles CD
                            INNER JOIN  combustibles C ON CD.combustibleId = C.id
                            WHERE       CD.maquinariaId = M.id
                            AND         C.fecha BETWEEN DATE_SUB(DATE_FORMAT(NOW(), '%Y-%m-%d'), INTERVAL {$diasSinCargaNumero} DAY) AND date_format(NOW(), '%Y-%m-%d') ) = 0
                ORDER BY    EM.nombreCorto, M.numeroEconomico";

        $maquinariasSinCargas = Conexion::queryAll(CONST_BD_APP, $query, $error);

        $configuracionProgramacion = New ConfiguracionProgramacion;
        $configuracionProgramacion->consultar(null , 1);

        $servicioProximoTitulo = "Equipos con menos de {$configuracionProgramacion->unidadesAbrirServicio} Hrs/Kms para el siguiente servicio";

        $arrayProgramacion = array();
        if ( count($configuracionProgramacion->servicioTipos) > 0 ) {

            $servicioTiposText = '';
            foreach ($configuracionProgramacion->servicioTipos as $key => $value) {
                if ( $key > 0 ) $servicioTiposText .= ', ';
                $servicioTiposText .= $value;
            }

            $query = "SELECT    P.horoOdometroUltimo, P.cantidadSiguienteServicio,
                        M.id AS 'maquinarias.id', M.numeroEconomico AS 'maquinarias.numeroEconomico',
                        E.nombreCorto AS 'empresas.nombreCorto',
                        U.descripcion AS 'ubicaciones.descripcion', U.nombreCorto AS 'ubicaciones.nombreCorto',
                        ES.descripcion AS 'estatus.descripcion',
                        ST.id AS 'servicio_tipos.id', ST.descripcion AS 'servicio_tipos.descripcion', ST.numero AS 'servicio_tipos.numero',
                        ( SELECT        CD.horoOdometro
                        FROM            combustible_detalles CD
                        INNER JOIN  combustibles C ON CD.combustibleId = C.id
                        WHERE           CD.maquinariaId = M.id
                        ORDER BY        CONCAT(C.fecha, ' ', C.hora) DESC, CD.id DESC
                        LIMIT           1 ) AS horoOdometroActual
                FROM        programaciones P
                INNER JOIN  maquinarias M ON P.maquinariaId = M.id
                INNER JOIN  empresas E ON M.empresaId = E.id
                INNER JOIN  ubicaciones U ON M.ubicacionId = U.id
                INNER JOIN  estatus ES ON M.estatusId = ES.id
                INNER JOIN  servicio_tipos ST ON P.servicioTipoId = ST.id
                WHERE       ST.id IN ( {$servicioTiposText} )
                ORDER BY    E.nombreCorto, M.numeroEconomico";

            $programacion = Conexion::queryAll(CONST_BD_APP, $query, $error);

            foreach ($programacion as $key => $value) {

                $ultimo = $value['horoOdometroUltimo'];
                $proximo = $value['cantidadSiguienteServicio'] + $ultimo;
                $actual = $value['horoOdometroActual'];
                $pendiente = ( $ultimo > $actual ) ? round($proximo - $ultimo, 1) : round($proximo - $actual, 1);

                if ( $pendiente > $configuracionProgramacion->unidadesAbrirServicio ) continue;

                $registro = [
                    "equipo" => mb_strtoupper(fString($value['maquinarias.numeroEconomico'])),
                    "empresa" => mb_strtoupper(fString($value['empresas.nombreCorto'])),
                    "ubicacion" => mb_strtoupper(fString($value['ubicaciones.descripcion'])),
                    "estado" => mb_strtoupper(fString($value['estatus.descripcion'])),
                    "servicio" => mb_strtoupper(fString($value['servicio_tipos.descripcion'])),
                    "pendiente" => $pendiente
                ];

                array_push($arrayProgramacion, $registro);

            }

        }

        unset($indicador);
        unset($maquinaria,$maquinarias);
        unset($servicio, $servicios);
        unset($requisicion, $requisiciones);
        unset($gasto, $gastos);
        unset($configuracionProgramacion, $programacion, $registro);
        unset($query, $error);

        // Requerir el modulo a incluir en la plantilla
        $contenido = array('modulo' => 'vistas/modulos/dashboard.php');
        include "vistas/modulos/plantilla.php";
    }
}
