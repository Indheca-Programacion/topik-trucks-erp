<?php
	$archivo = '';
	if ( isset($requisicion->id) ) {
		$empresaId = 7;
		$folio = $requisicion->id;
		$maquinariaId = $requisicion->servicio['maquinariaId'];
		$actualServicioEstatusId = $requisicion->servicioEstatusId;
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : $requisicion->servicioEstatusId;
		$tipoRequisicion = isset($requisicion->tipoRequisicion) ? $requisicion->tipoRequisicion : '0';

		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";

		$fechaRequerida = isset($requisicion->fechaRequerida) && !empty($requisicion->fechaRequerida)
			? fFechaLarga($requisicion->fechaRequerida)
			: fFechaLarga(date('Y-m-d'));
			
		// Datos de la Maquinaria
		$maquinariaSerie = $requisicion->maquinaria['serie'];

		$costoUnitario = '0.00';
		$cantidad = '0.00';
		$unidad = '';
		$numeroParte = '';
		$concepto = '';
	} else {
		$servicioId = $servicio->id;
		$empresaId = 7;
		$folio = $servicio->id;
		$maquinariaId = $servicio->maquinariaId;
		$actualServicioEstatusId = '';
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : "";
		$tipoRequisicion = $requisicion->tipoRequisicion ?? '0';

		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";

		$fechaRequerida = isset($old["fechaRequerida"]) ? $old["fechaRequerida"] : fFechaLarga(date("Y-m-d"));

		// Datos de la Maquinaria
		$maquinariaSerie = $servicio->maquinaria['serie'];

		$cantidad = '0.00';
		$costoUnitario = '0.00';
		$unidad = '';
		$numeroParte = '';
		$concepto = '';
	}

	$rutaJson = __DIR__ . '/../../../storage/json/productos_sat.json';

	$jsonProd_Sat = json_decode(file_get_contents($rutaJson), true);


	$mensaje = isset($old["mensaje"]) ? $old["mensaje"] : "";

	use App\Route;
	
?>

