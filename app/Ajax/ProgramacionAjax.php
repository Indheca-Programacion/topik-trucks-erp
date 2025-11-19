<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Models/Usuario.php";
require_once "../Models/ConfiguracionProgramacion.php";
require_once "../Models/Maquinaria.php";
require_once "../Models/ServicioTipo.php";
require_once "../Models/Programacion.php";
require_once "../Requests/SaveProgramacionRequest.php";
require_once "../Controllers/Autorizacion.php";

use App\Conexion;
use App\Route;
use App\Models\Usuario;
use App\Models\ConfiguracionProgramacion;
use App\Models\Maquinaria;
use App\Models\ServicioTipo;
use App\Models\Programacion;
use App\Requests\SaveProgramacionRequest;
use App\Controllers\Autorizacion;
use App\Controllers\Validacion;

class ProgramacionAjax
{
    /*=============================================
    CONSULTAR FILTROS
    =============================================*/
    public $empresaId;
    public $obraId;

    public function consultarFiltros()
    {
        $configuracionProgramacion = New ConfiguracionProgramacion;
        $configuracionProgramacion->consultar(null , 1);

        if ( count($configuracionProgramacion->servicioTipos) == 0 ) {

            $respuesta = array();
            $respuesta['codigo'] = 500;
            $respuesta['error'] = true;
            $respuesta["respuestaMessage"] = "Debe seleccionar los Tipos de Servicio (Módulo Configuración - Pantalla Programación) que se mostrarán en el Visor.";

            echo json_encode($respuesta);
            return;

        }

        $servicioTipos = $configuracionProgramacion->servicioTipos;
        $servicioTiposText = '';
        foreach ($servicioTipos as $key => $value) {
            if ( $key > 0 ) $servicioTiposText .= ', ';
            $servicioTiposText .= $value;
        }

        $query = "SELECT    P.horoOdometroUltimo, P.cantidadSiguienteServicio,
                        M.id AS 'maquinarias.id', M.numeroEconomico AS 'maquinarias.numeroEconomico',
                        E.id AS 'empresas.id', E.nombreCorto AS 'empresas.nombreCorto',
                        U.id AS 'ubicaciones.id', U.descripcion AS 'ubicaciones.descripcion', U.nombreCorto AS 'ubicaciones.nombreCorto',
                        O.id AS 'obras.id', O.descripcion AS 'obras.descripcion', O.nombreCorto AS 'obras.nombreCorto',
                        ES.descripcion AS 'estatus.descripcion',
                        ST.id AS 'servicio_tipos.id', ST.descripcion AS 'servicio_tipos.descripcion', ST.numero AS 'servicio_tipos.numero',
                        ( SELECT        CD.horoOdometro
                        FROM            combustible_detalles CD
                        INNER JOIN  combustibles C ON CD.combustibleId = C.id
                        WHERE           CD.maquinariaId = M.id
                        ORDER BY        CONCAT(C.fecha, ' ', C.hora) DESC, CD.id DESC
                        LIMIT           1 ) AS horoOdometroActual,
                        ( SELECT    S.folio
                        FROM        servicios S
                        INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                        WHERE       S.servicioTipoId = ST.id
                        AND         S.maquinariaId = M.id
                        AND         ( SE.servicioAbierto OR SE.nombreCorto = 'fin-sol' )
                        AND         S.servicioEstatusId <> 4
                        ORDER BY    S.fechaSolicitud DESC
                        LIMIT       1 ) AS servicioAbierto
            FROM        programaciones P
            INNER JOIN  maquinarias M ON P.maquinariaId = M.id
            INNER JOIN  empresas E ON M.empresaId = E.id
            INNER JOIN  ubicaciones U ON M.ubicacionId = U.id
            left JOIN  obras O ON M.obraId = O.id
            INNER JOIN  estatus ES ON M.estatusId = ES.id
            INNER JOIN  servicio_tipos ST ON P.servicioTipoId = ST.id
            WHERE       ST.id IN ( {$servicioTiposText} )";

        if ( $this->empresaId > 0 ) $query .= " AND         E.id = {$this->empresaId}";
        if ( $this->obraId > 0 ) $query .= " AND         O.id = {$this->obraId}";

        $query .= " ORDER BY     E.nombreCorto, M.numeroEconomico";

        $programacion = Conexion::queryAll(CONST_BD_APP, $query, $error);

        $arrayProgramacionAsoc = array();
        $arrayEquiposAsoc = array();
        foreach ($programacion as $key => $value) {
            $arrayProgramacionAsoc[$value['maquinarias.id']][$value['servicio_tipos.id']] = $value;

            if ( !isset($arrayEquiposAsoc[$value['maquinarias.numeroEconomico']]) ) $arrayEquiposAsoc[$value['maquinarias.numeroEconomico']] = $value;
        }

        $arrayEquipos = array_unique(array_column($programacion, 'maquinarias.numeroEconomico'));

        $arrayServicioTipos = array();
        foreach ($servicioTipos as $key => $value) {
            $servicioTipo = New ServicioTipo;
            $servicioTipo->consultar(null , $value);

            array_push($arrayServicioTipos, $servicioTipo);
        }

        $usuario = New Usuario;
        $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
        // Buscar permiso para Crear Servicios
        $permitirCrearServicios = false;
        if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "servicios", "crear") ) $permitirCrearServicios = true;

        $columnas = array();
        // array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "equipo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "estado" ]);
        $arrayEncabezado = array();
        foreach ($arrayServicioTipos as $key => $value) {
            array_push($arrayEncabezado, [ "data" => mb_strtoupper(fString($value->descripcion)) ]);
            array_push($columnas, [ "data" => "ultimo".($key+1) ]);
            array_push($columnas, [ "data" => "proximo".($key+1) ]);
            array_push($columnas, [ "data" => "actual".($key+1) ]);
            array_push($columnas, [ "data" => "pendiente".($key+1) ]);
        }

        $registros = array();
        foreach ($arrayEquipos as $key => $value) {
            $maquinariaId = $arrayEquiposAsoc[$value]['maquinarias.id'];
            $equipo = $arrayEquiposAsoc[$value]['maquinarias.numeroEconomico'];
            $empresaId = $arrayEquiposAsoc[$value]['empresas.id'];
            $empresa = $arrayEquiposAsoc[$value]['empresas.nombreCorto'];
            $ubicacionId = $arrayEquiposAsoc[$value]['ubicaciones.id'];
            $ubicacion = $arrayEquiposAsoc[$value]['ubicaciones.descripcion'];
            $obraId = $arrayEquiposAsoc[$value]['obras.id'];
            $estado = $arrayEquiposAsoc[$value]['estatus.descripcion'];

            $registro = [
                // "consecutivo" => ($key + 1),
                "maquinariaId" => $maquinariaId,
                "equipo" => mb_strtoupper(fString($equipo)),
                "empresaId" => $empresaId,
                "empresa" => mb_strtoupper(fString($empresa)),
                "ubicacionId" => $ubicacionId,
                "ubicacion" => mb_strtoupper(fString($ubicacion)),
                "estado" => mb_strtoupper(fString($estado)),
                "obraId" => $obraId
            ];

            $warningActuales = array();
            $warningPendientes = array();

            $maquinariaId = $arrayEquiposAsoc[$value]['maquinarias.id'];
            foreach ($arrayServicioTipos as $key2 => $value2) {

                if ( isset($arrayProgramacionAsoc[$maquinariaId][$value2->id]) ) {
                    // $registro["record".($key2+1)] = $arrayProgramacionAsoc[$maquinariaId][$value2->id];

                    $ultimo = $arrayProgramacionAsoc[$maquinariaId][$value2->id]['horoOdometroUltimo'];
                    $proximo = $arrayProgramacionAsoc[$maquinariaId][$value2->id]['cantidadSiguienteServicio'] + $ultimo;
                    $actual = $arrayProgramacionAsoc[$maquinariaId][$value2->id]['horoOdometroActual'];
                    $pendiente = ( $ultimo > $actual ) ? round($proximo - $ultimo, 1) : round($proximo - $actual, 1);
                    $servicioAbierto = mb_strtoupper(fString($arrayProgramacionAsoc[$maquinariaId][$value2->id]['servicioAbierto']));

                    $registro["ultimo".($key2+1)] = $ultimo;
                    $registro["proximo".($key2+1)] = $proximo;
                    $registro["actual".($key2+1)] = $actual;
                    $registro["pendiente".($key2+1)] = $pendiente;
                    if ( $actual < $ultimo ) array_push($warningActuales, ($key2 + 1) * 4 + 2);
                    if ( $pendiente <= $configuracionProgramacion->unidadesAbrirServicio ) {
                        $servicioTipoDescripcion = mb_strtoupper(fString($value2->descripcion));

                        if ( $servicioAbierto ) 
                            $registro["pendiente".($key2+1)] =
                                "<i class='fas fa-info-circle fa-lg text-dark float-left' style='margin-top: 4px; margin-left: 2px; margin-right: 2px; cursors: pointer;' title='Servicio Abierto [ {$servicioAbierto} ]'></i>
                                <span>{$pendiente}</span>";
                        elseif ( $permitirCrearServicios )
                            $registro["pendiente".($key2+1)] =
                                "<i class='fas fa-plus fa-lg text-dark float-left crearServicio' style='margin-top: 4px; margin-left: 2px; margin-right: 2px; cursor: pointer;' servicioTipoId='{$value2->id}' servicioTipo='{$servicioTipoDescripcion}' title='Crear servicio [ {$servicioTipoDescripcion} ]' data-toggle='modal' data-target='#modalCrearServicio'></i>
                                <span>{$pendiente}</span>";
                        else $registro["pendiente".($key2+1)] = "<span>{$pendiente}</span>";

                        array_push($warningPendientes, ($key2 + 1) * 4 + 3);
                    } else if ( $servicioAbierto ) 
                        $registro["pendiente".($key2+1)] =
                                "<i class='fas fa-info-circle fa-lg text-dark float-left' style='margin-top: 4px; margin-left: 2px; margin-right: 2px; cursors: pointer;' title='Servicio Abierto [ {$servicioAbierto} ]'></i>
                                <span>{$pendiente}</span>";

                    $registro["servicio".($key2+1)] = mb_strtoupper(fString($value2->descripcion));
                } else {
                    $registro["ultimo".($key2+1)] = '';
                    $registro["proximo".($key2+1)] = '';
                    $registro["actual".($key2+1)] = '';
                    $registro["pendiente".($key2+1)] = '';
                    $registro["servicio".($key2+1)] = '';
                }

            }
            $registro["warningActuales"] = $warningActuales;
            $registro["warningPendientes"] = $warningPendientes;

            array_push($registros, $registro);
        }

        // Generar datos para actualizar los catálogos en #modalAgregarSeguimiento
        $maquinaria = New Maquinaria;
        $maquinarias = $maquinaria->consultar();

        $registrosMaquinarias = array();
        foreach ($maquinarias as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "serie" => mb_strtoupper(fString($value["serie"]))
            ];

            array_push($registrosMaquinarias, $registro);
        }

        // Generar datos para actualizar los catálogos en #modalCrearServicio
        require_once "../Models/ServicioCentro.php";
        $servicioCentro = New \App\Models\ServicioCentro;
        $servicioCentros = $servicioCentro->consultar();

        $registrosServicioCentros = array();
        foreach ($servicioCentros as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"]))
            ];

            array_push($registrosServicioCentros, $registro);
        }

        require_once "../Models/MantenimientoTipo.php";
        $mantenimientoTipo = New \App\Models\MantenimientoTipo;
        $mantenimientoTipos = $mantenimientoTipo->consultar();

        $registrosMantenimientoTipos = array();
        foreach ($mantenimientoTipos as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"]))
            ];

            array_push($registrosMantenimientoTipos, $registro);
        }

        require_once "../Models/ServicioEstatus.php";
        $servicioEstatus = New \App\Models\ServicioEstatus;
        $servicioStatus = $servicioEstatus->consultar();

        $registrosServicioStatus = array();
        foreach ($servicioStatus as $key => $value) {
            if ( !$value['servicioAbierto'] ) continue;

            $registro = [
                "id" => $value["id"],
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"]))
            ];

            array_push($registrosServicioStatus, $registro);
        }

        require_once "../Models/SolicitudTipo.php";
        $solicitudTipo = New \App\Models\SolicitudTipo;
        $solicitudTipos = $solicitudTipo->consultar();

        $registrosSolicitudTipos = array();
        foreach ($solicitudTipos as $key => $value) {
            $registro = [
                "id" => $value["id"],
                "descripcion" => mb_strtoupper(fString($value["descripcion"])),
                "nombreCorto" => mb_strtoupper(fString($value["nombreCorto"]))
            ];

            array_push($registrosSolicitudTipos, $registro);
        }

        require_once "../Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresas = $empresa->consultar();

        require_once "../Models/Obra.php";
        $obra = new \App\Models\Obra;
        $obras = $obra->consultar();

        require_once "../Models/Ubicacion.php";
        $ubicacion = new \App\Models\Ubicacion;
        $ubicaciones = $ubicacion->consultar();

        $respuesta = [
            'codigo' => ( count($registros) > 0 ) ? 200 : 204,
            'error' => false,
            'cantidad' => count($registros),
            'datos' => [
                'columnas' => $columnas,
                'registros' => $registros,
                'encabezado' => $arrayEncabezado
            ],
            'catalogos' => [
                'maquinarias' => $registrosMaquinarias,
                // 'servicioTipos' => $registrosServicioTipos
                'servicioCentros' => $registrosServicioCentros,
                'mantenimientoTipos' => $registrosMantenimientoTipos,
                'servicioStatus' => $registrosServicioStatus,
                'solicitudTipos' => $registrosSolicitudTipos,
                'empresas' => $empresas,
                'obras' => $obras,
                'ubicaciones' => $ubicaciones

            ]
        ];

        echo json_encode($respuesta);
    }

    /*=============================================
    CONSULTAR MAQUINARIA
    =============================================*/
    public $maquinariaId;

    public function consultarMaquinaria()
    {
        $configuracionProgramacion = New ConfiguracionProgramacion;
        $configuracionProgramacion->consultar(null , 1);

        $programacion = New Programacion;
        $programacion->maquinariaId = $this->maquinariaId;
        $maquinariaProgramaciones = $programacion->consultarMaquinaria();

        $columnas = array();
        // array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "servicioTipo" ]);
        array_push($columnas, [ "data" => "ultimo" ]);
        array_push($columnas, [ "data" => "siguiente" ]);

        $registros = array();
        foreach ($configuracionProgramacion->servicioTipos as $key => $value) {
            $consecutivo = $key + 1;

            $servicioTipo = New ServicioTipo;
            $servicioTipo->consultar(null , $value);

            $keyArray = array_search($servicioTipo->id, array_column($maquinariaProgramaciones, 'servicioTipoId'));

            $servicioTipoDescripcion = mb_strtoupper(fString($servicioTipo->descripcion));
            $servicioChecked = ( $keyArray !== false) ? 'checked' : '';
            $ultimoServicio = ( $keyArray !== false ) ? $maquinariaProgramaciones[$keyArray]['horoOdometroUltimo'] : '';
            $ultimoServicioDisabled = ( $keyArray !== false ) ? '' : 'disabled';
            $siguienteServicio = ( $keyArray !== false ) ? $maquinariaProgramaciones[$keyArray]['cantidadSiguienteServicio'] : $servicioTipo->numero;
            $siguienteServicioDisabled = ( $keyArray !== false ) ? '' : 'disabled';

            $registro = [
                // "consecutivo" => ($key + 1),
                "servicioTipoId" => $servicioTipo->id,
                "servicioTipo" => "<input type='hidden' name='consecutivo[]' value='{$consecutivo}' {$ultimoServicioDisabled}>
                                <div class='input-group'>
                                    <input type='text' class='text-uppercase form-control form-control-sm' value='{$servicioTipoDescripcion}' readonly>
                                    <div class='input-group-append'>
                                        <div class='input-group-text'>
                                            <input type='checkbox' name='servicioTipoId[]' value='{$servicioTipo->id}' {$servicioChecked}>
                                        </div>
                                    </div>
                                </div>",
                "ultimo" => "<input type='text' class='text-right form-control form-control-sm campoConDecimal' decimales='1' name='horoOdometroUltimo[]' value='{$ultimoServicio}' {$ultimoServicioDisabled}>",
                "siguiente" => "<input type='text' class='text-right form-control form-control-sm campoSinDecimal' name='cantidadSiguienteServicio[]' value='{$siguienteServicio}' {$siguienteServicioDisabled}>"
            ];

            array_push($registros, $registro);
        }

        $respuesta = [
            'codigo' => ( count($registros) > 0 ) ? 200 : 204,
            'error' => false,
            'cantidad' => count($registros),
            'datos' => [
                'columnas' => $columnas,
                'registros' => $registros
            ]
        ];

        echo json_encode($respuesta);
    }

    /*=============================================
    AGREGAR SEGUIMIENTO
    =============================================*/
    public $token;
    public $consecutivo;
    public $servicioTipoId;
    public $horoOdometroUltimo;
    public $cantidadSiguienteServicio;

    public function agregarSeguimiento()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "programacion", "actualizar") ) throw new \Exception("No está autorizado para actualizar la Programación.");

            // Valida el detalle de la programación
            $arrayRequest = array();
            foreach ($this->consecutivo as $key => $value) {

                $_POST["maquinariaId"] = $this->maquinariaId;
                $_POST["servicioTipoId"] = $this->servicioTipoId[$key];
                $_POST["horoOdometroUltimo"] = $this->horoOdometroUltimo[$key];
                $_POST["cantidadSiguienteServicio"] = $this->cantidadSiguienteServicio[$key];

                $request = SaveProgramacionRequest::validated();

                if ( errors() ) {

                    $respuesta = [
                        'codigo' => 500,
                        'error' => true,
                        'errors' => errors(),
                        'consecutivo' => $value
                    ];

                    unset($_SESSION[CONST_SESSION_APP]["errors"]);

                    echo json_encode($respuesta);
                    return;

                }

                array_push($arrayRequest, $request);

            }

            $programacion = New Programacion;
            $programacion->maquinariaId = $this->maquinariaId;

            // Eliminar los registros (de la maquinaria)
            if ( !$programacion->eliminarMaquinaria() ) throw new \Exception("Hubo un error al intentar actualizar la programación, intente de nuevo.");

            // Actualizar la programación de la maquinaria
            foreach ($arrayRequest as $key => $request) {
                $programacion = New Programacion;

                // Crear el nuevo registro
                if ( !$programacion->crear($request) ) throw new \Exception("Hubo un error al intentar actualizar la programación, intente de nuevo.");
            }

            $respuesta = [
                'error' => false,
                // 'respuesta' => $programacion,
                'respuesta' => true,
                'respuestaMessage' => "La programación fue actualizada correctamente."
            ];

        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
    }

    /*=============================================
    CREAR SERVICIO
    =============================================*/
    public function crearServicio()
    {
        try {

            // Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "servicios", "crear") ) throw new \Exception("No está autorizado a crear nuevos Servicios.");

            require_once "../Requests/SaveServiciosRequest.php";
            require_once "../Models/Servicio.php";

            $request = \App\Requests\SaveServiciosRequest::validated();

            if ( errors() ) {

                $respuesta = [
                    'codigo' => 500,
                    'error' => true,
                    'errors' => errors()
                ];

                unset($_SESSION[CONST_SESSION_APP]["errors"]);

                echo json_encode($respuesta);
                return;

            }

            // Crear el nuevo servicio
            $servicio = New \App\Models\Servicio;
            if ( !$servicio->crear($request) ) throw new \Exception("Hubo un error al intentar crear el servicio, intente de nuevo.");

            if ( isset($_POST['kitMantenimiento']) && $_POST['kitMantenimiento'] > 0 ) 
            {
                // Crear Requisicion
                require_once "../Models/KitMantenimiento.php";
                $kitMantenimiento = New \App\Models\KitMantenimiento;
                $kitMantenimiento->consultar(null, $_POST['kitMantenimiento']);
                $kitMantenimiento->consultarDetalles();

                $requisicion_detalles = array();
                foreach ($kitMantenimiento->detalles as $detalle) {
                    // Cada $detalle es un array con los datos
                    $requisicion_detalles["cantidad"][] = $detalle['cantidad'];
                    $requisicion_detalles["unidad"][] = $detalle['unidad'];
                    $requisicion_detalles["numeroParte"][] = $detalle['numeroParte'];
                    $requisicion_detalles["concepto"][] = $detalle['concepto'];
                    $requisicion_detalles["costo"][] = 0;
                    $requisicion_detalles["partida"][] = 0;
                }

                $datos = [
                    "servicioId" => $servicio->id,
                    "fechaRequerida" => fFechaLarga(date('Y-m-d')),
                    "servicioEstatusId" => 9,
                    "tipoRequisicion" => 1,
                    "usuarioIdCreacion" => 80,
                    "detalles" => $requisicion_detalles
                ];

                if ( !is_null($kitMantenimiento->proveedorId)) {
                    $datos["proveedorId"] = $kitMantenimiento->proveedorId;
                }

                require_once "../Models/Requisicion.php";
                $requisicion = New \App\Models\Requisicion;
                if ( !$requisicion->crear($datos) ) throw new \Exception("Hubo un error al intentar crear la requisición del servicio, intente de nuevo.");
            }
            
            $respuesta = [
                'error' => false,
                // 'respuesta' => $servicio,
                'respuesta' => true,
                'respuestaMessage' => "El servicio fue creado correctamente."
            ];

        } catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

        echo json_encode($respuesta);
    }
    /*=============================================
    IMPRIMIR PROGRAMACION
    =============================================*/
    public function imprimirProgramacion()
    {
        $configuracionProgramacion = New ConfiguracionProgramacion;
        $configuracionProgramacion->consultar(null , 1);

        if ( count($configuracionProgramacion->servicioTipos) == 0 ) {

            $respuesta = array();
            $respuesta['codigo'] = 500;
            $respuesta['error'] = true;
            $respuesta["respuestaMessage"] = "Debe seleccionar los Tipos de Servicio (Módulo Configuración - Pantalla Programación) que se mostrarán en el Visor.";

            echo json_encode($respuesta);
            return;

        }

        $servicioTipos = $configuracionProgramacion->servicioTipos;
        $servicioTiposText = '';
        foreach ($servicioTipos as $key => $value) {
            if ( $key > 0 ) $servicioTiposText .= ', ';
            $servicioTiposText .= $value;
        }

        $maquinariasText = '';
        if ( isset($_POST["maquinarias"]) ) {
            $maquinariasSeleccionadas = json_decode($_POST["maquinarias"]);
            if ( count($maquinariasSeleccionadas) > 0 ) {
                foreach ($maquinariasSeleccionadas as $key => $value) {
                    if ( $key > 0 ) $maquinariasText .= ', ';
                    $maquinariasText .= $value;
                }
            }
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
            left JOIN  obras O ON M.obraId = O.id
            INNER JOIN  ubicaciones U ON M.ubicacionId = U.id
            INNER JOIN  estatus ES ON M.estatusId = ES.id
            INNER JOIN  servicio_tipos ST ON P.servicioTipoId = ST.id
            INNER JOIN unidades UN ON UN.id = ST.unidadId
            WHERE ST.id IN ( {$servicioTiposText} )";
        
        if ( $this->empresaId > 0 ) $query .= " AND         E.id = {$this->empresaId}";
        if ( $this->obraId > 0 ) $query .= " AND         O.id = {$this->obraId}";
        if (strlen(trim($maquinariasText)) > 0) $query .= " AND         M.id in ({$maquinariasText})";

        $query .= " ORDER BY     E.nombreCorto, M.numeroEconomico";

        $programaciones = Conexion::queryAll(CONST_BD_APP, $query, $error);

        $query = "SELECT ( SELECT    S.id
                    FROM        servicios S
                    INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                    WHERE       S.servicioTipoId = ST.id
                    AND         S.maquinariaId = M.id
                    AND         ( SE.servicioAbierto OR SE.nombreCorto = 'fin-sol' )
                    AND         S.servicioEstatusId <> 4
                    ORDER BY    S.fechaSolicitud DESC
                    LIMIT       1 ) AS servicioAbiertoId,
                    M.numeroEconomico,
                    ( SELECT    S.folio
                    FROM        servicios S
                    INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                    WHERE       S.servicioTipoId = ST.id
                    AND         S.maquinariaId = M.id
                    AND         ( SE.servicioAbierto OR SE.nombreCorto = 'fin-sol' )
                    AND         S.servicioEstatusId <> 4
                    ORDER BY    S.fechaSolicitud DESC
                    LIMIT       1 ) AS servicioAbierto,
                    IFNULL(O.descripcion, U.descripcion) AS ubicacion,
                    ( SELECT    IFNULL(SR.descripcion, 'S/REQ')
                    FROM        servicios S
                    INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                    LEFT JOIN  requisiciones R ON R.servicioId = S.id
                    LEFT JOIN   servicio_estatus SR ON SR.id = R.servicioEstatusId
                    WHERE       S.servicioTipoId = ST.id
                    AND         S.maquinariaId = M.id
                    AND         ( SE.servicioAbierto OR SE.nombreCorto = 'fin-sol' )
                    AND         S.servicioEstatusId <> 4
                    ORDER BY    S.fechaSolicitud DESC
                    LIMIT       1 ) AS estatusRequisicion,
                ST.descripcion AS tipoMantenimiento,				
                CASE
                        WHEN EXISTS (
                            -- Subconsulta: Busca si existe AL MENOS un registro en t2
                            -- que se relacione con el registro actual de t1.
                            SELECT 1
                            FROM servicio_imagenes SI
                            INNER JOIN servicios S ON S.id = SI.servicioId
                            INNER JOIN  servicio_estatus SE ON S.servicioEstatusId = SE.id
                            WHERE       S.servicioTipoId = ST.id
                                AND         S.maquinariaId = M.id
                                AND         ( SE.servicioAbierto OR SE.nombreCorto = 'fin-sol' )
                                AND         S.servicioEstatusId <> 4
                                ORDER BY    S.fechaSolicitud DESC
                                LIMIT       1
                        ) THEN 'completo'
                        ELSE 'falta'
                    END AS soporte
                
                FROM        programaciones P
                INNER JOIN  maquinarias M ON P.maquinariaId = M.id
                INNER JOIN  empresas E ON M.empresaId = E.id
                INNER JOIN  ubicaciones U ON M.ubicacionId = U.id
                left JOIN  obras O ON M.obraId = O.id
                INNER JOIN  estatus ES ON M.estatusId = ES.id
                INNER JOIN  servicio_tipos ST ON P.servicioTipoId = ST.id
                WHERE ST.id IN ( {$servicioTiposText} )";
        if ( $this->empresaId > 0 ) $query .= " AND         E.id = {$this->empresaId}";
        if ( $this->obraId > 0 ) $query .= " AND         O.id = {$this->obraId}";
        if (strlen(trim($maquinariasText)) > 0) $query .= " AND         M.id in ({$maquinariasText})";

        $query .= " ORDER BY     E.nombreCorto, M.numeroEconomico";
        
        $servicios = Conexion::queryAll(CONST_BD_APP, $query, $error);
        
        require_once "../Models/Obra.php";
        $ubicaciones = New \App\Models\Obra;
        $ubicaciones->consultar(null,$this->obraId);

        require_once "../Models/Empresa.php";
        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null, 2);

        include "../../reportes/programacion.php";
        include "../../reportes/programacionServicios.php";

        $respuesta = [
            'error' => false,
            'respuesta' => true,
            'respuestaMessage' => "Se creó porgramacion de mantenimiento."
        ];
        echo json_encode($respuesta);
    }
}

