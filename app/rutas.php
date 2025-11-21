<?php

namespace App;

if ( file_exists ( "app/Requests/Request.php" ) ) {
    require_once "app/Requests/Request.php";
} else {
    require_once "../Requests/Request.php";
}

class Route{

	/**
	 * FUNCIÓN ENCARGADA DE RETORNAR LA VARIABLE GLOBAL DE LA RUTA
	 * 
	 * @return string  LA VARIABLE GLOBAL DE LA RUTA
	 */
	static public function ruta()
	{
		return CONST_RUTA;
	}

	/**
	 * FUNCIÓN ENCARGADA DE RETORNAR LA VARIABLE GLOBAL DEL SERVIDOR
	 * Y SI LA RUTA ES PROVEEDOR RETORNA LA RUTA DEL SERVIDOR DEL PROVEEDOR 
	 *
	 * @return string  LA VARIABLE GLOBAL DEL SERVIDOR
	 */
	static public function rutaServidor()
	{
		if (strpos($_SERVER['HTTP_HOST'], 'proveedor.') === 0) {
			return CONST_RUTA_SERVIDOR_PROVEEDOR;
		}else {
			return CONST_RUTA_SERVIDOR;
		}
	}

	/**
	 * FUNCIÓN ENCARGADA DE RETORNAL LA VARIABLE GLOBAL DE LA RUTA DEL SERVIDOR
	 * DEL PROVEEDOR
	 *
	 * @return string   RUTA DEL SERVIDOR PROVEEDOR
	 */
	static public function rutaServidorProveedor()
	{
		return CONST_RUTA_SERVIDOR_PROVEEDOR;
	}

	static public function names($valorRuta = null, $parametro = null) {

		$rutaArray = explode(".", $valorRuta);

		$controlador = $rutaArray[0];
		$metodo = $rutaArray[1];

		switch ($metodo) {

		    case "index":
		        return self::rutaServidor() . $controlador;
		        break;
		    case 'create':
		    	return self::rutaServidor() . $controlador. "/crear";
		        break;
		    case 'store':
		    	return self::rutaServidor() . $controlador;
		        break;
		    case 'edit':
		    	return self::rutaServidor() . $controlador ."/". $parametro ."/editar";
		        break;
			case 'update':
		    	return self::rutaServidor() . $controlador ."/". $parametro;
		        break;
		    case 'destroy':
		    	return self::rutaServidor() . $controlador . "/". $parametro;
		        break;
		    case 'changeStatus':
		    	return self::rutaServidor() . $controlador . "/". $parametro ."/estatus";
		        break;
		    case 'print':
		    	return self::rutaServidor() . $controlador . "/". $parametro ."/imprimir";
		        break;

		    default:
				return null;
		}

	}