<div class="row col-12">

	<div class="col-md-6">
		<div class="card card-info card-outline">
			<div class="card-body">
								
				<input type="hidden" id="token_id" name="_token" value="<?php echo createToken(); ?>">
				
				<?php if ( !isset($requisicion->id) ) : ?>
					<input type="hidden" name="servicioId" value="<?php echo $servicioId; ?>">
				<?php endif; ?>
				<?php if ( isset($requisicion->id) ) : ?>
					<input type="hidden" id="requisicionId" value="<?php echo $requisicion->id; ?>">
				<?php endif; ?>

				<div class="row">
					<div class="col-md-12 form-group">
						<label for="empresaId">Empresa:</label>
						<select id="empresaId" class="custom-select form-controls select2" disabled>
						<?php if ( isset($requisicion->id) ) : ?>
						<!-- <select id="empresaId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
						<?php else: ?>
							<option value="">Selecciona una Empresa</option>
						<?php endif; ?>
							<?php foreach($empresas as $empresa) { ?>
							<option value="<?php echo $empresa["id"]; ?>"
								<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>
				</div>

				<div class="row">	

					<div class="col-md-6 form-group">
						<?php if ( isset($requisicion->id) ) : ?>
						<label for="folio">Folio de Requisición:</label>
						<?php else: ?>
						<label for="folio">Folio de Orden de Trabajo:</label>
						<?php endif; ?>
						<input type="text" id="folio" value="<?php echo fString($folio); ?>" class="form-control form-control-sm text-uppercase" placeholder="" disabled>
					</div>

					<div class="col-md-6 form-group">

						<input type="hidden" name="actualServicioEstatusId" id="actualServicioEstatusId" value="<?php echo $actualServicioEstatusId; ?>">

						<label for="servicioEstatusId">Estatus:</label>
						<?php if ( !isset($requisicion->id) || ( $formularioEditable && $permitirModificarEstatus ) ) : ?>
						<select name="servicioEstatusId" id="servicioEstatusId" class="custom-select form-controls select2">
						<?php else: ?>
						<select id="servicioEstatusId" class="custom-select form-controls select2" disabled>
						<?php endif; ?>
							<?php if ( !isset($requisicion->id) ) : ?>
							<!-- <option value="">Selecciona un Estatus</option> -->
							<?php endif; ?>
							<?php foreach($servicioStatus as $servicioEstatus) { ?>
							<?php if ( $servicioEstatus["requisicionAbierta"] || ( $servicioEstatus["requisicionCerrada"] && isset($requisicion->id) ) ) : ?>
							<option value="<?php echo $servicioEstatus["id"]; ?>"
								<?php echo $servicioEstatusId == $servicioEstatus["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
							</option>
							<?php endif; ?>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">
						<label for="tipoRequisicion">Tipo de Requisición:</label>
						<select name="tipoRequisicion" id="tipoRequisicion" class="custom-select form-controls select2">
							<option value="0"<?php echo ( (int)($tipoRequisicion ?? 0) === 0 ) ? ' selected' : ''; ?>>PROGRAMADA</option>
							<option value="1"<?php echo ( (int)($tipoRequisicion ?? 0) === 1 ) ? ' selected' : ''; ?>>URGENTE</option>

						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="fechaRequerida">Fecha Requerida:</label>
						<div class="input-group date" id="fechaRequeridaDTP" data-target-input="nearest">
							<input type="text" name="fechaRequerida" id="fechaRequerida" value="<?php echo $fechaRequerida; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaRequeridaDTP" <?php echo ( !$formularioEditable || isset($requisicion->id) ) ? ' disabled' : ''; ?>>
							<div class="input-group-append" data-target="#fechaRequeridaDTP" data-toggle="datetimepicker">
	                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
	                        </div>
						</div>
					</div>

				</div>

				<?php if ( isset($requisicion->id) && $permitirAgregarObservaciones ) : ?>
					<div class="row <?php echo ( $actualServicioEstatusId == $servicioEstatusId && !$cambioAutomaticoEstatus ) ? 'd-none' : '' ?>">
						<div class="col-12">
							<div class="form-group">
								<label for="observacion">Observación:</label>
								<input type="text" id="observacion" name="observacion" value="<?php echo fString($observacion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una observación" <?php echo ( $actualServicioEstatusId == $servicioEstatusId && !$cambioAutomaticoEstatus ) ? 'disabled' : '' ?>>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( isset($requisicion->id) && count($requisicion->observaciones) > 0 ) : ?>
				<div class="row">
					<div class="col-12">
						<ul class="list-group pb-3">
							<?php foreach($requisicion->observaciones as $observacion) { ?>
							<?php
								$leyenda = "[{$observacion["fechaCreacion"]}] Requisición fue cambiada a estado '";
								$leyenda .= mb_strtoupper(fString($observacion["servicio_estatus.descripcion"]));
								$leyenda .= "' por ";
								$leyenda .= mb_strtoupper(fString($observacion["usuarios.nombre"]));
								$leyenda .= " ";
								$leyenda .= mb_strtoupper(fString($observacion["usuarios.apellidoPaterno"]));
								if ( !is_null($observacion["usuarios.apellidoMaterno"]) ) {
									$leyenda .= " ";
									$leyenda .= mb_strtoupper(fString($observacion["usuarios.apellidoMaterno"]));
								}
								$leyenda .= " (";
								$leyenda .= mb_strtoupper(fString($observacion["observacion"]));
								$leyenda .= ")";
							?>
							<li class="list-group-item list-group-item-success py-2 px-3"><?php echo $leyenda; ?></li>
							<?php } ?>
						</ul>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $formularioEditable && $permitirSubirArchivos ) : ?>
				<div class="row">

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-comprobantes">
					
						<button type="button" class="btn btn-success" id="btnSubirComprobantes" requisicionId="<?php echo $requisicion->id; ?>">
							<i class="fas fa-folder-open"></i> Cargar Comprobantes de Pago
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->comprobantesPago as $key=>$comprobante) : ?>

						<p class="text-info mb-0" >
							<?php echo $comprobante['archivo']; ?>
							<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $comprobante['ruta']?>" style="cursor: pointer;" ></i>

							<?php if ( $permitirEliminarArchivos ) : ?>

								<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $comprobante['id']; ?>" folio="<?php echo $comprobante['archivo'];  ?>"></i>

							<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" name="comprobanteArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" multiple>
						
					</div>

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-ordenes d-flex flex-column align-items-end">

						<button type="button" class="btn btn-warning float-right" id="btnSubirOrdenes">
							<i class="fas fa-folder-open"></i> Cargar Órdenes de Compra
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->ordenesCompra as $key=>$orden) : ?>
						<p class="text-info mb-0 text-right"><?php echo $orden['archivo']; ?>
						<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $orden['ruta']?>"style="cursor: pointer;" ></i>
							<?php if ( $permitirEliminarArchivos ) : ?>

							<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $orden['id']; ?>" folio="<?php echo $orden['archivo']; ?>"></i>
							<?php endif; ?>

						</p>
						<?php endforeach; ?>
						<?php endif; ?>


						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="ordenesArchivos" name="ordenesArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="ordenesArchivos" multiple>

					</div>

					<div class="col-12 text-muted">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>

				</div>
				<?php elseif ( isset($requisicion->id) ) : ?>
				<div class="row">
					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1">
						<?php if ( count($requisicion->comprobantesPago) > 0 ) : ?>
						<p class="text-info font-weight-bold mb-0">Comprobantes de Pago:</p>
						<?php foreach($requisicion->comprobantesPago as $key=>$comprobante) : ?>
						<p class="text-info mb-0"><?php echo $comprobante['archivo']; ?></p>
						<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 d-flex flex-column align-items-end">
						<?php if ( count($requisicion->ordenesCompra) > 0 ) : ?>
						<p class="text-info font-weight-bold mb-0 text-right">Órdenes de Compra:</p>
						<?php foreach($requisicion->ordenesCompra as $key=>$orden) : ?>
						<p class="text-info mb-0 text-right"><?php echo $orden['archivo']; ?></p>
						<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
				<?php endif; ?>

				<hr>

				<div class="row">

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-facturas">
					
						<button type="button" class="btn btn-info" id="btnSubirFacturas">
							<i class="fas fa-folder-open"></i> Cargar Facturas
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->facturas as $key=>$factura) : ?>
						<p class="text-info mb-0"><?php echo $factura['archivo']; ?>
						<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $factura['ruta']?>"style="cursor: pointer;" ></i>
							<?php if ( $permitirEliminarArchivos ) : ?>
							<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $factura['id']; ?>" folio="<?php echo $factura['archivo']; ?>"></i>
							<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="facturaArchivos" name="facturaArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="facturaArchivos" multiple>

						<div class="text-muted mt-1">Archivos permitidos PDF y XML (con capacidad máxima de 4MB)</div>
						
					</div>

					<!-- <div class="col-12 text-muted">Archivos permitidos PDF y XML (con capacidad máxima de 4MB)</div> -->

					<?php if ( $formularioEditable ) : ?>


					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-cotizaciones d-flex flex-column align-items-end mt-1">

						<button type="button" class="btn btn-info float-right" id="btnSubirCotizaciones">
							<i class="fas fa-folder-open"></i> Cargar Cotizaciones
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->cotizaciones as $key=>$cotizacion) : ?>
						<p class="text-info mb-0 text-right"><?php echo $cotizacion['archivo']; ?>
						<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $cotizacion['ruta']?>"style="cursor: pointer;" ></i>
							<?php if ( $permitirEliminarArchivos ) : ?>
							<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $cotizacion['id']; ?>" folio="<?php echo $cotizacion['archivo']; ?>"></i>
							<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="cotizacionArchivos" name="cotizacionArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="cotizacionArchivos" multiple>

						<div class="text-muted mt-1 text-right">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>

					</div>

					<!-- <div class="col-12 text-muted text-right">Archivos permitidos PDF (con capacidad máxima de 4MB)</div> -->

					<?php elseif ( isset($requisicion->id) && count($requisicion->cotizaciones) > 0 ) : ?>

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 d-flex flex-column align-items-end mt-1">
						<p class="text-info font-weight-bold mb-0 text-right">Cotizaciones:</p>
						<?php foreach($requisicion->cotizaciones as $key=>$cotizacion) : ?>
						<p class="text-info mb-0 text-right"><?php echo $cotizacion['archivo']; ?></p>
						<?php endforeach; ?>
					</div>

					<?php endif; ?>

				</div>

				<hr>

				<?php if ( $formularioEditable ) : ?>

					<div class="row">
						<!-- <div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-vales"> -->
						<div class="col-6 mb-1 subir-vales">
							<button type="button" class="btn btn-info" id="btnSubirVales">
								<i class="fas fa-folder-open"></i> Cargar Vales de Almacén
							</button>

							<?php if ( isset($requisicion->id) ) : ?>
								<?php foreach($requisicion->valesAlmacen as $key=>$vale) : ?>
									<p class="text-info mb-0"><?php echo $vale['archivo']; ?>
									<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $vale['ruta']?>"style="cursor: pointer;" ></i>
										<?php if ( $permitirEliminarArchivos ) : ?>
										<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $vale['id']; ?>" folio="<?php echo $vale['archivo']; ?>"></i>
										<?php endif; ?>
									</p>
								<?php endforeach; ?>
								<?php  foreach($requisicion->valesAlmacenDigital as $key=>$vale) : ?>
									<a href="<?php echo Route::names('inventarios.edit', $vale['id']); ?>" target="_blank">
										<p class="text-info mb-0">Entrada folio <?php echo $vale['id']; ?></p>
									</a>
								<?php endforeach; ?>
							<?php endif; ?>

							<span class="lista-archivos">
							</span>

							<input type="file" class="form-control form-control-sm d-none" id="valeArchivos" multiple>

							<div class="text-muted mt-1">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>
						</div>
						<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 d-flex flex-column align-items-end mt-1 subir-soportes">
							<button type="button" class="btn btn-info" id="btnSubirSoportes">
								<i class="fas fa-folder-open"></i> Cargar Soportes
							</button>

							<?php if ( isset($requisicion->id) ) : ?>
								<?php foreach($requisicion->soportes as $key=>$vale) : ?>
									<p class="text-info mb-0"><?php echo $vale['archivo']; ?>
									<i  class="ml-1 fas fa-eye verArchivo" archivoRuta="<?php echo $vale['ruta']?>"style="cursor: pointer;" ></i>
										<?php if ( $permitirEliminarArchivos ) : ?>
										<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $vale['id']; ?>" folio="<?php echo $vale['archivo']; ?>"></i>
										<?php endif; ?>
									</p>
								<?php endforeach; ?>
							<?php endif; ?>

							<span class="lista-archivos">
							</span>

							<input type="file" class="form-control form-control-sm d-none" id="soporteArchivos" multiple>

							<div class="text-muted mt-1">Archivos permitidos PDF e Imagenes</div>
						</div>
					</div>
								
				<?php elseif ( isset($requisicion->id) && count($requisicion->valesAlmacen) > 0 ) : ?>
						<div class="row">
							<div class="col-12 mb-1">
								<p class="text-info font-weight-bold mb-0">Vales de Almacén:</p>
							<?php foreach($requisicion->valesAlmacen as $key=>$vale) : ?>
								<p class="text-info mb-0"><?php echo $vale['archivo']; ?></p>
							<?php endforeach; ?>
							</div>
							<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 d-flex flex-column align-items-end mt-1">
								<p class="text-info font-weight-bold mb-0">Soportes:</p>
								<?php foreach($requisicion->soportes as $key=>$soporte) : ?>
									<p class="text-info mb-0"><?php echo $soporte['archivo']; ?></p>
								<?php endforeach; ?>
							</div>
					</div>
				<?php endif; ?>		
			</div> 
		</div> 
	</div> 

	<div class="col-md-6">
		<div class="card card-warning card-outline">
			<div class="card-body">
				<div class="row">

					<div class="col-md-6 form-group">

						<label for="maquinariaId">Número Económico:</label>
						<select id="maquinariaId" class="custom-select form-controls select2" disabled>
						<?php if ( isset($requisicion->id) ) : ?>
						<!-- <select id="maquinariaId" class="form-control select2" style="width: 100%" disabled> -->
						<?php else: ?>
							<option value="">Selecciona un Número Económico</option>
						<?php endif; ?>
							<?php foreach($maquinarias as $maquinaria) { ?>
							<option value="<?php echo $maquinaria["id"]; ?>"
								<?php echo $maquinariaId == $maquinaria["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">
						<label for="maquinariaSerie">Serie:</label>
						<input type="text" id="maquinariaSerie" value="<?php echo fString($maquinariaSerie); ?>" class="form-control form-control-sm text-uppercase" readonly>
					</div>

				</div>
				<?php if ( $formularioEditable && $permitirAgregarPartida ) : ?>
					<div class="row">

						<div class="col-sm-6 form-group">
							<label for="cantidad">Cantidad:</label>
							<input type="text" id="cantidad" value="<?php echo fString($cantidad); ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la cantidad">
						</div>

						<div class="col-sm-6 form-group">
							<label for="costo">Costo Unitario:</label>
							<input type="text" id="costo" value="<?php echo $costoUnitario; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el precio unitario">
						</div>

						<div class="col-sm-6 form-group">
							<label for="unidad">Unidad:</label>
							<input type="text" id="unidad" value="<?php echo fString($unidad); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la unidad">
						</div>

						<div class="col-sm-6 form-group">
							<label for="numeroParte">Número de Parte:</label>
							<input type="text" id="numeroParte" value="<?php echo fString($numeroParte); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número de parte">
						</div>

						<div class="col-sm-6 form-group">
							<label for="codigo">Código:</label>
							<div class="input-group">
								<input type="hidden" name="codigoId" id="codigoId" value="">
								<input type="text" id="codigoDescripcion" class="form-control form-control-sm text-uppercase" placeholder="Buscar Código" disabled>
								<div class="input-group-append">
									<button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#modalVerCodigosSat">
										<i class="fas fa-search"></i>
									</button>
								</div>
							</div>
						</div>

					</div>

					<div class="form-group">
						<label for="concepto">Concepto:</label>
						<textarea id="concepto" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa el concepto de la partida"><?php echo fString($concepto); ?></textarea>
					</div>

					<div class="subir-fotos mb-1">
						<button type="button" class="btn btn-info" id="btnSubirFotos">
							<i class="fas fa-images"></i> Subir Fotos
						</button>
						<span class="previsualizar"></span>
						<input type="file" class="form-control form-control-sm d-none" id="fotos" multiple>
					</div>

					<div class="mb-1 text-muted">Archivos permitidos JPG O PNG (con capacidad máxima de 1MB)</div>

					<button type="button" id="btnAgregarPartida" class="btn btn-primary float-right">
						<i class="fas fa-plus"></i> Agregar partida
					</button>
				<?php endif; ?>
			</div> 
		</div> 
	</div> 

</div> 


<div class="card card-success card-outline col-12">

	<div class="card-body">

		<div class="table-responsive">

			<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">

				<thead>
					<tr>
						<th class="text-right" style="min-width: 80px;">Partida</th>
						<th class="text-right">Cant.</th>
						<th class="text-right">Costo Unitario</th>
						<th>Unidad</th>
						<th style="min-width: 160px;">Código</th>
						<th style="min-width: 160px;">Num. de Parte</th>
						<th style="min-width: 320px;">Concepto</th>
					</tr>
				</thead>

				<tbody class="text-uppercase">
					<?php if ( isset($requisicion->id) ) : ?>
					<?php foreach($requisicion->detalles as $key=>$detalle) : ?>
					<tr>
						<td partida class="text-right">
							<span><?php echo ($key + 1); ?></span>
							<?php if ( $detalle['cant_imagenes'] == 0 ) : ?>
							<i class="mx-1 fas fa-eye text-secondary"></i>
							<?php else: ?>
							<i class="mx-1 fas fa-eye text-info verImagenes" style="cursor: pointer;" detalleId="<?php echo $detalle['id']; ?>" data-toggle="modal" data-target="#modalVerImagenes"></i>
							<?php endif; ?>
							<?php if ( $formularioEditable && $permitirEliminarPartida ) : ?>
							<i class="mx-1 fas fa-trash-alt text-danger eliminarPartida" style="cursor: pointer;" detalleId="<?php echo $detalle['id']; ?>"></i>
							<?php endif; ?>
						</td>
						<td class="text-right"><?php echo $detalle['cantidad']; ?></td>
						<td class="text-right"><?php echo $detalle['costo']; ?></td>
						<td><?php echo fString($detalle['unidad']); ?></td>
						<td><?php echo $detalle['codigoId']; ?></td>
						<td><?php echo $detalle['numeroParte']; ?></td>
						<td><?php echo $detalle['concepto']; ?></td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>

			</table>

		</div>

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->

<?php if ( isset($requisicion->id) ) : ?>
<?php if ( $formularioEditable ) : ?>
<button type="button" id="btnSend" class="btn btn-outline-primary">
	<i class="fas fa-save"></i> Actualizar
</button>
<?php else: ?>
<button type="button" id="btnSend" class="btn btn-outline-primary cargar-facturas d-none" disabled>
	<i class="fas fa-save"></i> Actualizar
</button>
<?php endif; ?>

<div class="btn-group descargar-archivos">
	<button type="button" class="btn btn-outline-info" <?php if ( $cantidadComprobantes == 0 && $cantidadOrdenes == 0 && $cantidadFacturas == 0 && $cantidadCotizaciones == 0 && $cantidadVales == 0 && $cantidadSoportes == 0 ) echo "disabled"; ?>>
		<i class="fas fa-download"></i> Descargar
	</button>
	<button type="button" class="btn btn-outline-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false" <?php if ( $cantidadComprobantes == 0 && $cantidadOrdenes == 0 && $cantidadFacturas == 0 && $cantidadCotizaciones == 0 && $cantidadVales == 0 && $cantidadSoportes == 0 ) echo "disabled"; ?>>
		<span class="sr-only">Alternar Menú Desplegable</span>
	</button>
	<div class="dropdown-menu">
		<a class="dropdown-item <?php if ( $cantidadComprobantes == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarComprobantes">Comprobantes de Pago</a>
		<a class="dropdown-item <?php if ( $cantidadOrdenes == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarOrdenes">Órdenes de Compra</a>
		<a class="dropdown-item <?php if ( $cantidadFacturas == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarFacturas">Facturas</a>
		<a class="dropdown-item <?php if ( $cantidadCotizaciones == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarCotizaciones">Cotizaciones</a>
		<a class="dropdown-item <?php if ( $cantidadVales == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarVales">Vales de Almacén</a>
		<a class="dropdown-item <?php if ( $cantidadSoportes == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarSoportes">Soportes</a>
		<a class="dropdown-item <?php if ( $cantidadDocs == 0 ) echo "disabled-link"; ?>" href="#" id="btnDescargarTodo">Descargar Todo</a>
	</div>
</div>

<a href="<?php echo Route::names('requisiciones.print', $requisicion->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>

<div id="msgSend"></div>
<?php endif ?>

<!-- Modal ver codigos sat -->
<!-- Modal para ver tablas -->
<div class="modal fade" id="modalVerCodigosSat" tabindex="-1" role="dialog" aria-labelledby="modalVerCodigosSatLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalVerCodigosSatLabel">Ver Códigos SAT</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<!-- Aquí puedes agregar la tabla que desees mostrar -->
				<div class="table-responsive">
					<table class="table table-bordered table-striped" id="tablaCodigosSat" width="100%">
						<thead>
							<tr>
								<th>Codigo</th>
								<th>Descripcion</th>
								<th>fecha de Inicio de Vigencia</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($jsonProd_Sat as $item) : ?>
							<tr class="seleccionable">
								<td><?php echo htmlspecialchars($item['id']); ?></td>
								<td><?php echo htmlspecialchars($item['descripcion']); ?></td>
								<td><?php echo ($item['fechaInicioVigencia']); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>