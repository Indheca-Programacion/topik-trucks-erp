<?php
	$archivo = '';
	// var_dump($permitirEliminarPartida);
	if ( isset($requisicion->id) ) {
		$empresaId = $requisicion->empresa;
		$folio = $requisicion->folio;
		$actualServicioEstatusId = $requisicion->servicioEstatusId;
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : $requisicion->servicioEstatusId;

		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";

		$cantidad = '0.00';
		$unidad = '';
		$numeroParte = '';
		$concepto = '';
	} else {
		$servicioId = $servicio->id;
		$empresaId = $servicio->empresaId;
		$folio = $servicio->folio;
		$maquinariaId = $servicio->maquinariaId;
		$actualServicioEstatusId = '';
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : "";

		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";

		// Datos de la Maquinaria
		$maquinariaSerie = $servicio->maquinaria['serie'];

		$cantidad = '0.00';
		$unidad = '';
		$numeroParte = '';
		$concepto = '';
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
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
						<label for="gasto">Gasto:</label>
						<a href="<?= $rutaGasto?>" target="_blank"><span type="text" class="form-control form-control-sm text-uppercase"><?php echo $gasto->id ?> </a></span>
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
					
						<button type="button" class="btn btn-info" id="btnSubirComprobantes" requisicionId="<?php echo $requisicion->id; ?>">
							<i class="fas fa-folder-open"></i> Cargar Comprobantes de Pago
						</button>

						<?php if ( isset($requisicion->id) ) : ?>
						<?php foreach($requisicion->comprobantesPago as $key=>$comprobante) : ?>
						<p class="text-info mb-0"><?php echo $comprobante['archivo']; ?>
							<?php if ( $permitirEliminarArchivos ) : ?>
							<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $comprobante['id']; ?>" folio="<?php echo $comprobante['archivo']; ?>"></i>
							<?php endif; ?>
						</p>
						<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" name="comprobanteArchivos[]" multiple> -->
						<input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" multiple>
						
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

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->

<div class="card card-success card-outline">

	<div class="card-body">

		<div class="table-responsive">

			<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">

				<thead>
					<tr>
						<th class="text-right" style="min-width: 80px;">Partida</th>
						<th class="text-right">Cant.</th>
						<th>Unidad</th>
						<th style="min-width: 100px;">Num. de Parte</th>
						<th style="min-width: 100px;">Costo</th>
						<th style="min-width: 320px;">Concepto</th>
					</tr>
				</thead>

				<tbody class="text-uppercase">
					<?php if ( isset($requisicion->id) ) : ?>
					<?php foreach($requisicion->detalles as $key=>$detalle) : ?>
					<tr>
						<td partida class="text-right">
							<span><?php echo ($key + 1); ?></span>
							
						</td>
						<td class="text-right"><?php echo $detalle['cantidad']; ?></td>
						<td><?php echo fString($detalle['unidad']); ?></td>
						<td><?php echo $detalle['numeroParte']; ?></td>
						<td><?php echo '$ '. number_format($detalle['costo'],2); ?></td>
						<td><?php echo $detalle['concepto']; ?></td>
					</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>

			</table>

		</div>

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->
