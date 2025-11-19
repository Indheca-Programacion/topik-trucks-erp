<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";

require_once "../Models/Usuario.php";
require_once "../Models/Generadores.php";
require_once "../Models/GeneradorDetalles.php";
require_once "../Models/GeneradorObservaciones.php";
require_once "../Models/Estimaciones.php";
require_once "../Models/Desempeno.php";
require_once "../Models/Tarea.php";
require_once "../Models/Ubicacion.php";
require_once "../Controllers/Autorizacion.php";
require_once "../Requests/SaveGeneradorDetallesRequest.php";
require_once "../Requests/SaveGeneradorObservacionesRequest.php";


use App\Route;
use App\Models\Usuario;
use App\Controllers\Autorizacion;
use App\Models\Generadores;
use App\Models\GeneradorDetalles;
use App\Models\GeneradorObservaciones;
use App\Models\Estimaciones;
use App\Models\Desempeno;
use App\Models\Ubicacion;
use App\Models\Tarea;
use App\Requests\SaveGeneradorDetallesRequest;
use App\Requests\SaveGeneradorObservacionesRequest;


class GeneradorAjax
{
	/*=============================================
	TABLA DE REPORTES
	=============================================*/
	public function mostrarTabla()
	{
        $generador = new Generadores;
        $generadores = $generador->consultar();
        $columnas = array();

        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "obra" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "observaciones" ]);
        array_push($columnas, [ "data" => "user_crecion" ]);
        array_push($columnas, [ "data" => "mes" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $registros = array();

        $token = createToken();
        setlocale(LC_TIME, 'es_ES.UTF-8');
        foreach ($generadores as $key => $value) {
            $rutaEdit = Route::names('generadores.edit', $value['id']);
        	$rutaDestroy = Route::names('generadores.destroy', $value['id']);
        	$rutaPrint = Route::names('generadores.print', $value['id']);
            $folio = fString($value["id"]);
            list($year, $month, $day) = explode('-', $value["mes"]);
            $monthName = fNombreMes($month);
            array_push($registros,[
                "consecutivo" => ($key + 1),
                "folio" => "GEN-".$value["folio"],
                "obra" => mb_strtoupper(fString($value["obra"])),
                "ubicacion" => mb_strtoupper(fString($value["ubicacion"])),
                "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                "user_crecion" => mb_strtoupper(fString($value["nombreCompleto"])),
                "mes" => $monthName." ".$year,
                "fecha_creacion" => fFechaLarga($value["fechaCreacion"]),
                "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form><a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" 
            ]
            );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
    }

    public $generador;
    public function obtenerMaquinas()
    {
        require_once "../Models/Maquinaria.php";
        $maquinaria = New \App\Models\Maquinaria;
        $maquinarias = $maquinaria->consultarMaquinasGenerador($this->generador);
        
        $columnas = array();

        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "empresa" ]);
        array_push($columnas, [ "data" => "tipoMaquinaria" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "numeroFactura" ]);
        array_push($columnas, [ "data" => "descripcion" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "year" ]);
        array_push($columnas, [ "data" => "serie" ]);
        array_push($columnas, [ "data" => "color" ]);
        array_push($columnas, [ "data" => "ubicacion" ]);
        array_push($columnas, [ "data" => "almacen" ]);
        $registros = array();

        foreach ($maquinarias as $key => $value) {
            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "maquinariaId" => $value["id"],
                "empresa" => fString($value["empresas.nombreCorto"]),
                "tipoMaquinaria" => fString($value["maquinaria_tipos.descripcion"]),
                "numeroEconomico" => fString($value["numeroEconomico"]),
                "numeroFactura" => fString($value["numeroFactura"]),
                "descripcion" => fString($value["descripcion"]),
                "marca" => fString($value["marcas.descripcion"]),
                "modelo" => fString($value["modelos.descripcion"]),
                "year" => $value["year"],
                "serie" => fString($value["serie"]),
                "color" => fString($value["colores.descripcion"]),
                "ubicacion" => fString($value["ubicaciones.descripcion"]),
                "almacen" => fString($value["almacenes.descripcion"]) 
                ] );
        }
        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;
        echo json_encode($respuesta);
    }

    public $ubicacionId;
    public $obraId;
    
    public function agregarMaquina()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generadores", "actualizar") ) throw new \Exception("No está autorizado a agregar nuevos generadores.");

            $request = SaveGeneradorDetallesRequest::validated();

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

            $generadorDetalle = new GeneradorDetalles;
            $generadorDetalle->ubicacionId = $this->ubicacionId;
            $generadorDetalle->obraId = $this->obraId;
            $response = $generadorDetalle->crear($request);

            $desempeno = new Desempeno;
            $datosDesempeno = array(
                    "generador_detalle" => $generadorDetalle->id,
                    "hmr" => 0,
                    "rr" => 0,
                    "lcc" => 0,
                    "observaciones" => ""
                );
            $response = $desempeno->crear($datosDesempeno);

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "La maquinaria fue agregada correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    //***********************************************/
    //* FUNCION PARA OBTENER RESUMEN DE GENERADOR 
    //* DIAS TRABAJADOS Y OBSERVACIONES
    //**********************************************/

    public function mostrarDetallesGenerador()
    {

        $generadorDetalle = New \App\Models\GeneradorDetalles;
        $generadorDetalles = $generadorDetalle->consultarDetalles($this->generador); 

        require_once "../Models/GeneradorObservaciones.php";
        $generadorObservaciones = New \App\Models\GeneradorObservaciones;
        $observaciones = $generadorObservaciones->consultarObservaciones($this->generador);

        $token = token();

        $columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "numeroEconomico" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "modelo" ]);
        array_push($columnas, [ "data" => "marca" ]);
        array_push($columnas, [ "data" => "serie" ]);
        array_push($columnas, [ "data" => "fecha" ]);

        $registros = array();
        $registrosResumen = array();

        require_once "../Models/Usuario.php";
        $usuario = new \App\Models\Usuario;
        $usuario->consultar(null, usuarioAutenticado()["id"]);
        $usuario->consultarPerfiles();
        $usuario->consultarPermisos();

        foreach ($generadorDetalles as $key => $value) {
            $rutaEdit = Route::names('generador-detalles.edit', $value['generadorId']);
        	$rutaDestroy = Route::names('generador-detalles.destroy', $value['generadorId']);
            $folio = fString($value["numeroEconomico"]);
            $acciones = "";
            if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::permiso($usuario, "generador-detalles", "actualizar") ) {
                $acciones = "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
                                <form method='POST' action='{$rutaDestroy}' style='display: inline'>
                                    <input type='hidden' name='_method' value='DELETE'>
                                    <input type='hidden' name='generador' value='{$this->generador}'>
                                    <input type='hidden' name='_token' value='{$token}'>
                                    <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
                                        <i class='far fa-times-circle'></i>
                                    </button>
                                </form>";
            }
            
            array_push( $registros, [ 
                "consecutivo" => ($key + 1),
                "generadorId" => $value["generadorId"],
                "maquinariaId" => $value["id"],
                "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                "equipo" => mb_strtoupper(fString($value["equipo"])),
                "marca" => mb_strtoupper(fString($value["marca"])),
                "modelo" => mb_strtoupper(fString($value["modelo"])),
                "serie" => mb_strtoupper(fString($value["serie"])),
                "fecha" => fFechaLarga($value["fecha"]),
                "equipo" => mb_strtoupper(fString($value["equipo"])),
                "laborados" => json_decode($value["laborados"]), 
                "fallas" => json_decode($value["fallas"]), 
                "paros" => json_decode($value["paros"]), 
                "clima" => json_decode($value["clima"]),
                "diaParcial" => json_decode($value["diaParcial"]),
                "acciones" => $acciones 
            ] );

            $totalDias = 0;
            $diasEfectivos = count(json_decode($value["laborados"]));
            $fallas = count(json_decode($value["fallas"]));
            $paros = count(json_decode($value["paros"]));
            $clima = count(json_decode($value["clima"]));
            $diaParcial = count(json_decode($value["diaParcial"]));

            $totalDias = $diasEfectivos+$fallas+$paros+$clima+$diaParcial;
            $dm = 0; 
            if($totalDias >0 ){
                $dm = (($totalDias-$fallas)/$totalDias)*100;
                if ( floor($dm) != $dm) $dm = number_format($dm,2);
            } 

            array_push( $registrosResumen, [
                "maquinariaId" => $value["id"],
                "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                "totalDias" => $totalDias,
                "laborados" => $diasEfectivos,
                "fallas" => $fallas,
                "paros" => $paros,
                "clima" => $clima,
                "diaParcial" => $diaParcial,
                "dm" => $dm." %",
            ]);
        }

        $columnaResumen = array();
        array_push($columnaResumen, [ "data" => "numeroEconomico" ]);
        array_push($columnaResumen, [ "data" => "totalDias" ]);
        array_push($columnaResumen, [ "data" => "laborados" ]);
        array_push($columnaResumen, [ "data" => "fallas" ]);
        array_push($columnaResumen, [ "data" => "paros" ]);
        array_push($columnaResumen, [ "data" => "clima" ]);
        array_push($columnaResumen, [ "data" => "diaParcial" ]);
        array_push($columnaResumen, [ "data" => "dm" ]);
    
        $columnaObservaciones = array();
        array_push($columnaObservaciones, [ "data" => "numeroEconomico" ]);
        array_push($columnaObservaciones, [ "data" => "fecha_inicio" ]);
        array_push($columnaObservaciones, [ "data" => "fecha_fin" ]);
        array_push($columnaObservaciones, [ "data" => "observaciones" ]);
        array_push($columnaObservaciones, [ "data" => "acciones" ]);
        $registrosObservaciones = array();


        foreach ($observaciones as $key => $value) {
            $rutaEdit = Route::names('generador-observaciones.edit', $value['id']);
        	$rutaDestroy = Route::names('generador-observaciones.destroy', $value['id']);
            $folio = fString($value["numeroEconomico"]);
            array_push( $registrosObservaciones, [ 
                "numeroEconomico" => mb_strtoupper(fString($value["numeroEconomico"])),
                "fecha_inicio" => fFechaLarga($value["fecha_inicio"]),
                "fecha_fin" => fFechaLarga($value["fecha_fin"]),
                "observaciones" => mb_strtoupper(fString($value["observaciones"])),
                "acciones" => "<a href='{$rutaEdit}' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='generador' value='{$this->generador}'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>" 
            ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['maquinaria']['columnas'] = $columnas;
        $respuesta['maquinaria']['registros'] = $registros;
        $respuesta['resumen']['columnas'] = $columnaResumen;
        $respuesta['resumen']['registros'] = $registrosResumen;
        $respuesta['observaciones']['columnas'] = $columnaObservaciones;
        $respuesta['observaciones']['registros'] = $registrosObservaciones;

        echo json_encode($respuesta);
    }

    public function actualizarIncidencia()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generadores", "actualizar") ) throw new \Exception("No está autorizado a agregar nuevos generadores.");
            
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
            $array = json_decode($_POST["datos"],true);
            
            foreach ($array as $key => $value) {
                $datos = [
                    "laborados" => "[".implode(", ", $value["laborados"])."]",
                    "fallas" => "[".implode(", ", $value["fallas"])."]",
                    "paros" => "[".implode(", ", $value["paros"])."]",
                    "clima" => "[".implode(", ", $value["clima"])."]",
                    "diaParcial" => "[".implode(", ", $value["diaParcial"])."]",
                    "id" => $value["detalleId"],
                ];
                
                $generadorDetalle = New \App\Models\GeneradorDetalles;
                $response = $generadorDetalle->updateIncidencias($datos);

                if (isset($_POST["observaciones"])) {
                    $generadorObservacion = New \App\Models\GeneradorObservaciones;
                    $datos = [
                        "fecha_inicio" => $_POST["desde"],
                        "fecha_fin" => $_POST["hasta"],
                        "observaciones" => $_POST["observaciones"],
                        "generadorDetalle" => $value["detalleId"]
                    ];
                    $response = $generadorObservacion->crear($datos);
                }
            }

            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "Se han añadido incidencias correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }
    public function obtenerIncidencias()
    {
        $generadorDetalle = New \App\Models\GeneradorDetalles;
        $respuesta = $generadorDetalle->obtenerIncidencias($this->mes,$this->maquinariaId);

        $response = array(); 
        foreach ($respuesta as $key => $value) {
            array_push($response,[
                "laborados" => json_decode($value["laborados"]),
                "fallas" => json_decode($value["fallas"]),
                "paros" => json_decode($value["paros"]),
                "clima" => json_decode($value["clima"]),
            ]);
        }

        echo json_encode($response);
    }
    public function agregarObservaciones()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generador-observaciones", "crear") ) throw new \Exception("No está autorizado a agregar observaciones.");

            $request = SaveGeneradorObservacionesRequest::validated();

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

            $generadorObservaciones = new GeneradorObservaciones;
            $response = $generadorObservaciones->crear($request);

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $response,
                'respuestaMessage' => "La observacion fue agregada correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }
    public function actualizarEstimaciones()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generadores", "crear") ) throw new \Exception("No está autorizado a actualizar generadores.");

            $estimacion = new Estimaciones;
            array_shift($_POST);

            if ($estimacion->consultarExistente($_POST["generador_detalle_id"])) {
                $response = $estimacion->actualizar($_POST);
            }else{
                $response = $estimacion->crear($_POST);
            }

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => '',
                'respuestaMessage' => "La estimacion fue actualizada correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }
    public function actualizarDesempeno()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generadores", "crear") ) throw new \Exception("No está autorizado a actualizar generadores.");

            $desempeno = new Desempeno;
            array_shift($_POST);

            if ($desempeno->consultarExistente($_POST["generador_detalle"])) {
                $response = $desempeno->actualizar($_POST);
            }else{
                $response = $desempeno->crear($_POST);
            }

            // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => '',
                'respuestaMessage' => "El desempeño fue actualizada correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }
    public function firmar()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generadores-aut", "crear") ) throw new \Exception("No está autorizado a actualizar generadores.");

            $generador = new Generadores;
            $generador->id = $_POST["generadorId"];
            $response = $generador->firmar(usuarioAutenticado()["id"]);

            // OBTENER ID TAREA POR ID_GENERADOR            
            $tarea = new Tarea;
            $id_tarea =  $tarea->consultarIdTarea($generador->id);
            
            $datos = [
                'id_tarea' => $id_tarea
            ];
            // COMPLETAR TAREA
            $tareaCompletada = $tarea->terminarTarea($datos);

            // MODEL UBICACION
            // CONSULTAR MANNAGER POR UBICACION
            $ubicacion = new Ubicacion;
            $ubicacionManager = $ubicacion->consultarManagerUbicacion(usuarioAutenticado()["id"]);

            // CREAR TAREA PARA AUTORIZAR ESTIMACIONES
            $datosTarea = [
                // MANDAR AL MANNAGER TAREA
                'fk_usuario' =>  $ubicacionManager,
                'descripcion' => 'LLENAR ESTIMACIONES GENERADOR FOLIO '.$generador->folio,  
                'fecha_inicio' => date('Y-m-d H:i:s'),
                'fecha_limite' => date('Y-m-d', strtotime('+1 week')),
                'usuarioIdCreacion' => usuarioAutenticado()["id"],
                'categoria' => "AUTORIZACION GENERADOR"
            ];

            // CREAR TAREA
            $id_tarea = $tarea->crearTarea($datosTarea);

            // DATOS PARA RELACION TAREA_GENERADOR
            $datosTareaGenerador = [
                'id_tarea' =>  $id_tarea,
                'id_generador' => $generador->id
            ];

            // CREAR RELACION TAREA GENERADOR
            $respuesta = $tarea->crearTareaGenerador($datosTareaGenerador);


            // // Crear el nuevo registro
            if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            $respuesta = [
                'error' => false,
                'respuesta' => $generador->id,
                'respuestaMessage' => "El generador fue firmado correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    /* =============================================
    AUTORIZAR ESTIMACION
    ============================================= */
    public function autorizarEstimacion()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            // Se verifica que esten autorizados para firmar estimaciones
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "estimaciones-aut", "crear") ) throw new \Exception("No está autorizado a actualizar generadores.");

            $generador = new Generadores;
            $generador->consultar(null, $_POST["generadorId"]);
            $generador->id = $_POST["generadorId"];
            $response = $generador->autorizarEstimacion(usuarioAutenticado()["id"]);

            // COMPLETAR TAREA
            $tarea = new Tarea;
            $id_tarea =  $tarea->consultarIdTarea($generador->id);
            

            $datos = [
                'id_tarea' => $id_tarea
            ];
            // COMPLETAR TAREA
            $tareaCompletada = $tarea->terminarTarea($datos);

            $respuesta = [
                'error' => false,
                'respuesta' => '',
                'respuestaMessage' => "El generador fue firmado correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }
    /* =============================================
    AUTORIZAR ESTIMACION SUPERVISOR
    ============================================= */
    public function autorizarEstimacionSupervisor()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            // Se verifica que esten autorizados para firmar estimaciones
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "estimacion-rev-auth", "crear") ) throw new \Exception("No está autorizado a actualizar generadores.");

            $generador = new Generadores;
            $generador->consultar(null, $_POST["generadorId"]);
            $generador->id = $_POST["generadorId"];
            $response = $generador->autorizarEstimacionSupervisor(usuarioAutenticado()["id"]);

            $today = date("Y-m-d"); 

            require_once "../Models/GeneradorDetalles.php";
            $generadorDetalles = New \App\Models\GeneradorDetalles;
            $estimaciones = $generadorDetalles->consultarEstimaciones($generador->id);

            // COMPLETAR TAREA
            $tarea = new Tarea;
            $id_tarea =  $tarea->consultarIdTarea($generador->id, 'generador');
            

            $datos = [
                'id_tarea' => $id_tarea
            ];
            // COMPLETAR TAREA
            $tareaCompletada = $tarea->terminarTarea($datos);

            $importe = 0;
            $crearRequisicon = false;
            $partidas = [];
            foreach ($estimaciones as $key => $value) {
                if ( $value["empresaId"] == 1 || $value["empresaId"] == 3 ) {
                    $crearRequisicon = true;
                    $laborados = json_decode($value["laborados"]);
                    $paros = json_decode($value["paros"]);
                    $totalDias = count($laborados) +  count($paros);
                    //
                    $fechaIngresada = $generador->mes;
                    $partesFecha = explode('-', $fechaIngresada);
                    $año = $partesFecha[0];
                    $mes = $partesFecha[1];
                    $totalDiasMes = cal_days_in_month(CAL_GREGORIAN, $mes, $año);
                    $division = $totalDiasMes != 0 ? count($laborados) / $totalDiasMes : 0;
                    $pu = (floatval($value["costo"])/30) * $totalDias;
        
                    $importe = $pu+$value["operacion"]+$value["comb"]+$value["mantto"]+$value["flete"]+$value["ajuste"] ;
                    $partidas["partida"][] = 0;
                    $partidas["cantidad"][] = 1;
                    $partidas["costo"][] = $importe;
                    $partidas["unidad"][] = "RENTA";
                    $partidas["concepto"][] = "Renta de ".$value["equipo"]." ".$value["modelo"]." ".$value["numeroEconomico"];
                }

            }

            if ( $crearRequisicon ) {
                //Crea formatos
                $rutaGenerador = $this->crearFormatoGenerador();
                $rutaEstimacion = $this->crearFormatoEstimacion();
                $archivos = [
                    $rutaGenerador,
                    $rutaEstimacion
                ];
                
                require_once "../Models/Obra.php";
                $obra = New \App\Models\Obra;
                $obra->consultar(null, $generador->obraId);

                require_once "../Models/Requisicion.php";
                $requisicion = New \App\Models\Requisicion;

                $obraCC = $obra->consultarCC();
                $folioCC = $requisicion->consultarUlimoFolioCC($obra->id);
                $usuarioCreo = $usuario->consultarCC();
                $obraDetalleRenta = $obra->consultarDetalleRenta($obraCC["id"]??1);
                $partidasCC = [];
                foreach ($partidas["partida"] as $key => $value) {
                    $partidasCC[] = [
                        "obraDetalleId" => $obraDetalleRenta["id"],
                        "costo" => $partidas["costo"][$key],
                        "cantidad" => 1,
                        "periodo" => date('W'),
                        "concepto" => $partidas["concepto"][$key],
                        "unidadId" => 20,
                        "costo_unitario" => $partidas["costo"][$key]
                    ];
                }

                // Crea datos para la requisicion de costos
                $datosRequisicionCC = [
                    "fk_idObra" => $obraCC["id"]??1,
                    "proveedorId" => 377, // Id de tibernal
                    "periodo" => date('W'),
                    "folio" => ($folioCC["folio"]??0)+1,
                    "divisa" => 1,
                    "tipoRequisicion" => 0, // Requisicion de costos
                    "fechaRequerida" => $today,
                    "direccion" => "",
                    "especificaciones" => "",
                    "usuarioIdCreacion" => 129,
                    "usuarioIdAutorizacion" => 59,
                    "usuarioIdAlmacen" => 19,
                    "estatusId" => 8,
                    "categoriaId" => 10, // Categoria de maquinarias
                    "presupuesto" => 0,
                    "justificacion" => "",
                    "detalles" => $partidasCC
                ];

                if (!$requisicion->crearRQCostos($datosRequisicionCC,$archivos)) throw new \Exception("Hubo un error al intentar crear la requisicion en Indheca, intente de nuevo.");
    
                // Crear el nuevo registro
                if ( !$response ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

            }

            $respuesta = [
                'error' => false,
                'respuesta' => '',
                'respuestaMessage' => "El generador fue firmado correctamente."
            ];
        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    /*=============================================
    AUTORIZACION SUPERVISOR
    =============================================*/
    public function autorizarSupervisor()
    {
        try {
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

            $usuario = New Usuario;
            $usuario->consultar("usuario", usuarioAutenticado()["usuario"]);
            // Se verifica que esten autorizados para firmar estimaciones
            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "generador-sup-auth", "crear") ) throw new \Exception("No está autorizado a actualizar generadores.");

            $generador = new Generadores;
            $generador->consultar(null, $_POST["generadorId"]);
            $generador->id = $_POST["generadorId"];
            $response = $generador->autorizarSupervisor();


             // OBTENER ID TAREA POR ID_GENERADOR            
             $tarea = new Tarea;
             $id_tarea =  $tarea->consultarIdTarea($generador->id);
             
             $datos = [
                 'id_tarea' => $id_tarea
             ];
             // COMPLETAR TAREA
             $tareaCompletada = $tarea->terminarTarea($datos);
 
             // MODEL UBICACION
             // CONSULTAR MANNAGER POR UBICACION
             $ubicacion = new Ubicacion;
             $ubicacionManager = $ubicacion->consultarManagerUbicacion(usuarioAutenticado()["id"]);
 
             // CREAR TAREA PARA AUTORIZAR ESTIMACIONES
             $datosTarea = [
                 // MANDAR AL MANNAGER TAREA
                 'fk_usuario' =>  $ubicacionManager,
                 'descripcion' => 'LLENAR ESTIMACIONES GENERADOR FOLIO '.$generador->id,  
                 'fecha_inicio' => date('Y-m-d H:i:s'),
                 'fecha_limite' => date('Y-m-d', strtotime('+1 week')),
                 'usuarioIdCreacion' => usuarioAutenticado()["id"],
                 'categoria' => "AUTORIZACION GENERADOR"
             ];
 
             // CREAR TAREA
             $id_tarea = $tarea->crearTarea($datosTarea);
 
             // DATOS PARA RELACION TAREA_GENERADOR
             $datosTareaGenerador = [
                 'id_tarea' =>  $id_tarea,
                 'id_generador' => $generador->id
             ];
 
             // CREAR RELACION TAREA GENERADOR
             $respuesta = $tarea->crearTareaGenerador($datosTareaGenerador);

            $respuesta = [
                'error' => false,
                'respuesta' => '',
                'respuestaMessage' => "El generador fue firmado correctamente."
            ];

        } catch (\Exception $e) {
            $errorMessage = $e->getMessage();
            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $errorMessage
            ];
        }

        echo json_encode($respuesta);
    }

    function crearFormatoGenerador()
    {
        $generador = new Generadores;
        $generador->consultar(null, $_POST["generadorId"]);

        require_once "../Models/GeneradorObservaciones.php";
        $generadorObservaciones = New \App\Models\GeneradorObservaciones;

        require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null,$generador->usuarioIdCreacion);

        require_once "../Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obra->consultar(null, $generador->obraId);

        $usuarioNombre = $usuario->nombre;
        $elaboro = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $elaboro .= ' ' . $usuario->apellidoMaterno;
        $elaboroFirma = $usuario->firma;
        unset($usuario);

        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null,$generador->firmado);

        $usuarioNombre = $usuario->nombre;
        $superintendente = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $superintendente .= ' ' . $usuario->apellidoMaterno;
        $autorizoFirma = $usuario->firma;
        unset($usuario);

        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null,$generador->generadorSupervisorFirma);

        $usuarioNombre = $usuario->nombre;
        $superviso = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $superviso .= ' ' . $usuario->apellidoMaterno;
        $supervisoFirma = $usuario->firma;
        $superviso = mb_strtoupper($superviso);
        unset($usuario);

        require_once "../Models/GeneradorDetalles.php";
        $generadorDetalle = New \App\Models\GeneradorDetalles;
        $maquinarias = $generadorDetalle->consultarDetalles($generador->id);

        $maquinariasPorEmpresa = [];
        foreach ($maquinarias as $maquinaria) {
            $empresaId = $maquinaria['empresaId'];
            if ( $empresaId == 1 || $empresaId == 3 ) { // Excluir Indheca y Indheca Renta
                if (!isset($maquinariasPorEmpresa[$empresaId])) {
                    $maquinariasPorEmpresa[$empresaId]["maquinarias"] = [];
                    $maquinariasPorEmpresa[$empresaId]["observaciones"] = [];
                }
                $maquinariasPorEmpresa[$empresaId]["maquinarias"][] = $maquinaria;
                require_once "../Models/Empresa.php";
                $empresa = new \App\Models\Empresa;
                $empresa->consultar(null, $empresaId);
                $maquinariasPorEmpresa[$empresaId]["empresa"] = $empresa->imagen;
                unset($empresa);
            }
            
        }

        $observaciones = $generadorObservaciones->consultarObservaciones($generador->id);

        foreach ($observaciones as $observacion) {
            $empresaId = $observacion['empresaId'];
            if ( $empresaId == 1 || $empresaId == 3 ) {
                if (!isset($maquinariasPorEmpresa[$empresaId])) {
                    $maquinariasPorEmpresa[$empresaId]["maquinarias"] = [];
                }
                if (!isset($maquinariasPorEmpresa[$empresaId]["observaciones"])) {
                    $maquinariasPorEmpresa[$empresaId]["observaciones"] = [];
                }
                $maquinariasPorEmpresa[$empresaId]["observaciones"][] = $observacion;
            }
        }

        $empresa = New \App\Models\Empresa;
        $empresa->consultar(null,$generador->empresaId);

        include "../../reportes/generador-adjunto.php";
        return $archivoGenerador;

    }

    function crearFormatoEstimacion()
    {
        $generador = new Generadores;
        $generador->consultar(null, $_POST["generadorId"]);
        require_once "../Models/GeneradorDetalles.php";
        $generadorDetalle = New \App\Models\GeneradorDetalles;
        
        require_once "../Models/Usuario.php";
        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $generador->usuarioIdCreacion);

        require_once "../Models/Obra.php";
        $obra = New \App\Models\Obra;
        $obra->consultar(null, $generador->obraId);

        $estimaciones = $generadorDetalle->consultarEstimaciones($_POST["generadorId"]);

        $datos = array();
        foreach ($estimaciones as $registro) {
            $empresaId = $registro['empresaId'];

            // Solo si la empresaId es 1 o 3
            if ($empresaId == 1 || $empresaId == 3) {
                // Si la empresaId aún no existe en el resultado, la inicializamos como un array vacío
                if (!isset($datos[$empresaId])) {
                    $datos[$empresaId] = [];
                    $empresa = New \App\Models\Empresa;
                    $empresa->consultar(null, $registro["empresaId"]);
                    $datos[$empresaId]["ruta"] = $empresa->imagen;
                    unset($empresa);
                }

                // Agregamos el registro al array correspondiente
                $datos[$empresaId]["registros"][] = $registro;
            }
        }

        $usuarioNombre = $usuario->nombre;
        $elaboro = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $elaboro .= ' ' . $usuario->apellidoMaterno;
        $elaboroFirma = $usuario->firma;
        $elaboro = mb_strtoupper($elaboro);
        unset($usuario);

        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $generador->estimacionFirma);

        $usuarioNombre = $usuario->nombre;
        $autorizo = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $autorizo .= ' ' . $usuario->apellidoMaterno;
        $estimacionFirma = $usuario->firma;
        $autorizo = mb_strtoupper($autorizo);
        unset($usuario);

        $usuario = New \App\Models\Usuario;
        $usuario->consultar(null, $generador->estimacionSupervisorFirma);

        $usuarioNombre = $usuario->nombre;
        $superviso = $usuario->nombre . ' ' . $usuario->apellidoPaterno;
        if ( !is_null($usuario->apellidoMaterno) ) $superviso .= ' ' . $usuario->apellidoMaterno;
        $supervisoFirma = $usuario->firma;
        $superviso = mb_strtoupper($superviso);
        unset($usuario);

        include "../../reportes/estimacion-adjunto.php";
        return $archivoEstimacion;
    }
}

