<?php

namespace App\Ajax;

session_start();

require_once "../globales.php";
require_once "../funciones.php";
require_once "../rutas.php";
require_once "../conexion.php";
require_once "../Requests/SaveInventariosRequest.php";
require_once "../Requests/SaveInventarioSalidasRequest.php";
require_once "../Requests/SaveResguardoRequest.php";
require_once "../Models/Requisicion.php";
require_once "../Models/OrdenCompra.php";
require_once "../Models/Usuario.php";
require_once "../Models/Resguardo.php";
require_once "../Models/Inventario.php";
require_once "../Models/InventarioSalida.php";
require_once "../Models/InventarioPartida.php";
require_once "../Controllers/Autorizacion.php";

use App\Route;
use App\Models\Usuario;
use App\Models\Requisicion;
use App\Models\OrdenCompra;
use App\Models\Inventario;
use App\Models\InventarioSalida;
use App\Models\InventarioPartida;
use App\Models\Resguardo;
use App\Requests\SaveInventariosRequest;
use App\Requests\SaveInventarioSalidasRequest;
use App\Requests\SaveResguardoRequest;
use App\Controllers\Validacion;
use App\Controllers\Autorizacion;

class InventarioAjax
{

	/*=============================================
	TABLA DE INVENTARIOS
	=============================================*/
	public function mostrarTabla()
	{
		try {

			$usuario = New Usuario;
			$usuario->consultar(null, usuarioAutenticado()["id"]);

			$inventario = New Inventario;
			$inventarioPartida = new InventarioPartida;
			$inventarioSalida = new InventarioSalida;

			$permiso = 'WHERE I.usuarioRecibioId = '.usuarioAutenticado()["id"];
			if ( Autorizacion::perfil($usuario, CONST_ADMIN) || Autorizacion::perfil($usuario, 'supervisor-almacen') ) {
				$permiso = '';
			}

			// var_dump(Autorizacion::perfil($usuario, 'supervisor-almacen'));

			// INVENTARIOS
			$inventarios = $inventario->consultarInventarios($permiso);
			// PARTIDAS DEL INVENTARIO
			$partidasInventario = $inventarioPartida->consultarPartidas($permiso);
			//SALIDAS
			$inventarioSalidas = $inventarioSalida->consultar(null,null,$permiso);

			/*=============================================
    		MOSTRAR PARTIDAS DE LOS INVENTARIOS
    		=============================================*/

			$columnasPartidas = [
				["data" => "consecutivo"],
				["data" => "almacen"],
				["data" => "cantidad"],
				["data" => "unidad"],
				["data" => "descripcion"],
				["data" => "numeroParte"],
				["data" => "acciones"]
			];
			
			$registroPartidas = []; 

			foreach ($partidasInventario as $key => $value) {
				$rutaEdit = Route::names('inventarios.edit', $value['id']);
			
				$registroPartidas[] = [
					"consecutivo"  => ($key + 1),
					"almacen"      => mb_strtoupper(fString($value["nombreAlmacen"])),
					"cantidad" => $value["cantidad"] - ($value["cantidadSalidas"] ?? 0),
					"unidad"       => mb_strtoupper(fString($value["unidad"])),
					"descripcion"  => mb_strtoupper(fString($value["concepto"])),
					"numeroParte"  => mb_strtoupper(fString($value["numeroParte"])),
					"acciones"     => "
										<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
				];
			}
			
			/*=============================================
    		MOSTRAR INVENTARIOS
    		=============================================*/

			$columnasInventarios = [
				["data" => "consecutivo"],
				["data" => "folio"],
				["data" => "almacen"],
				["data" => "entrego"],
				["data" => "ordenCompra"],
				["data" => "recibio"],
				["data" => "acciones"]
			];
			
			$token = createToken();
			
			$registroInventarios = []; 

			foreach ($inventarios as $key => $value) {

				$rutaEdit = Route::names('inventarios.edit', $value['folio']);
				$rutaPrint = Route::names('inventarios.print', $value['folio']);
				$rutaDestroy = Route::names('inventarios.destroy', $value['folio']);

				$folio = mb_strtoupper(fString($value['folio']));

			
				$registroInventarios[] = [
					"consecutivo"  => ($key + 1),
					"folio"     => $value["folio"],
					"almacen"     => $value["nombreAlmacen"],
					"entrego"       => mb_strtoupper(fString($value["entrego"])),
					"ordenCompra"  => mb_strtoupper(fString($value["ordenCompra"])),
					"recibio"  => mb_strtoupper(fString($value["recibio"])),
					"acciones"     => "
									<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>

									<form method='POST' action='{$rutaDestroy}' style='display: inline'>
										<input type='hidden' name='_method' value='DELETE'>
										<input type='hidden' name='_token' value='{$token}'>
										<button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									        <i class='far fa-times-circle'></i>
									    </button>
								    </form>
									<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>"
				];
			}
			
			/*=============================================
    		MOSTRAR SALIDAS
    		=============================================*/

			$columnaSalidas = [
					["data" => "salidaId"],
				["data" => "entradaId"],
				["data" => "almacen"],
				["data" => "entrego"],
				["data" => "fechaSalida"],
				["data" => "recibio"],
				["data" => "status"],
				["data" => "acciones"]
			];

			$registroSalidas = []; 

			foreach ($inventarioSalidas as $key => $value) {

				$rutaEdit = Route::names('inventarios.edit', $value['entradaId']);
				$rutaPrint = Route::names('inventario-salidas.print', $value['id']);

				$folio = mb_strtoupper(fString($value['id']));

			
				$registroSalidas[] = [
					"salidaId"     => $value["id"],
					"entradaId"     => $value["entradaId"],
					"almacen"     => $value["nombreAlmacen"],
					"recibio"  => mb_strtoupper(fString($value["nombreRecibio"])),
					"fechaSalida" =>  !empty($value["fechaSalida"]) ? mb_strtoupper(fString($value["fechaSalida"])) : "SIN FECHA DE SALIDA",
					"entrego" => !empty($value["nombreEntrego"]) ? mb_strtoupper(fString($value["nombreEntrego"])) : "SIN USUARIO ASIGNADO",	
					"recibio" => !empty($value["nombreRecibio"]) ? mb_strtoupper(fString($value["nombreRecibio"])) : "SIN USUARIO ASIGNADO",	
					"status"  => mb_strtoupper(fString($value["status"])),
					"acciones"     => "
									<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
									<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-success'><i class='fas fa-eye'></i></a>"
				];
			}


			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnasInventarios'] = $columnasInventarios;
			$respuesta['datos']['registroInventarios'] = $registroInventarios;
			$respuesta['datos']['columnasPartidas'] = $columnasPartidas;
			$respuesta['datos']['registroPartidas'] = $registroPartidas;
			$respuesta['datos']['registroSalidas'] = $registroSalidas;
			$respuesta['datos']['columnaSalidas'] = $columnaSalidas;

			

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta, JSON_UNESCAPED_UNICODE);
		exit; 
	}

