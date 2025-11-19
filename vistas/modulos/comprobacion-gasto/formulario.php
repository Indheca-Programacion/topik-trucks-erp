<?php
	$archivo = '';
	if ( isset($comprobacionGasto->id) ) {
		$empresaId = $comprobacionGasto->empresaId;
		$folio = $comprobacionGasto->folio;
		$maquinariaId = $comprobacionGasto->maquinariaId;
		$actualServicioEstatusId = $comprobacionGasto->estatus["id"];
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : $comprobacionGasto->servicioEstatusId;
		$tipoRequisicion = isset($comprobacionGasto->tipoRequisicion) ? $comprobacionGasto->tipoRequisicion : '0';
		$justificacion = isset($comprobacionGasto->justificacion) ? $comprobacionGasto->justificacion : '';
		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";
		$monto = isset($comprobacionGasto->monto) ? $comprobacionGasto->monto : '0.00';

		$fechaRequerida = isset($comprobacionGasto->fechaRequerida) && !empty($comprobacionGasto->fechaRequerida)
			? fFechaLarga($comprobacionGasto->fechaRequerida)
			: fFechaLarga(date('Y-m-d'));
			
		// Datos de la Maquinaria
		$maquinariaSerie = $comprobacionGasto->maquinaria['serie'];

		$cantidad = '0.00';
		$unidad = '';
		$numeroParte = '';
		$concepto = '';
	} else {
		$empresaId = 3;
		$folio = "";
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : 0;
		$actualServicioEstatusId = '';
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : "";
		$tipoRequisicion = $requisicion->tipoRequisicion ?? '0';
		$justificacion = isset($old["justificacion"]) ? $old["justificacion"] : "";
		$observacion = isset($old["observacion"]) ? $old["observacion"] : "";
		$monto = isset($old["monto"]) ? $old["monto"] : '0.00';

		$fechaRequerida = isset($old["fechaRequerida"]) ? $old["fechaRequerida"] : fFechaLarga(date("Y-m-d"));

		// Datos de la Maquinaria
		$maquinariaSerie = "";

		$cantidad = '0.00';
		$unidad = '';
		$numeroParte = '';
		$concepto = '';
	}

	$mensaje = isset($old["mensaje"]) ? $old["mensaje"] : "";

	use App\Route;
	
?>

