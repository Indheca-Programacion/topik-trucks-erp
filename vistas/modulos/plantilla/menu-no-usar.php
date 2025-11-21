<aside class="main-sidebar">

	<section class="sidebar">

		<ul class="sidebar-menu">

		<?php

		use App\Route;

		if ( usuarioAutenticado() ) {

			echo '<li' . ( (Route::getRoute() == "inicio") ? ' class="active"' : '' ) . '>

				<a href="'.  Route::routes('inicio') .'">

					<i class="fa fa-home"></i>
					<span>Inicio</span>

				</a>';

			// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("depositarios") ) {

			// echo '<li' . ( (Route::getRoute() == "depositarios") ? ' class="active"' : '' ) . '>

			// 	<a href="'.  Route::names('depositarios.index') .'">

			// 		<i class="fa fa-male"></i>
			// 		<span>Usuarios Reponsables</span>

			// 	</a>

			// </li>';

			// }

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") ) {

				echo '<li' . ( (Route::getRoute() == "maquinarias") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('maquinarias.index') .'">

							<i class="fa fa-truck"></i>
							<span>Maquinarias</span>

						</a>

					</li>';

			}

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") || $usuarioAutenticado->checkPermiso("ordenCompras") || $usuarioAutenticado->checkPermiso("recepciones") ) {

			echo '<li class="treeview' . ( (Route::getRoute() == "proveedores" || Route::getRoute() == "ordenes-compra" || Route::getRoute() == "recepciones" ) ? ' active"' : '' ) . '">

				<a href="#">

					<i class="fa fa-shopping-basket"></i>
					
					<span>Compras</span>
					
					<span class="pull-right-container">
					
						<i class="fa fa-angle-left pull-right"></i>

					</span>

				</a>

				<ul class="treeview-menu">';

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") ) {

					echo '<li' . ( (Route::getRoute() == "proveedores") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('proveedores.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Proveedores</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("ordenCompras") ) {

					echo '<li' . ( (Route::getRoute() == "ordenes-compra") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('ordenes-compra.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Ordenes de Compra</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("recepciones") ) {

					echo '<li' . ( (Route::getRoute() == "recepciones") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('recepciones.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Recepciones</span>

						</a>

					</li>';

					}
					
				echo '</ul>

			</li>';

			}

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("clientes") || $usuarioAutenticado->checkPermiso("cotizciones") ) {

			echo '<li class="treeview' . ( (Route::getRoute() == "clientes" || Route::getRoute() == "cotizaciones" ) ? ' active"' : '' ) . '">

				<a href="#">

					<i class="fa fa-shopping-cart"></i>
					
					<span>Ventas</span>
					
					<span class="pull-right-container">
					
						<i class="fa fa-angle-left pull-right"></i>

					</span>

				</a>

				<ul class="treeview-menu">';

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("clientes") ) {

					echo '<li' . ( (Route::getRoute() == "clientes") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('clientes.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Clientes</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cotizaciones") ) {

					echo '<li' . ( (Route::getRoute() == "cotizaciones") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('cotizaciones.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Cotizaciones</span>

						</a>

					</li>';

					}
					
				echo '</ul>

			</li>';

			}

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("categorias") || $usuarioAutenticado->checkPermiso("lineas") || $usuarioAutenticado->checkPermiso("marcas") || $usuarioAutenticado->checkPermiso("productos") ) {

			echo '<li class="treeview' . ( (Route::getRoute() == "categorias" || Route::getRoute() == "lineas" || Route::getRoute() == "marcas" || Route::getRoute() == "productos") ? ' active"' : '' ) . '">

				<a href="#">

					<i class="fa fa-product-hunt" aria-hidden="true"></i>
					
					<span>Productos</span>
					
					<span class="pull-right-container">
					
						<i class="fa fa-angle-left pull-right"></i>

					</span>

				</a>

				<ul class="treeview-menu">';

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("categorias") ) {

					echo '<li' . ( (Route::getRoute() == "categorias") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('categorias.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Categorías</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("lineas") ) {
					
					echo '<li' . ( (Route::getRoute() == "lineas") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('lineas.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Líneas</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("marcas") ) {

					echo '<li' . ( (Route::getRoute() == "marcas") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('marcas.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Marcas</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("productos") ) {

					echo '<li' . ( (Route::getRoute() == "productos") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('productos.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Productos</span>

						</a>

					</li>';

					}

				echo '</ul>

			</li>';

			}

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("condicionPagos") ) {

			echo '<li class="treeview' . ( (Route::getRoute() == "condiciones-pago") ? ' active"' : '' ) . '">

				<a href="#">

					<i class="fa fa-cog" aria-hidden="true"></i>
					
					<span>Configuración</span>
					
					<span class="pull-right-container">
					
						<i class="fa fa-angle-left pull-right"></i>

					</span>

				</a>

				<ul class="treeview-menu">';

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("condicionPagos") ) {

					echo '<li' . ( (Route::getRoute() == "condiciones-pago") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('condiciones-pago.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Condiciones de Pago</span>

						</a>

					</li>';

					}

				echo '</ul>

			</li>';

			}

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empresas") || $usuarioAutenticado->checkPermiso("sucursales") ) {

				// <i class="fa fa-gear"></i>

			echo '<li class="treeview' . ( (Route::getRoute() == "empresas" || Route::getRoute() == "sucursales" ) ? ' active"' : '' ) . '">

				<a href="#">

					<i class="fa fa-building"></i>
					
					<span>Empresas</span>
					
					<span class="pull-right-container">
					
						<i class="fa fa-angle-left pull-right"></i>

					</span>

				</a>

				<ul class="treeview-menu">';

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("empresas") ) {

						// <a href="'. Route::names('empresas.edit', 1) .'">

					echo '<li' . ( (Route::getRoute() == "empresas") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('empresas.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Empresas</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("sucursales") ) {
					
					echo '<li' . ( (Route::getRoute() == "sucursales") ? ' class="active"' : '' ) . '>

						<a href="'. Route::names('sucursales.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Sucursales</span>

						</a>

					</li>';

					}

					// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("niveles2") ) {

					// echo '<li' . ( (Route::getRoute() == "niveles2") ? ' class="active"' : '' ) . '>

					// 	<a href="'.  Route::names('niveles2.index') .'">

					// 		<i class="fa fa-circle-o"></i>
					// 		<span>Nivel 2</span>

					// 	</a>

					// </li>';

					// }

					// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("niveles3") ) {

					// echo '<li' . ( (Route::getRoute() == "niveles3") ? ' class="active"' : '' ) . '>

					// 	<a href="'.  Route::names('niveles3.index') .'">

					// 		<i class="fa fa-circle-o"></i>
					// 		<span>Nivel 3</span>

					// 	</a>

					// </li>';

					// }

				echo '</ul>

			</li>';

			}

			// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cuentas") || $usuarioAutenticado->checkPermiso("depreciacion") ) {

			// echo '<li class="treeview' . ( (Route::getRoute() == "cuentas" || Route::getRoute() == "depreciacion") ? ' active"' : '' ) . '">

			// 	<a href="#">

			// 		<i class="fa fa-balance-scale" aria-hidden="true"></i>
					
			// 		<span>Contabilidad</span>
					
			// 		<span class="pull-right-container">
					
			// 			<i class="fa fa-angle-left pull-right"></i>

			// 		</span>

			// 	</a>

			// 	<ul class="treeview-menu">';

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("cuentas") ) {
					
			// 		echo '<li' . ( (Route::getRoute() == "cuentas") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('cuentas.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Cuentas</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("depreciacion") ) {

			// 		echo '<li' . ( (Route::getRoute() == "depreciacion") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('depreciacion.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Depreciación</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 	echo '</ul>

			// </li>';

			// }

			// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("proveedores") ) {

			// echo '<li' . ( (Route::getRoute() == "proveedores") ? ' class="active"' : '' ) . '>

			// 	<a href="'.  Route::names('proveedores.index') .'">

			// 		<i class="fa fa-users"></i>
			// 		<span>Proveedores</span>

			// 	</a>

			// </li>';

			// }

			// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("tipos") || $usuarioAutenticado->checkPermiso("marcas") || $usuarioAutenticado->checkPermiso("colores") || $usuarioAutenticado->checkPermiso("estados") ) {

			// echo '<li class="treeview' . ( (Route::getRoute() == "tipos" || Route::getRoute() == "marcas" || Route::getRoute() == "colores" || Route::getRoute() == "estados") ? ' active"' : '' ) . '">

			// 	<a href="#">

			// 		<i class="fa fa-table" aria-hidden="true"></i>
					
			// 		<span>Otros</span>
					
			// 		<span class="pull-right-container">
					
			// 			<i class="fa fa-angle-left pull-right"></i>

			// 		</span>

			// 	</a>

			// 	<ul class="treeview-menu">';

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("tipos") ) {

			// 		echo '<li' . ( (Route::getRoute() == "tipos") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('tipos.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Tipos</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("marcas") ) {
					
			// 		echo '<li' . ( (Route::getRoute() == "marcas") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('marcas.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Marcas</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("colores") ) {

			// 		echo '<li' . ( (Route::getRoute() == "colores") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('colores.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Colores</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("estados") ) {

			// 		echo '<li' . ( (Route::getRoute() == "estados") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('estados.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Estados</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 	echo '</ul>

			// </li>';

			// }

			// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("bienes") || $usuarioAutenticado->checkPermiso("inmuebles") || $usuarioAutenticado->checkPermiso("transportes") ) {

			// echo '<li class="treeview' . ( (Route::getRoute() == "bienes" || Route::getRoute() == "inmuebles" || Route::getRoute() == "transportes") ? ' active"' : '' ) . '">

			// 	<a href="#">

			// 		<i class="fa fa-pencil-square-o"></i>
					
			// 		<span>Registro de Activos</span>
					
			// 		<span class="pull-right-container">
					
			// 			<i class="fa fa-angle-left pull-right"></i>

			// 		</span>

			// 	</a>

			// 	<ul class="treeview-menu">';

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("bienes") ) {
					
			// 		echo '<li' . ( (Route::getRoute() == "bienes") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('bienes.index') .'">

			// 				<i class="fa fa-laptop"></i>
			// 				<span>Bienes Muebles</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("inmuebles") ) {

			// 		echo '<li' . ( (Route::getRoute() == "inmuebles") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('inmuebles.index') .'">

			// 				<i class="fa fa-building-o"></i>
			// 				<span>Bienes Inmuebles</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("transportes") ) {

			// 		echo '<li' . ( (Route::getRoute() == "transportes") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::names('transportes.index') .'">

			// 				<i class="fa fa-car"></i>
			// 				<span>Equipos de Transporte</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 	echo '</ul>

			// </li>';

			// }

			// if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("reportes") ) {

			// echo '<li class="treeview' . ( (Route::getRoute() == "reportes" || Route::getRoute() == "resguardos") ? ' active"' : '' ) . '">

			// 	<a href="#">

			// 		<i class="fa fa-print" aria-hidden="true"></i>
					
			// 		<span>Reportes</span>
					
			// 		<span class="pull-right-container">
					
			// 			<i class="fa fa-angle-left pull-right"></i>

			// 		</span>

			// 	</a>

			// 	<ul class="treeview-menu">';

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("reportes") ) {
					
			// 		echo '<li' . ( (Route::getRoute() == "reportes") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::routes('reportes.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Reporte de Activos</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 		if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("reportes") ) {

			// 		echo '<li' . ( (Route::getRoute() == "resguardos") ? ' class="active"' : '' ) . '>

			// 			<a href="'.  Route::routes('resguardos.index') .'">

			// 				<i class="fa fa-circle-o"></i>
			// 				<span>Resguardos</span>

			// 			</a>

			// 		</li>';

			// 		}

			// 	echo '</ul>

			// </li>';

			// }

			if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("usuarios") || $usuarioAutenticado->checkPermiso("perfiles") || $usuarioAutenticado->checkPermiso("permisos") ) {

			echo '<li class="treeview' . ( (Route::getRoute() == "usuarios" || Route::getRoute() == "perfiles" || Route::getRoute() == "permisos") ? ' active"' : '' ) . '">

				<a href="#">

					<i class="fa fa-user"></i>
					
					<span>Usuarios</span>
					
					<span class="pull-right-container">
					
						<i class="fa fa-angle-left pull-right"></i>

					</span>

				</a>

				<ul class="treeview-menu">';

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("usuarios") ) {
					
					echo '<li' . ( (Route::getRoute() == "usuarios") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('usuarios.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Usuarios</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("perfiles") ) {

					echo '<li' . ( (Route::getRoute() == "perfiles") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('perfiles.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Perfiles</span>

						</a>

					</li>';

					}

					if ( $usuarioAutenticado->checkAdmin() || $usuarioAutenticado->checkPermiso("permisos") ) {

					echo '<li' . ( (Route::getRoute() == "permisos") ? ' class="active"' : '' ) . '>

						<a href="'.  Route::names('permisos.index') .'">

							<i class="fa fa-circle-o"></i>
							<span>Permisos</span>

						</a>

					</li>';

					}

				echo '</ul>

			</li>';

			} else {

				echo '<li' . ( (Route::getRoute() == "usuarios") ? ' class="active"' : '' ) . '>

					<a href="'.  Route::names('usuarios.edit', usuarioAutenticado()["id"]) .'">

						<i class="fa fa-user"></i>
						<span>Perfil</span>

					</a>

				</li>';

			}

		}

		?>

		</ul>

	</section>

</aside>