try {

    $programacionAjax = New ProgramacionAjax;

    if ( isset($_POST["accion"]) ) {

        $programacionAjax->token = $_POST["_token"];

        if ( $_POST["accion"] == "agregarSeguimiento" ) {

            /*=============================================
            AGREGAR SEGUIMIENTO
            =============================================*/
            $programacionAjax->maquinariaId = $_POST["maquinariaId"];
            $programacionAjax->consecutivo = ( isset($_POST["consecutivo"]) ) ? $_POST["consecutivo"] : [];
            $programacionAjax->servicioTipoId = ( isset($_POST["servicioTipoId"]) ) ? $_POST["servicioTipoId"] : [];
            $programacionAjax->horoOdometroUltimo = ( isset($_POST["horoOdometroUltimo"]) ) ? $_POST["horoOdometroUltimo"] : [];
            $programacionAjax->cantidadSiguienteServicio = ( isset($_POST["cantidadSiguienteServicio"]) ) ? $_POST["cantidadSiguienteServicio"] : [];
            $programacionAjax->agregarSeguimiento();

        } elseif ( $_POST["accion"] == "crearServicio" ) {

            /*=============================================
            CREAR SERVICIO
            =============================================*/
            $programacionAjax->crearServicio();

        } elseif ( $_POST["accion"] == "imprimir") {
            /*=============================================
            IMPRIMIR REPORTE DE PROGRAMACION
            =============================================*/
            $programacionAjax->empresaId = $_POST["empresaId"];
            $programacionAjax->obraId = $_POST["obraId"];
            $programacionAjax->imprimirProgramacion();
        } else {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => "Realizó una petición desconocida."
            ];

            echo json_encode($respuesta);

        }

    } elseif ( isset($_GET["empresaId"]) ) {

        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $programacionAjax->empresaId = $_GET["empresaId"];
        $programacionAjax->obraId = $_GET["obraId"];
        $programacionAjax->consultarFiltros();

    } elseif ( isset($_GET["maquinariaId"]) ) {

        /*=============================================
        CONSULTAR MAQUINARIA
        =============================================*/
        $programacionAjax->maquinariaId = $_GET["maquinariaId"];
        $programacionAjax->consultarMaquinaria();

    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}