	public function consultarFiltros()
	{
		$arrayFiltros = array();

        if ( $this->almacenId > 0 ) array_push($arrayFiltros, [ "campo" => "A.id", "operador" => "=", "valor" => $this->almacenId ]);    
        if ( $this->descripcion !== '' ) array_push($arrayFiltros, [ "campo" => "lower(IV.descripcion)", "operador" => "like", "valor" => "'%".$this->descripcion."%'" ]);

		$inventario = New Inventario;
        $inventarios = $inventario->consultarFiltros($arrayFiltros);

		$columnas = array();
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "folio" ]);
        array_push($columnas, [ "data" => "almacen" ]);
        array_push($columnas, [ "data" => "entrega" ]);
        array_push($columnas, [ "data" => "ordenCompra" ]);
        array_push($columnas, [ "data" => "creo" ]);
        array_push($columnas, [ "data" => "acciones" ]);
        
        $token = createToken();
        
        $registros = array();
        foreach ($inventarios as $key => $value) {
        	$rutaEdit = Route::names('inventarios.edit', $value['id']);
        	$rutaDestroy = Route::names('inventarios.destroy', $value['id']);
        	$rutaPrint = Route::names('inventarios.print', $value['id']);
        	$folio = mb_strtoupper(fString($value['ordenCompra']));

        	array_push( $registros, [ "consecutivo" => ($key + 1),
									  "folio" => mb_strtoupper(fString($value["folio"])),
									  "almacen" => mb_strtoupper(fString($value["almacen.descripcion"])),
        							  "entrega" => mb_strtoupper(fString($value["entrega"])),
        							  "ordenCompra" => mb_strtoupper(fString($value["ordenCompra"])),
        							  "creo" => mb_strtoupper(fString($value["nombreCompleto"])),
        							  "acciones" => "<a href='{$rutaEdit}' target='_blank' class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a>
			        							     <form method='POST' action='{$rutaDestroy}' style='display: inline'>
									                      <input type='hidden' name='_method' value='DELETE'>
									                      <input type='hidden' name='_token' value='{$token}'>
									                      <button type='button' class='btn btn-xs btn-danger eliminar' folio='{$folio}'>
									                         <i class='far fa-times-circle'></i>
									                      </button>
								                     </form>
													 <a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>" ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

	/*=============================================
	CONSULTAR DETALLES DE INVENTARIO
	=============================================*/
	public function consultarDetalles()
	{
		$usuario = New Usuario;
		$usuario->consultar(null, usuarioAutenticado()["id"]);
		$usuario->consultarPermisos();
		$inventarioPartida = New InventarioPartida;
		$inventarioPartida->id = $this->inventarioId;
        $inventarios = $inventarioPartida->consultarPartidaPorId();

		$inventarioSalidas = New InventarioSalida;

        $salidas = $inventarioSalidas->consultarInventarioPorId($this->inventarioId);

		$columnas = array();	
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "partidaId" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "cantidadDisponible" ]);
        array_push($columnas, [ "data" => "costo_unitario" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "concepto" ]);   
        array_push($columnas, [ "data" => "acciones" ]);   

        
        $registros = array();
        	foreach ($inventarios as $key => $value) {
        	array_push( $registros, [ "consecutivo" => ($key + 1),
										"partidaId" => mb_strtoupper($value["id"]),
										"cantidad" => mb_strtoupper($value["cantidad"]),
        							  "cantidadDisponible" => $value["cantidad"] - ($value["cantidadSalidas"] ?? 0),
									  "costo_unitario" =>"$ ".round($value["costo_unitario"],2),
        							  "unidad" => mb_strtoupper(fString($value["unidad"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "concepto" => mb_strtoupper($value["concepto"]),
									  "acciones" => "<button type='button' class='btn btn-xs btn-info btn-subirArchivo' id='{$value['id']}'><i class='fas fa-file-upload'></i></button> <button type='button' class='btn btn-xs btn-info verImagenes' data-toggle='modal' data-target='#modalVerImagenes' partida='{$value['id']}'><i class='fas fa-eye'></i></button>"
									  ] );
        	}

		$columnasSalidas = array();
		array_push($columnasSalidas, [ "data" => "consecutivo" ]);
		array_push($columnasSalidas, [ "data" => "folio" ]);
		array_push($columnasSalidas, [ "data" => "fechaSalida" ]);
		array_push($columnasSalidas, [ "data" => "entrego" ]);
		array_push($columnasSalidas, [ "data" => "status" ]);
		array_push($columnasSalidas, [ "data" => "acciones" ]);



		$registrosSalidas = array();
		foreach ($salidas as $key => $value) {
			$rutaEdit = Route::names('inventario-salidas.edit', $value['id']);
        	$rutaPrint = Route::names('inventario-salidas.print', $value['id']);
			$salidaId = $value["id"];

			if ( $value["status"] == "NO AUTORIZADO" ) {
				$statusValor = 'autorizar';
			}else{
				$statusValor = 'firmar';
			}

			$permisoAutorizar = '';
			if ( !$usuario->checkPermiso("inventarios-auth") && $statusValor == 'autorizar' ) {
				$permisoAutorizar = 'disabled';
			}


			$accionesStatus = $value["status"] == "SALIDA FIRMADA" ? 									 				 "
			<button salidaId='{$salidaId}'  type='button' class='btn btn-xs btn-warning resguardo' > 
				<i class='fas fa-angle-double-right'></i>
			</button>
			<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>"  
			: 
			"
			<button salidaId='{$salidaId}'  type='button' class='btn btn-xs btn-warning resguardo' > 
				<i class='fas fa-angle-double-right'></i>
			</button>
			<button salidaId='{$salidaId}'  type='button' class='btn btn-xs btn-success {$statusValor}'  {$permisoAutorizar}> 
				<i class='fas fa-check'></i>
			</button>
			<a href='{$rutaPrint}' target='_blank' class='btn btn-xs btn-info'><i class='fas fa-print'></i></a>
				" 
			;

        	array_push( $registrosSalidas, [ 
									"consecutivo" => ($key + 1),
									  "folio" => $salidaId	,
									  "fechaSalida" =>  !empty($value["fechaSalida"]) ? mb_strtoupper(fString($value["fechaSalida"])) : "SIN FECHA DE SALIDA",
									  "entrego" => !empty($value["nombreEntrego"]) ? mb_strtoupper(fString($value["nombreEntrego"])) : "SIN USUARIO ASIGNADO",	
									  "status" => mb_strtoupper(fString($value["status"])),	
									  "acciones" => $accionesStatus
									] ); 
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;
        $respuesta['salidas']['columnas'] = $columnasSalidas;
        $respuesta['salidas']['registros'] = $registrosSalidas;

		// $respuesta['data'] = $disponibles;

        echo json_encode($respuesta);
	}

	public $salidaId;

	/*=============================================
	CONSULTAR PARTIDAS DE LAS SALIDAS
	=============================================*/
	public function consultarSalidasPartidas()
	{
		$inventarioSalidas = New InventarioSalida;
        $salidas = $inventarioSalidas->consultarPartidaSalidaPorId($this->salidaId);
	
		$columnas = array();	
        array_push($columnas, [ "data" => "consecutivo" ]);
        array_push($columnas, [ "data" => "salidaId" ]);
        array_push($columnas, [ "data" => "cantidad" ]);
        array_push($columnas, [ "data" => "unidad" ]);
        array_push($columnas, [ "data" => "numeroParte" ]);
        array_push($columnas, [ "data" => "concepto" ]);   
        array_push($columnas, [ "data" => "partidaId" ]);   
        array_push($columnas, [ "data" => "acciones" ]);   

        $registros = array();
        	foreach ($salidas as $key => $value) {
        	array_push( $registros, [ "consecutivo" => ($key + 1),
										"salidaId" => mb_strtoupper($value["id"]),
										"cantidad" =>  $value["cantidad"] - ($value["cantidadSalidas"] ?? 0),
        							  "unidad" => mb_strtoupper(fString($value["unidad"])),
        							  "numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
        							  "concepto" => mb_strtoupper($value["concepto"]),
        							  "partidaId" => mb_strtoupper($value["partidaId"]),
									  ] );
        }

        $respuesta = array();
        $respuesta['codigo'] = 200;
        $respuesta['error'] = false;
        $respuesta['datos']['columnas'] = $columnas;
        $respuesta['datos']['registros'] = $registros;

        echo json_encode($respuesta);
	}

	/*=============================================
	CREAR RESGUARDO
	=============================================*/
	public function crearResguardo()
	{
		try {

			$request = SaveResguardoRequest::validated();
			
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

			$resguardo = new Resguardo;

			$firma = substr($request["firma"], strpos($request["firma"], ',') + 1);
			// Decodificar los datos base64
			$firma = base64_decode($firma);
			// Nombre del archivo
			$directorio ='../../vistas/img/almacenes/recibe/';
			$filename =  fRandomNameFile($directorio, '.png');
			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}
			// Guardar el archivo
			file_put_contents($filename, $firma);
			$request["firma"]= substr($filename,6);

			// Crear el nuevo registro
			if ( !$resguardo->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			foreach ($this->detalles as $detalle) {

				$detalle["salidaResguardoId"] = $resguardo->id;
				
				$respuesta = $resguardo->insertarDetalles($detalle);
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['ruta'] = Route::names('inventarios.index');
			$respuesta['respuestaMessage'] = 'Se creó con exito el resguardo';

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
	CONSULTAR PARTIDAS PARA CREAR INVENTARIO
	=============================================*/
	public function consultarPartidas()
	{

		try {

			$ordenCompra = New OrdenCompra;
			$ordenCompra->id = $this->requisicionId;
			$ordenCompra->consultarDetalles();

			$columnas = array();
			array_push($columnas, [ "data" => "id" ]);
			array_push($columnas, [ "data" => "consecutivo" ]);
			array_push($columnas, [ "data" => "cantidad" ]);
			array_push($columnas, [ "data" => "costo_unitario" ]);
			array_push($columnas, [ "data" => "unidad" ]);
			array_push($columnas, [ "data" => "numeroParte" ]);
			array_push($columnas, [ "data" => "concepto" ]);
			$registros = array();

			
			foreach ($ordenCompra->detalles as $key => $value) {

				array_push( $registros, [ "id" => $value["id"],
										"consecutivo" => ($key + 1),
										"cantidad_disponible" => $value["cantidad"] - $value["cantidadEntrada"],
										"cantidad" => $value["cantidad"],
										"unidad" => mb_strtoupper(fString($value["unidad"])),
										"numeroParte" => mb_strtoupper(fString($value["numeroParte"])),
										"concepto" => mb_strtoupper(fString($value["concepto"])),
										"costo_unitario" =>"$ ".round($value["importeUnitario"],2),
										] );
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['datos']['columnas'] = $columnas;
			$respuesta['datos']['registros'] = $registros;

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
	CREAR INVENTARIO
	=============================================*/

	public $detalles;

	public function guardar()
	{

		try {
			// Validar Autorizacion
            if ( !usuarioAutenticado() ) throw new \Exception("Usuario no Autenticado, intente de nuevo.");

				$usuario = New Usuario;
				$usuario->consultar("usuario", usuarioAutenticado()["usuario"]);

            if ( !Autorizacion::perfil($usuario, CONST_ADMIN) && !Autorizacion::permiso($usuario, "inventarios", "crear") ) throw new \Exception("No está autorizado a crear inventarios.");

			$request = SaveInventariosRequest::validated();

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

			$inventario = new Inventario;
			$inventarioPartida = new InventarioPartida;

			$firma = substr($request["firma"], strpos($request["firma"], ',') + 1);
			// Decodificar los datos base64
			$firma = base64_decode($firma);
			// Nombre del archivo
			$directorio ='../../vistas/img/almacenes/';
			do {
				$filename =  fRandomNameFile($directorio, '.png');;
			} while ( file_exists($filename) );

			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}
			// Guardar el archivo
			file_put_contents($filename, $firma);
			$request["firma"]= substr($filename,6);

			// Crear el nuevo registro
			$request["fechaEntrega"] = fFechaSQL($request["fechaEntrega"]);

            if ( !$inventario->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			$inventarioId = $inventario->id;

			foreach ($this->detalles as $detalle) {

				$detalle["partidaId"] = is_numeric($detalle["id"]) ? $detalle["id"] : null;
				$detalle["inventarioId"] = $inventarioId;
				$respuesta = $inventarioPartida->crear($detalle);
			}

			$requisicion = New Requisicion;
			if (isset($request["requisicionId"]) && $request["requisicionId"] > 0) {
				$requisicion->id = $request["requisicionId"];
				$requisicion->consultarDetalles();

				$requisicion->servicioEstatusId = 7;
				foreach ($requisicion->detalles as $detalle) {
					if ($detalle["cantidad"] - $detalle["cantidadEntrada"] > 0) {
						$requisicion->servicioEstatusId = 6;
						break;
					}
				}

				$requisicion->actualizarEstado();
			}
			
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['ruta'] = Route::names('inventarios.edit', $inventarioId);
			$respuesta['respuestaMessage'] = 'Se creó con exito la entrada';

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
	CREAR SALIDA
	=============================================*/

	public function crearSalida()
	{
		try {

			$request = SaveInventarioSalidasRequest::validated();

			
			$inventario = new InventarioSalida;

			// Crear el nuevo registro
			if ( !$inventario->crear($request) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");
			
			foreach ($this->detalles as $detalle) {
				$detalle["inventarioId"] = $inventario->id;
				
				$respuesta = $inventario->insertarDetalles($detalle);
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['respuestaMessage'] = 'Se creó con exito la salida';

		} catch (\Exception $e) {

            $respuesta = [
                'codigo' => 500,
                'error' => true,
                'errorMessage' => $e->getMessage()
            ];

        }

		echo json_encode($respuesta);
	}

	public function subirArchivo()
	{
		try {
			$inventarioPartida = New InventarioPartida;
			$inventarioPartida->insertarImagen($_POST["inventario_detalle"], $_FILES["archivos"]);

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Se subió con exito el archivo';

		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}

		echo json_encode($respuesta);
	}

	public function verImagenes()
	{
		try {
			$inventarioPartida = New InventarioPartida;
			$imagenes = $inventarioPartida->consultarImagenes($_POST["partida"]);

	
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['imagenes'] = $imagenes;

		} catch (\Exception $e) {

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];

		}

		echo json_encode($respuesta);
	}

	public function crearEntrada()
	{
		try {
			$existencias = $_POST["existencias"];
			$entradas = array();

			foreach ($existencias as $existencia) {
				if ( !isset($entradas[$existencia["almacenId"]]) ) {
					$entradas[$existencia["almacenId"]] = array();
				}
				array_push($entradas[$existencia["almacenId"]], $existencia);
			}

			$inventario = new Inventario;
			$inventarioDetalles = new InventarioDetalles;

			foreach ($entradas as $key => $detalles) {

				$datos = [
					"observaciones" => $_POST["observaciones"],
					"almacen" => $key,
					"entrega" => "",
					"firma" => "",
					"requisicionId" => $_POST["requisicionId"],
					"fechaCreacion" => date("Y-m-d H:i:s")
				];

				if ( !$inventario->crear($datos) ) throw new \Exception("Hubo un error al intentar grabar el registro, intente de nuevo.");

				foreach ($detalles as $key => $detalle) {
					$newValues = array();
					
					$detalle["indirecto"] = is_numeric($detalle["indirecto"]) ? $detalle["indirecto"] : null;
					$detalle["directo"] = is_numeric($detalle["directo"]) ? $detalle["directo"] : null;
					
					$inventarioDetalles->inventario = $inventario->id;
					
					$respuesta = $inventarioDetalles->crear($detalle);
				}
			}

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Se creó con exito la entrada';

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
	AUTORIZAR SALIDA
	=============================================*/

	public function autorizarSalida()
	{
		try {

			$inventarioSalida = New InventarioSalida;
			$inventarioSalida->id = $this->salidaId;

			$inventarioSalida->actualizarStatus();

			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Autorización con éxito';

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
	FIRMAR SALIDA
	=============================================*/

	public $usuarioRecibioId;
	public $firma;

	public function firmarSalida(){

		try{

			$inventarioSalida = New InventarioSalida;
			$inventarioSalida->id = $this->salidaId;

			$firma = substr($this->firma, strpos($this->firma, ',') + 1);
			// Decodificar los datos base64
			$firma = base64_decode($firma);
			// Nombre del archivo
			$directorio ='../../vistas/img/almacenes/recibe/';
			$filename =  fRandomNameFile($directorio, '.png');;
			if (!file_exists($directorio)) {
				mkdir($directorio, 0777, true);
			}
			// Guardar el archivo
			file_put_contents($filename, $firma);

			$request["firma"]= substr($filename,6);
			$request["usuarioRecibioId"] = $this->usuarioRecibioId;

			$inventarioSalida->firmarSalida($request);
		
			$respuesta = array();
			$respuesta['codigo'] = 200;
			$respuesta['error'] = false;
			$respuesta['mensaje'] = 'Autorización firmada con éxito';

		} catch(Exception $e){

			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => $e->getMessage()
			];
		}
		echo json_encode($respuesta);
	}
}

try {

    $inventarioAjax = New InventarioAjax;

    if ( isset($_POST["accion"]) ) {

		if ( $_POST["accion"] == "guardar" ) {

			$detalles = json_decode($_POST["detalles"], true);
			$inventarioAjax->detalles = $detalles;

            $inventarioAjax->guardar();

		} elseif ( $_POST["accion"] == "crearSalida" ) {

			$detalles = json_decode($_POST["detalles"], true);
			$inventarioAjax->detalles = $detalles;

			$inventarioAjax->crearSalida();

		} elseif ( $_POST["accion"] == "crearSalidaResguardo" ) {

			$detalles = json_decode($_POST["detalles"], true);
			$inventarioAjax->detalles = $detalles;

			$inventarioAjax->crearResguardo();

		} 
		elseif ( $_POST["accion"] == "subir-archivo" ) {
			$inventarioAjax->subirArchivo();
		}
		elseif ( $_POST["accion"] == "firmarSalida" ) {

			$inventarioAjax->salidaId = $_POST["salidaId"];
			$inventarioAjax->usuarioRecibioId = $_POST["usuarioRecibioId"];
			$inventarioAjax->firma = $_POST["firma"];
			$inventarioAjax->firmarSalida();
		}
		elseif ( $_POST["accion"] == "autorizarSalida" ) {

			$inventarioAjax->salidaId = $_POST["salidaId"];
			$inventarioAjax->autorizarSalida();
		}
		elseif ( $_POST["accion"] == "verImagenes" ) {
			$inventarioAjax->verImagenes();
		} elseif ( $_POST["accion"] == "crearEntrada" ) {
			$inventarioAjax->crearEntrada();
		} else {
			$respuesta = [
				'codigo' => 500,
				'error' => true,
				'errorMessage' => 'Acción no encontrada'
			];
		}
    
    } elseif (isset($_GET["descripcion"])) {
        /*=============================================
        CONSULTAR FILTROS
        =============================================*/
        $inventarioAjax->almacenId = $_GET["almacenId"];
        $inventarioAjax->descripcion = $_GET["descripcion"];
        $inventarioAjax->consultarFiltros();
    } elseif (isset($_GET["disponibles"])) {
		/*=============================================
		TABLA DE INVENTARIOS DISPONIBLES
		=============================================*/
        $inventarioAjax->consultarDisponibles();
	} elseif ( isset($_GET["requisicionId"]) ) {
		/*=============================================
		TABLA DE INVENTARIOS DISPONIBLES
		=============================================*/
		$inventarioAjax->requisicionId = $_GET["requisicionId"];
        $inventarioAjax->consultarPartidas();

	} elseif ( isset($_GET["inventarioId"]) ) {
		/*=============================================
		TABLA DETALLES DE INVENTARIO
		=============================================*/
		$inventarioAjax->inventarioId = $_GET["inventarioId"];
        $inventarioAjax->consultarDetalles();
	} 
	elseif ( isset($_GET["salidaId"]) ) {
		/*=============================================
		TABLA LAS PARTIDAS DE LAS SALIDAS
		=============================================*/
		$inventarioAjax->salidaId = $_GET["salidaId"];
        $inventarioAjax->consultarSalidasPartidas();
	}
	else {
        /*=============================================
		TABLA DE INVENTARIOS
		=============================================*/
        $inventarioAjax->mostrarTabla();
    }

} catch (\Error $e) {

    $respuesta = [
        'codigo' => 500,
        'error' => true,
        'errorMessage' => $e->getMessage()
    ];

    echo json_encode($respuesta);

}