	static public function routes($valorRuta = null, $parametro = null) {

		switch ($valorRuta) {
		    case "inicio":
		        return self::rutaServidor() . "inicio";
		        break;
			case "ingreso":
		        return self::rutaServidor() . "ingreso";
		        break;

			case "perfil":
		        return self::rutaServidor() . "perfil";
		        break;

		    case 'informacion-tecnica.download':
		    	return self::rutaServidor() . "informacion-tecnica/{$parametro}/download";
		        break;

		    case 'servicios.crear-requisicion':
		    	return self::rutaServidor() . "servicios/{$parametro}/crear-requisicion";
		        break;

		    case 'requisiciones.downloadComprobantes':
		    	return self::rutaServidor() . "requisiciones/{$parametro}/download/comprobantes";
		        break;
		    case 'requisiciones.downloadOrdenes':
		    	return self::rutaServidor() . "requisiciones/{$parametro}/download/ordenes";
		        break;
		    case "requisiciones.crear-orden-compra":
		    	return self::rutaServidor() . "requisiciones/{$parametro}/crear-orden-compra";
		        break;
			case "requisiciones.crear-cotizacion":
		    	return self::rutaServidor() . "requisiciones/{$parametro}/crear-cotizacion";
		        break;
			case "configuracion-requisiciones":
		        return self::rutaServidor() . "configuracion-requisiciones";
		        break;

			case "configuracion-ordenes-compra":
				return self::rutaServidor() . "configuracion-ordenes-compra";
				break;
	
			case "configuracion-puesto-tipo":
		        return self::rutaServidor() . "configuracion-puesto-tipo";
		        break;
			case "configuracion-programacion":
		        return self::rutaServidor() . "configuracion-programacion";
		        break;

			case "configuracion-correo-electronico":
		        return self::rutaServidor() . "configuracion-correo-electronico";
		        break;

		    case "reportes.index":
		        return self::rutaServidor() . "reportes";
		        break;
		    case 'reportes.create':
		    	return self::rutaServidor() . "reportes/crear/" . $parametro;
		        break;
		    case "resguardos.index":
		        return self::rutaServidor() . "resguardos";
		        break;
			case 'resguardos.create':
		    	return self::rutaServidor() . "resguardos/crear/" . $parametro;
		        break;
			case 'traslados.crear-requisicion':
		    	return self::rutaServidor() . "traslados/{$parametro}/crear-requisicion";
		        break;
			case 'inventarios.crear':
				return self::rutaServidor(). "inventarios/{$parametro}/crear";
			break;

			case 'maquinarias.crear-checklist':
				return self::rutaServidor(). "maquinarias/{$parametro}/crear-checklist";
			// case "login":
		 //        return self::$rutaServidor . "login";
		 //        break;

		 //    case "perfiles.index":
		 //        return self::$rutaServidor . "perfiles";
		 //        break;
		 //    case 'perfiles.create':
		 //    	return self::$rutaServidor . "perfiles/crear";
		 //        break;
		 //    case 'perfiles.store':
		 //    	// return self::$rutaServidor . "perfiles/guardar";
		 //    	return self::$rutaServidor . "perfiles";
		 //        break;
		 //    case 'perfiles.edit':
		 //    	return self::$rutaServidor . "perfiles/". $parametro ."/editar";
		 //        break;
			// case 'perfiles.update':
		 //    	return self::$rutaServidor . "perfiles/". $parametro;
		 //        break;
		 //    case 'perfiles.destroy':
		 //    	return self::$rutaServidor . "perfiles/". $parametro;
		 //        break;

		    // case "permisos.index":
		    //     return self::$rutaServidor . "permisos";
		    //     break;

		    case "salir":
		    	return self::rutaServidor() . "salir";
		        break;

			default:
				return null;
		}		

	}