/*=============================================
TABLA DE REPORTES
=============================================*/
$generadorAjax = new GeneradorAjax;
if ( isset($_POST["accion"])) {
    if( $_POST["accion"] == "agregar" ){
        /*=============================================
        AGREGAR EQUIPO
        =============================================*/
        $generadorAjax->ubicacionId=$_POST["ubicacionId"];
        $generadorAjax->obraId=$_POST["obraId"];
        $generadorAjax->agregarMaquina();
    } else if ( $_POST["accion"] == "updateIncidencia" ) {
        /*=============================================
        ACTUALIZAR INCIDENCIA
        =============================================*/
        $generadorAjax->actualizarIncidencia();
    } else if ( $_POST["accion"] == "agregarObservacion" ) {
        /*=============================================
        AGREGAR OBSERVACION
        =============================================*/
        $generadorAjax->agregarObservaciones();
    } else if ( $_POST["accion"] == "actualizar" ) {
        /*=============================================
        AGREGAR OBSERVACION
        =============================================*/
        $generadorAjax->actualizarEstimaciones();
    } else if ( $_POST["accion"] == "actualizarDesempeno" ) {
        /*=============================================
        ACTUALIZAR DESEMPEÑO
        =============================================*/
        $generadorAjax->actualizarDesempeno();
    } else if ( $_POST["accion"] == "firmar" ) {
        /*=============================================
        ACTUALIZAR DESEMPEÑO
        =============================================*/
        $generadorAjax->firmar();
    } else if ( $_POST["accion"] == "autorizarEstimacion" ) {
        /*=============================================
        FIRMAR ESTIMACION
        =============================================*/
        $generadorAjax->autorizarEstimacion();
    } elseif ( $_POST["accion"] == "firmarSupervisorEstimacion" ) {
        /*=============================================
        ACTUALIZAR INCIDENCIA
        =============================================*/
        $generadorAjax->autorizarEstimacionSupervisor();
    } elseif ( $_POST["accion"] == "firmarSupervisor" ) {
        /*=============================================
        ACTUALIZAR INCIDENCIA
        =============================================*/
        $generadorAjax->autorizarSupervisor();
    } else {

        $respuesta = [
            'codigo' => 500,
            'error' => true,
            'errorMessage' => "Realizó una petición desconocida."
        ];

        echo json_encode($respuesta);

    }
} else if ( isset($_GET["generadorId"]) ) {

	/*=============================================
	OBTENER MAQUINAS
	=============================================*/	
	$generadorAjax->generador = $_GET["generadorId"];
	$generadorAjax->obtenerMaquinas();

} else if ( isset($_GET["generador"])){
    /*=============================================
	OBTENER LISTA
	=============================================*/	
    $generadorAjax->generador = $_GET["generador"];
	$generadorAjax->mostrarDetallesGenerador();
} else if ( isset($_GET["maquinariaId"])){
    /*=============================================
	OBTENER LISTA
	=============================================*/	
    $generadorAjax->maquinariaId = $_GET["maquinariaId"];
    $generadorAjax->mes = $_GET["mes"];
	$generadorAjax->obtenerIncidencias();
} else {

	/*=============================================
	TABLA DE GENERADORES
	=============================================*/
	$generadorAjax->mostrarTabla();

}