<div class="row col-12">

	<div class="col-md-6">
		<div class="card card-info card-outline">
			<div class="card-body">
								
				<input type="hidden" id="token_id" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">
					<div class="col-md-12 form-group">
						<label for="empresaId">Empresa:</label>
						<?php if ( !isset($comprobacionGasto->id) ) : ?>
						<select id="empresaId" name="empresaId" class="custom-select form-controls select2" >
						<?php else: ?>
						<select id="empresaId" class="custom-select form-controls select2" disabled>
						<?php endif; ?>
							<option value="">Selecciona una Empresa</option>
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
						<label for="folio">Folio de Gasto a Comprobar:</label>
						<input type="text" id="folio" value="<?php echo fString($folio); ?>" class="form-control form-control-sm text-uppercase" placeholder="" disabled>
					</div>

					<div class="col-md-6 form-group">

						<input type="hidden" name="actualServicioEstatusId" id="actualServicioEstatusId" value="<?php echo $actualServicioEstatusId; ?>">

						<label for="servicioEstatusId">Estatus:</label>
						<?php if ( !isset($comprobacionGasto->id) || ( $formularioEditable && $permitirModificarEstatus ) ) : ?>
						<select name="servicioEstatusId" id="servicioEstatusId" class="custom-select form-controls select2">
						<?php else: ?>
						<select id="servicioEstatusId" class="custom-select form-controls select2" disabled>
						<?php endif; ?>
							<?php foreach($servicioStatus as $servicioEstatus) { ?>
							<option value="<?php echo $servicioEstatus["id"]; ?>"
								<?php echo $servicioEstatusId == $servicioEstatus["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">

						<label for="maquinariaId">Número Económico:</label>
						<?php if ( !isset($comprobacionGasto->id) ) : ?>
						<select id="maquinariaId" name="maquinariaId" class="custom-select form-controls select2" >
						<?php else: ?>
						<select id="maquinariaId" class="custom-select form-controls select2" disabled>
						<?php endif; ?>
							<option value="">Selecciona un Número Económico</option>
							<?php foreach($maquinarias as $maquinaria) { ?>
							<option value="<?php echo $maquinaria["id"]; ?>"
								<?php echo $maquinariaId == $maquinaria["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
							</option>
							<?php } ?>
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

					<div class="col-md-6 form-group">
						<label for="monto">Monto:</label>
						<input type="text" name="monto" id="monto" value="<?php echo $monto; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el monto" <?php echo ( isset($comprobacionGasto->id) ) ? ' disabled' : ''; ?>>
					</div>

					<div class="col-md-12 form-group">
						<label for="justificacion">Justificación:</label>
						<textarea name="justificacion" id="justificacion" class="form-control form-control-sm" placeholder="Ingresa la justificación" <?php echo ( !$formularioEditable ) ? ' disabled' : ''; ?>><?php echo fString($justificacion); ?></textarea>
					</div>

				</div>

				<?php if ( isset($comprobacionGasto->id) && $permitirAgregarObservaciones ) : ?>
					<div class="row <?php echo ( $actualServicioEstatusId == $servicioEstatusId && !$cambioAutomaticoEstatus ) ? 'd-none' : '' ?>">
						<div class="col-12">
							<div class="form-group">
								<label for="observacion">Observación:</label>
								<input type="text" id="observacion" name="observacion" value="<?php echo fString($observacion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa una observación" <?php echo ( $actualServicioEstatusId == $servicioEstatusId && !$cambioAutomaticoEstatus ) ? 'disabled' : '' ?>>
							</div>
						</div>
					</div>
				<?php endif; ?>

				<?php if ( isset($comprobacionGasto->id) && count($comprobacionGasto->observaciones) > 0 ) : ?>
				<div class="row">
					<div class="col-12">
						<ul class="list-group pb-3">
							<?php foreach($comprobacionGasto->observaciones as $observacion) { ?>
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

				<div class="row">

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-comprobantes">
					
						<button type="button" class="btn btn-success" id="btnSubirComprobantes" >
							<i class="fas fa-folder-open"></i> Cargar Comprobantes de Pago
						</button>

						<?php if ( isset($comprobacionGasto->id) ) : ?>
						<?php foreach($comprobacionGasto->comprobantesPago as $key=>$comprobante) : ?>

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

						<input type="file" class="form-control form-control-sm d-none" id="comprobanteArchivos" multiple>
						
					</div>

					<div class="col-12 col-sm-6 col-md-12 col-xl-6 mb-1 subir-soportes d-flex flex-column align-items-end">
						<button type="button" class="btn btn-info" id="btnSubirSoportes" >
							<i class="fas fa-folder-open"></i> Cargar Soportes
						</button>

						<?php if ( isset($comprobacionGasto->id) ) : ?>
							<?php foreach($comprobacionGasto->soportes as $key=>$soporte) : ?>

							<p class="text-info mb-0" >
								<?php echo $soporte['archivo']; ?>
								<i  class="ml-1 fas fa-eye text-warnig verArchivo" archivoRuta="<?php echo $soporte['ruta']?>" style="cursor: pointer;" ></i>

								<?php if ( $permitirEliminarArchivos ) : ?>

									<i class="ml-1 fas fa-trash-alt text-danger eliminarArchivo" style="cursor: pointer;" archivoId="<?php echo $soporte['id']; ?>" folio="<?php echo $soporte['archivo'];  ?>"></i>

								<?php endif; ?>
							</p>
							<?php endforeach; ?>
						<?php endif; ?>
						<span class="lista-archivos">
						</span>

						<input type="file" class="form-control form-control-sm d-none" id="soporteArchivos" multiple>

					</div>

					<div class="col-12 text-muted">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>

				</div>

			</div> 
		</div> 
	</div> 

	<?php if ( $formularioEditable && $permitirAgregarPartida ) : ?>
	<div class="col-md-6">
		<div class="card card-warning card-outline">
			<div class="card-body">
				<div class="row">

				</div>
					<div class="row">

						<div class="col-sm-6 form-group">
							<label for="cantidad">Cantidad:</label>
							<input type="text" id="cantidad" value="<?php echo $cantidad; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la cantidad">
						</div>

						<div class="col-sm-6 form-group">
							<label for="costo_unitario">Costo Unitario:</label>
							<input type="text" id="costo_unitario" value="0" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el costo unitario">
						</div>

						<div class="col-sm-6 form-group">
							<label for="unidad">Unidad:</label>
							<input type="text" id="unidad" value="<?php echo fString($unidad); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la unidad">
						</div>

						<div class="col-sm-6 form-group">
							<label for="numeroParte">Número de Parte:</label>
							<input type="text" id="numeroParte" value="<?php echo fString($numeroParte); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número de parte">
						</div>

						<div class="col-sm-6 form-group d-none">
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

					<div class="subir-fotos mb-1 d-none">
						<button type="button" class="btn btn-info" id="btnSubirFotos">
							<i class="fas fa-images"></i> Subir Fotos
						</button>
						<span class="previsualizar"></span>
						<input type="file" class="form-control form-control-sm d-none" id="fotos" multiple>
					</div>

					<!-- <div class="mb-1 text-muted">Archivos permitidos JPG O PNG (con capacidad máxima de 1MB)</div> -->

					<button type="button" id="btnAgregarPartida" class="btn btn-primary float-right">
						<i class="fas fa-plus"></i> Agregar partida
					</button>
				</div> 
			</div> 
		</div> 
	<?php endif; ?>

</div> 


<div class="card card-success card-outline col-12">

	<div class="card-body">

		<div class="table-responsive">

			<table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisicionDetalles" width="100%">

				<thead>
					<tr>
						<th class="text-right" style="min-width: 80px;">Partida</th>
						<th class="text-right">Cant.</th>
						<th>Unidad</th>
						<th style="min-width: 160px;">Costo</th>
						<th style="min-width: 160px;">Num. de Parte</th>
						<th style="min-width: 320px;">Concepto</th>
					</tr>
				</thead>

				<tbody class="text-uppercase">
					<?php if ( isset($comprobacionGasto->id) ) : ?>
					<?php foreach($comprobacionGasto->detalles as $key=>$detalle) : ?>
					<tr>
						<td partida class="text-right">
							<span><?php echo ($key + 1); ?></span>
							<?php if ( $formularioEditable && $permitirEliminarPartida ) : ?>
							<i class="mx-1 fas fa-trash-alt text-danger eliminarPartida" style="cursor: pointer;" detalleId="<?php echo $detalle['id']; ?>"></i>
							<?php endif; ?>
						</td>
						<td class="text-right"><?php echo $detalle['cantidad']; ?></td>
						<td><?php echo fString($detalle['unidad']); ?></td>
						<td><?php echo $detalle['costo']; ?></td>
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

<?php if ( isset($comprobacionGasto->id) ) : ?>
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
	<button type="button" class="btn btn-outline-info" <?php if ( $cantidadComprobantes == 0 && $cantidadSoportes == 0 ) echo "disabled"; ?>>
		<i class="fas fa-download"></i> Descargar
	</button>
	<button type="button" class="btn btn-outline-info dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-expanded="false" <?php if ( $cantidadComprobantes == 0 && $cantidadSoportes == 0 ) echo "disabled"; ?>>
		<span class="sr-only">Alternar Menú Desplegable</span>
	</button>
	<div class="dropdown-menu">
		<a class="dropdown-item <?php if ( $cantidadComprobantes == 0 ) echo "disabled-link"; ?>" href="" id="btnDescargarComprobantes">Comprobantes de Pago</a>
		<a class="dropdown-item <?php if ( $cantidadSoportes == 0 ) echo "disabled-link"; ?>"  href=""  id="btnDescargarSoportes">Soportes</a>
		<a class="dropdown-item <?php if ( $cantidadDocs == 0 ) echo "disabled-link"; ?>" href="" id="btnDescargarTodo">Descargar Todo</a>
	</div>
</div>

<a href="<?php echo Route::names('comprobacion-gastos.print', $comprobacionGasto->id); ?>" target="_blank" class="btn btn-info float-right"><i class="fas fa-print"></i> Imprimir</a>

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