	// Funcion que permite ejecutar los metodos del controlador
	static function execute($rutas, $controlador) {

		switch ( count($rutas) ) {
	
		    case 1:
	
		    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
	
		    		$controlador -> index();
	
		        } elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {
	
		        	$controlador -> store();
	
		        }
	
		        break;
	
		    case 2:
	
		    	$param1 = $rutas[1];
	
		    	if ( $param1 == "crear" ) {
	
			        $controlador -> create();
	
		    	} elseif ( Requests\Request::method() === "PUT" ) {
	
		    		$controlador -> update($param1);
	
		    	} elseif ( Requests\Request::method() === "DELETE" ) {
	
		    		$controlador -> destroy($param1);
	
		    	} else {
	
		    		$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            		include "vistas/modulos/plantilla.php";
	
		    	}
	
		        break;
	
		    case 3:
	
		    	$param1 = $rutas[1];
		    	$param2 = $rutas[2];
	
		    	if ( $param2 == "editar" ) {
	
			        $controlador -> edit($param1);
	
		    	} elseif ( $param2 == "estatus" && $_SERVER['REQUEST_METHOD'] === "POST" ) {

		    		$controlador -> changeStatus($param1);

		    	} elseif ( $param2 == "imprimir" ) {

		    		$controlador -> print($param1);

		    	} elseif ( $param2 == "download" ) {

		    		$controlador -> download($param1);

		    	} else {
	
		    		$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            		include "vistas/modulos/plantilla.php";
	
		    	}
	
		    	break;
	
			default:
	
				$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            	include "vistas/modulos/plantilla.php";
	
		}

	}

	static public function index() {

		$rutas = array();
		$ruta = null;

	    if ( isset($_GET["ruta"]) ) {

			$rutas = explode("/", $_GET["ruta"]);
			
			$rutasFinal = "";
			foreach ($rutas as $indice => $valor) {
			    
			    if ( $valor != "" ) {

			    	if ($indice != 0) {

			    		$rutasFinal .= "/";

			    	}

			    	$rutasFinal .= $valor;

				} 

			}

			$rutas = explode("/", $rutasFinal);

	    	$ruta = $rutas[0];

			if ($ruta == "inicio" ||
				$ruta == "maquinaria-tipos" ||
				$ruta == "marcas" ||
				$ruta == "modelos" ||
				$ruta == "colores" ||
				$ruta == "estatus" ||
				$ruta == "ubicaciones" ||
				$ruta == "almacenes" ||
				$ruta == "maquinarias" ||

				$ruta == "servicio-centros" ||
				$ruta == "unidades" ||
				$ruta == "mantenimiento-tipos" ||
				$ruta == "servicio-tipos" ||
				$ruta == "servicio-estatus" ||
				$ruta == "estatus-orden-compra" ||
				$ruta == "solicitud-tipos" ||
				$ruta == "servicios" ||
				$ruta == "orden-compra" ||
				$ruta == "requisiciones" ||
				$ruta == "requisicion-gastos" ||
				$ruta == "actividad-semanal" ||

				$ruta == "informacion-tecnica-tags" ||
				$ruta == "informacion-tecnica" ||

				$ruta == "combustible-cargas" ||
				$ruta == "combustible-rendimiento" ||
				$ruta == "programacion" ||

				$ruta == "resumen-costos" ||

				$ruta == "empleados" ||
				$ruta == "proveedores" ||

				/*------------------------------
				| RUTAS CATEGORIA PROVEEDOR
				------------------------------*/

				$ruta == "categoria-proveedores" ||
				$ruta == "categoria-permiso-proveedor" ||
				$ruta == "permiso-proveedor" ||
				$ruta == "solicitud-proveedor" ||

				$ruta == "configuracion-requisiciones" ||
				$ruta == "configuracion-ordenes-compra" ||

				$ruta == "configuracion-puesto-tipo" ||
				$ruta == "configuracion-programacion" ||
				$ruta == "configuracion-correo-electronico" ||

				$ruta == "empresas" ||
				$ruta == "sucursales" ||
		        $ruta == "usuarios" ||
		        $ruta == "perfiles" ||
		        $ruta == "permisos" ||
		        // $ruta == "reportes" ||

		        $ruta == "resguardos" ||
		        $ruta == "inventarios" ||
				$ruta == "inventario-salidas" ||

		        $ruta == "ingreso" ||
		        $ruta == "perfil" ||
		        $ruta == "estimaciones" ||
		        $ruta == "desempeno" ||
		        $ruta == "generadores" ||
		        $ruta == "generador-observaciones" ||
		        $ruta == "generador-detalles" ||
				$ruta == "tareas" ||
				$ruta == "tarea-observaciones" ||
				$ruta == "gastos" ||
				$ruta == "gasto-detalles" ||
				$ruta == "obras" ||
				$ruta == "alertas" ||
				$ruta == "escaner" ||
				$ruta == "politicas" ||

				$ruta == "traslados" ||
				$ruta == "requisicion-traslados" ||
				$ruta == "puestos" ||
				$ruta == "checklist-maquinarias" ||

				$ruta == "kit-mantenimiento" ||
				$ruta == "kits-maquinarias" ||

				$ruta == "pagos" ||
				$ruta == "inventarios-pendientes" ||

				$ruta == "comprobacion-gastos" ||
				$ruta == "presupuestos" ||

		    	$ruta == "salir") {

				switch ( $ruta ) {
					case "presupuestos":
						require_once "app/Controllers/PresupuestosController.php";
						self::execute($rutas, new \App\Controllers\PresupuestosController);
						break;
					case "comprobacion-gastos":
						require_once "app/Controllers/ComprobacionGastosController.php";
						self::execute($rutas, new \App\Controllers\ComprobacionGastosController);
						break;
					case "inventarios-pendientes":
						require_once "app/Controllers/InventariosPendientesController.php";
						self::execute($rutas, new \App\Controllers\InventariosPendientesController);
						break;
					case "pagos":
						require_once "app/Controllers/PagosController.php";
						self::execute($rutas, new \App\Controllers\PagosController);
						break;
					case "kits-maquinarias":
						require_once "app/Controllers/KitsMaquinariasController.php";
						self::execute($rutas, new \App\Controllers\KitsMaquinariasController);
						break;
					case "kit-mantenimiento":

						require_once "app/Controllers/KitMantenimientoController.php";

						self::execute($rutas, new \App\Controllers\KitMantenimientoController);

						break;
					case "checklist-maquinarias":

						require_once "app/Controllers/ChecklistMaquinariasController.php";

						self::execute($rutas, new \App\Controllers\ChecklistMaquinariasController);

						break;
					case "traslados" :

						if ( count($rutas) == 3 && $rutas[2] == 'crear-requisicion' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/RequisicionTrasladosController.php";

							$controlador = new \App\Controllers\RequisicionTrasladosController();

							$controlador -> create($param1);

						} else {

							require_once "app/Controllers/TrasladosController.php";
							self::execute($rutas, new \App\Controllers\TrasladosController);

						}
						break;
					case "requisicion-traslados" :

						require_once "app/Controllers/RequisicionTrasladosController.php";
						self::execute($rutas, new \App\Controllers\RequisicionTrasladosController);
						break;
					case "politicas":
						require_once "app/Controllers/PoliticasController.php";
						self::execute($rutas, new \App\Controllers\PoliticasController);
						break;
					case "desempeno":

						require_once "app/Controllers/DesempenoController.php";
						self::execute($rutas, new \App\Controllers\DesempenoController);
						break;
					case "estimaciones":

						require_once "app/Controllers/EstimacionesController.php";
						self::execute($rutas, new \App\Controllers\EstimacionesController);
						break;
					case "requisicion-gastos":

						require_once "app/Controllers/RequisicionGastosController.php";
						self::execute($rutas, new \App\Controllers\RequisicionGastosController);
						break;
					case "escaner":

						require_once "app/Controllers/EscanerController.php";

						self::execute($rutas, new \App\Controllers\EscanerController);
						break;
					case "gasto-detalles":

						require_once "app/Controllers/GastoDetallesController.php";

						self::execute($rutas, new \App\Controllers\GastoDetallesController);
						break;
					case "alertas":

						require_once "app/Controllers/AlertasController.php";

						self::execute($rutas, new \App\Controllers\AlertasController);
						break;
					case "gastos":

						require_once "app/Controllers/GastosController.php";

						self::execute($rutas, new \App\Controllers\GastosController);
						break;
					case "obras":

						require_once "app/Controllers/ObrasController.php";

						self::execute($rutas, new \App\Controllers\ObrasController);
						break;
					case "tarea-observaciones":

						require_once "app/Controllers/TareaObservacionesController.php";

						self::execute($rutas, new \App\Controllers\TareaObservacionesController);
						break;
						
				    case "tareas":
						require_once "app/Controllers/TareasController.php";
						self::execute($rutas, new \App\Controllers\TareasController);
						break;

				    case "generadores":
						
						require_once "app/Controllers/GeneradoresController.php";

						self::execute($rutas, new \App\Controllers\GeneradoresController);
						break;
				    case "generador-observaciones":
						
						require_once "app/Controllers/GeneradorObservacionesController.php";

						self::execute($rutas, new \App\Controllers\GeneradorObservacionesController);
						break;
				    case "generador-detalles":
						
						require_once "app/Controllers/GeneradorDetallesController.php";

						self::execute($rutas, new \App\Controllers\GeneradorDetallesController);
						break;

					case "ingreso":

						require_once "app/Controllers/LoginController.php";

						$controlador = new \App\Controllers\LoginController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();
					
						        } elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						        	$controlador -> login();
						        	
						        } else {

						        	header("Location:" . Route::routes('inicio'));
                    				die();

						        }
					
						        break;
					
							default:

								header("Location:" . Route::routes('inicio'));
                    			die();
					
						}

						break;

					case "perfil":

						require_once "app/Controllers/UsuariosController.php";

						$controlador = new \App\Controllers\UsuariosController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> editPerfil();
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;

					case "inicio":

						require_once "app/Controllers/HomeController.php";

						$ejecuta = new \App\Controllers\HomeController();

						$ejecuta -> index();

						break;

					case "maquinaria-tipos":

						require_once "app/Controllers/MaquinariaTiposController.php";

						self::execute($rutas, new \App\Controllers\MaquinariaTiposController);

						break;

					case "marcas":

						require_once "app/Controllers/MarcasController.php";

						self::execute($rutas, new \App\Controllers\MarcasController);

						break;

					case "modelos":

						require_once "app/Controllers/ModelosController.php";

						self::execute($rutas, new \App\Controllers\ModelosController);

						break;

					case "colores":

						require_once "app/Controllers/ColoresController.php";

						self::execute($rutas, new \App\Controllers\ColoresController);

						break;

					case "estatus":

						require_once "app/Controllers/EstatusController.php";

						self::execute($rutas, new \App\Controllers\EstatusController);

						break;

					case "ubicaciones":

						require_once "app/Controllers/UbicacionesController.php";

						self::execute($rutas, new \App\Controllers\UbicacionesController);

						break;

					case "almacenes":

						require_once "app/Controllers/AlmacenesController.php";

						self::execute($rutas, new \App\Controllers\AlmacenesController);

						break;

					case "maquinarias":

						if ( count($rutas) == 3 && $rutas[2] == 'crear-checklist' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/ChecklistMaquinariasController.php";

							$controlador = new \App\Controllers\ChecklistMaquinariasController();

							$controlador -> create($param1);

						} else {

							require_once "app/Controllers/MaquinariasController.php";

							self::execute($rutas, new \App\Controllers\MaquinariasController);

						}

						break;

					case "servicio-centros":

						require_once "app/Controllers/ServicioCentrosController.php";

						self::execute($rutas, new \App\Controllers\ServicioCentrosController);

						break;

					case "unidades":

						require_once "app/Controllers/UnidadesController.php";

						self::execute($rutas, new \App\Controllers\UnidadesController);

						break;

					case "mantenimiento-tipos":

						require_once "app/Controllers/MantenimientoTiposController.php";

						self::execute($rutas, new \App\Controllers\MantenimientoTiposController);

						break;

					case "servicio-tipos":

						require_once "app/Controllers/ServicioTiposController.php";

						self::execute($rutas, new \App\Controllers\ServicioTiposController);

						break;

					case "servicio-estatus":

						require_once "app/Controllers/ServicioEstatusController.php";

						self::execute($rutas, new \App\Controllers\ServicioEstatusController);

						break;

					case "estatus-orden-compra":

						require_once "app/Controllers/EstatusOrdenCompraController.php";

						self::execute($rutas, new \App\Controllers\EstatusOrdenCompraController);

						break;
						
					case "solicitud-tipos":

						require_once "app/Controllers/SolicitudTiposController.php";

						self::execute($rutas, new \App\Controllers\SolicitudTiposController);

						break;

					case "servicios":

						if ( count($rutas) == 3 && $rutas[2] == 'crear-requisicion' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/RequisicionesController.php";

							$controlador = new \App\Controllers\RequisicionesController();

							$controlador -> create($param1);

						} else {

							require_once "app/Controllers/ServiciosController.php";

							self::execute($rutas, new \App\Controllers\ServiciosController);					

						}

						break;
					case "orden-compra":

						require_once "app/Controllers/OrdenCompraController.php";

						self::execute($rutas, new \App\Controllers\OrdenCompraController);

						break;

					case "requisiciones":

						if ( count($rutas) == 3 && $rutas[2] == 'crear-orden-compra' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/OrdenCompraController.php";

							$controlador = new \App\Controllers\OrdenCompraController();

							$controlador -> create($param1);

							break;

						}
						if ( count($rutas) == 3 && $rutas[2] == 'crear-cotizacion' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/CotizacionesController.php";

							$controlador = new \App\Controllers\CotizacionesController();

							$controlador -> store($param1);

							break;

						}
						if ( count($rutas) == 2 && $rutas[1] == 'crear' ) {

							$contenido = array('modulo' => 'vistas/modulos/errores/404.php');

            				include "vistas/modulos/plantilla.php";

            				break;

						} if ( count($rutas) == 4 && $rutas[2] == 'download' ) {

							$param1 = $rutas[1];
							// $tipo = ( $rutas[3] == 'comprobantes' ) ? 1 : 2;
							if ( $rutas[3] == 'comprobantes' ) $tipo = 1;
							elseif ( $rutas[3] == 'ordenes' ) $tipo = 2;
							elseif ( $rutas[3] == 'facturas' ) $tipo = 3;
							elseif ( $rutas[3] == 'cotizaciones' ) $tipo = 4;
							elseif ( $rutas[3] == 'soportes' ) $tipo = 6;
							else $tipo = 5;

							require_once "app/Controllers/RequisicionesController.php";

							$controlador = new \App\Controllers\RequisicionesController();

							$controlador -> download($param1, $tipo);

							break;

						} else {

							require_once "app/Controllers/RequisicionesController.php";

							self::execute($rutas, new \App\Controllers\RequisicionesController);

							break;

						}

					case "actividad-semanal":

						require_once "app/Controllers/ActividadesController.php";

						self::execute($rutas, new \App\Controllers\ActividadesController);

						break;

					case "informacion-tecnica-tags":

						require_once "app/Controllers/InformacionTecnicaTagsController.php";

						self::execute($rutas, new \App\Controllers\InformacionTecnicaTagsController);

						break;

					case "informacion-tecnica":

						require_once "app/Controllers/InformacionTecnicaController.php";

						self::execute($rutas, new \App\Controllers\InformacionTecnicaController);

						break;

					case "combustible-cargas":

						require_once "app/Controllers/CombustibleCargaController.php";

						self::execute($rutas, new \App\Controllers\CombustibleCargaController);

						break;

					case "combustible-rendimiento":

						require_once "app/Controllers/CombustibleRendimientoController.php";

						self::execute($rutas, new \App\Controllers\CombustibleRendimientoController);

						break;

					case "programacion":

						require_once "app/Controllers/ProgramacionController.php";

						self::execute($rutas, new \App\Controllers\ProgramacionController);

						break;

					case "empleados":

						require_once "app/Controllers/EmpleadosController.php";

						self::execute($rutas, new \App\Controllers\EmpleadosController);

						break;

					case "proveedores":

						require_once "app/Controllers/ProveedoresController.php";

						self::execute($rutas, new \App\Controllers\ProveedoresController);

						break;

					case "configuracion-ordenes-compra":

						require_once "app/Controllers/ConfiguracionOrdenesCompraController.php";

						$controlador = new \App\Controllers\ConfiguracionOrdenesCompraController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> edit(1);

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> update(1);
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}


					break;

					case "resumen-costos":

						require_once "app/Controllers/ResumenCostosController.php";

						self::execute($rutas, new \App\Controllers\ResumenCostosController);

						break;

					case "configuracion-requisiciones":

						require_once "app/Controllers/ConfiguracionRequisicionesController.php";

						$controlador = new \App\Controllers\ConfiguracionRequisicionesController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> edit(1);

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> update(1);
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;

						
						case "configuracion-puesto-tipo":

							require_once "app/Controllers/ConfiguracionPuestoMantenimientoController.php";
	
							$controlador = new \App\Controllers\ConfiguracionPuestoMantenimientoController();
	
							switch ( count($rutas) ) {
						
								case 1:						    	
						
									if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
						
										$controlador -> edit(1);
	
									} 
						
									break;
						
								default:
	
									$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
									include "vistas/modulos/plantilla.php";
						
							}
	
						break;

					case "configuracion-correo-electronico":

						require_once "app/Controllers/ConfiguracionCorreoElectronicoController.php";

						$controlador = new \App\Controllers\ConfiguracionCorreoElectronicoController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> edit(1);

						    	} elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						    		$controlador -> update(1);
					
						        } else {

						        	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

						        }
					
						        break;
					
							default:

								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;

					case "empresas":

						require_once "app/Controllers/EmpresasController.php";

						self::execute($rutas, new \App\Controllers\EmpresasController);

						break;

					case "sucursales":

						require_once "app/Controllers/SucursalesController.php";

						self::execute($rutas, new \App\Controllers\SucursalesController);

						break;

					case "usuarios":

						require_once "app/Controllers/UsuariosController.php";

						self::execute($rutas, new \App\Controllers\UsuariosController);

						break;

					case "perfiles":

						require_once "app/Controllers/PerfilesController.php";

						self::execute($rutas, new \App\Controllers\PerfilesController);

						break;

					case "permisos":

						require_once "app/Controllers/PermisosController.php";
						self::execute($rutas, new \App\Controllers\PermisosController);

					break;
					case "puestos":

						require_once "app/Controllers/PuestosController.php";	
						self::execute($rutas, new \App\Controllers\PuestosController);
	
					break;	

					case "inventarios":

						if ( count($rutas) == 3 && $rutas[2] == 'crear' ) {

							$param1 = $rutas[1];

							require_once "app/Controllers/InventariosController.php";


							$controlador = new \App\Controllers\InventariosController();

							$controlador -> create($param1);

						} else {

							require_once "app/Controllers/InventariosController.php";
							self::execute($rutas, new \App\Controllers\InventariosController);

						}


					break;

					case "inventario-salidas":
						require_once "app/Controllers/InventarioSalidasController.php";
						self::execute($rutas, new \App\Controllers\InventarioSalidasController);
					break;

					case "resguardos":
						require_once "app/Controllers/ResguardosController.php";
						self::execute($rutas, new \App\Controllers\ResguardosController);
						
					break;

					case "reportes":

						require_once "app/Controllers/ReportesController.php";

						// self::execute($rutas, new \App\Controllers\ReportesController);

						$controlador = new \App\Controllers\ReportesController();

						switch ( count($rutas) ) {
					
						    case 1:
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();
					
						        }
					
						        break;
					
						    case 3:
					
						    	$param1 = $rutas[1];
						    	$param2 = $rutas[2];
					
						    	if ( $param1 == "crear" && ( $param2 == CONST_EXCEL || $param2 == CONST_PDF ) ) {

							        $controlador -> create($param2);

						    	} else {
					
						    		// include "vistas/modulos/errores/404.php";
						    		$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";
					
						    	}
					
						        break;
					
							default:
					
								// include "vistas/modulos/errores/404.php";
								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;

					/*------------------------------
					| RUTAS - PROVEEDOR
					------------------------------*/
					
					case "categoria-proveedores":
						require_once "app/Controllers/CategoriaProveedorController.php";
						self::execute($rutas, new \App\Controllers\CategoriaProveedorController);
						break;
					case "categoria-permiso-proveedor":
						require_once "app/Controllers/CategoriasPermisosController.php";
						self::execute($rutas, new \App\Controllers\CategoriasPermisosController);
						break;
					case "permiso-proveedor":
						require_once "app/Controllers/PermisoCategoriaProveedorController.php";
						self::execute($rutas, new \App\Controllers\PermisoCategoriaProveedorController);
						break;

					case "solicitud-proveedor":
						require_once "app/Controllers/SolicitudProveedorController.php";
						self::execute($rutas, new \App\Controllers\SolicitudProveedorController);
						break;
					
					case "resguardos":

						require_once "app/Controllers/ResguardosController.php";

						$controlador = new \App\Controllers\ResguardosController();

						switch ( count($rutas) ) {
					
						    case 1:
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();
					
						        }
					
						        break;
					
						    case 3:

						    	// Si es POST debe validar las variables y si es GET genera el PDF
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" || $_SERVER['REQUEST_METHOD'] === "POST" ) {
					
							    	$param1 = $rutas[1];
							    	$param2 = $rutas[2];
						
							    	// if ( $param1 == "crear" && ( $param2 == CONST_EXCEL || $param2 == CONST_PDF ) ) {
							    	if ( $param1 == "crear" && $param2 == CONST_PDF ) {

							    		if ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

								        	$controlador -> create($param2);

								        } else {

								        	$controlador -> exportPDF();

								        }

							    	} else {
						
							    		// include "vistas/modulos/errores/404.php";
							    		$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            							include "vistas/modulos/plantilla.php";
						
							    	}

							    } else {

							    	// include "vistas/modulos/errores/404.php";
							    	$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            						include "vistas/modulos/plantilla.php";

							    }
					
						        break;
					
							default:
					
								// include "vistas/modulos/errores/404.php";
								$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            					include "vistas/modulos/plantilla.php";
					
						}

						break;

					case "salir":

						include "vistas/modulos/salir.php";

						break;

				}

			} else {

				// include "vistas/modulos/errores/404.php";
				$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
            	include "vistas/modulos/plantilla.php";

			}

	    } else {

			require_once "app/Controllers/HomeController.php";

			$ejecuta = new \App\Controllers\HomeController();

			$ejecuta -> index();

	    }

	}

	static public function proveedor()
	{
		$rutas = array();
		$ruta = null;

	    if ( isset($_GET["ruta"]) ) {

			$rutas = explode("/", $_GET["ruta"]);
			
	    	$ruta = $rutas[0];

			if (
				$ruta == "inicio" || 
				$ruta == "formulario-proveedor" || 
				$ruta == "proveedor" || 
				$ruta == "ingreso" || 
				$ruta == "ordenes-compra" || 
				$ruta == "datos-fiscales" || 

				$ruta == "datos-generales" || 
				$ruta == "datos-legales" || 
				$ruta == "datos-financieros" || 
				$ruta == "calidad-producto" || 

				$ruta == "estados-cuenta" || 
				$ruta == "debida-deligencia" || 
				$ruta == "vendedores" || 
				$ruta == "salir" || 
				$ruta == "cotizaciones") {

				switch($ruta) {
					case "vendedores":
						require_once "app/Controllers/VendedoresController.php";
						self::execute($rutas, new \App\Controllers\VendedoresController);
						break;
					case "inicio":
						require_once "app/Controllers/HomeProveedorController.php";
						$ejecuta = new \App\Controllers\HomeProveedorController();
						$ejecuta -> index();
						break;
					case "formulario-proveedor":
						require_once "app/Controllers/FormularioProveedorController.php";
						self::execute($rutas, new \App\Controllers\FormularioProveedorController);
						break;
					case "debida-deligencia":
						require_once "app/Controllers/DebidaDiligenciaController.php";
						self::execute($rutas, new \App\Controllers\DebidaDiligenciaController);
						break;
					case "estados-cuenta":
						require_once "app/Controllers/EstadoCuentaController.php";
						self::execute($rutas, new \App\Controllers\EstadoCuentaController);
						break;

					//**A CAMBIO**/
					case "datos-fiscales":
						require_once "app/Controllers/DatosFiscalesController.php";
						self::execute($rutas, new \App\Controllers\DatosFiscalesController);
						break;
					//**A CAMBIO**/

					case "datos-generales":
						require_once "app/Controllers/DatosGeneralesProveedorController.php";
						self::execute($rutas, new \App\Controllers\DatosGeneralesProveedorController);
						break;
					case "datos-legales":
						require_once "app/Controllers/DatosLegalesProveedorController.php";
						self::execute($rutas, new \App\Controllers\DatosLegalesProveedorController);
						break;
					case "datos-financieros":
						require_once "app/Controllers/DatosFinancierosProveedorController.php";
						self::execute($rutas, new \App\Controllers\DatosFinancierosProveedorController);
						break;
					case "calidad-producto":
						require_once "app/Controllers/CalidadProductoProveedorController.php";
						self::execute($rutas, new \App\Controllers\CalidadProductoProveedorController);
						break;
					
					case "ordenes-compra":
						require_once "app/Controllers/OrdenCompraProveedorController.php";
						self::execute($rutas, new \App\Controllers\OrdenCompraProveedorController);
						break;
					case "cotizaciones":
						require_once "app/Controllers/CotizacionesController.php";
						self::execute($rutas, new \App\Controllers\CotizacionesController);
						break;
					case "ingreso":
						require_once "app/Controllers/LoginProveedorController.php";

						$controlador = new \App\Controllers\LoginProveedorController();

						switch ( count($rutas) ) {
					
						    case 1:						    	
					
						    	if ( $_SERVER['REQUEST_METHOD'] === "GET" ) {
					
						    		$controlador -> index();
					
						        } elseif ( $_SERVER['REQUEST_METHOD'] === "POST" ) {

						        	$controlador -> login();
						        	
						        } else {

						        	header("Location:" . Route::routes('inicio'));
									die();

						        }
					
						        break;
					
							default:

								header("Location:" . Route::routes('inicio'));
								die();
					
						}
						break;
					case "salir":

						include "vistas/modulos/salir.php";

						break;
				}

			} else {
				
				$contenido = array('modulo' => 'vistas/modulos/errores/404.php');
				include "vistas/modulos/plantilla_proveedores.php";
			}

	    } else {

			require_once "app/Controllers/HomeProveedorController.php";

			$ejecuta = new \App\Controllers\HomeProveedorController();

			$ejecuta -> index();

	    }
	}

	static public function getRoute() {

		$rutas = array();
		$ruta = "inicio";

	    if ( isset($_GET["ruta"]) ) {

			$rutas = explode("/", $_GET["ruta"]);
			
	    	$ruta = $rutas[0];

	    }

	    return $ruta;

	}
}
