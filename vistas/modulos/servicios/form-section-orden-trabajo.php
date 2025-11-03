<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">

					<div class="col-md-12 form-group">

						<label for="empresaId">Empresa:</label>
						<select name="empresaId" id="empresaId" class="custom-select form-controls select2" <?php echo ( !$formularioEditable || isset($servicio->id) ) ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
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
						<label for="servicioCentroId">Centro de Servicio:</label>
						<select name="servicioCentroId" id="servicioCentroId" class="custom-select form-controls select2" <?php echo ( !$formularioEditable || isset($servicio->id) ) ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
						<!-- <select id="servicioCentroId" class="form-control select2" style="width: 100%" disabled> -->
						<?php else: ?>
						<!-- <select name="servicioCentroId" id="servicioCentroId" class="form-control select2Add" style="width: 100%"> -->
							<option value="">Selecciona un Centro</option>
						<?php endif; ?>
							<?php foreach($servicioCentros as $servicioCentro) { ?>
								<?php if ( strtoupper(substr($servicioCentro["descripcion"], 0,4)) !== 'C.S.' ) continue; ?>
							<option value="<?php echo $servicioCentro["id"]; ?>"
								<?php echo $servicioCentroId == $servicioCentro["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($servicioCentro["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="numero">Folio asignado por sistema:</label>
						<input type="text" id="numero" value="<?php echo fString($numero); ?>" class="form-control form-control-sm text-uppercase" placeholder="" disabled>
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="folio">Folio de Orden de Trabajo:</label>
						<input type="text" id="folio" value="<?php echo fString($folio); ?>" class="form-control form-control-sm text-uppercase" placeholder="" <?php echo ( !$formularioEditable || isset($servicio->id) ) ? ' disabled' : ' disabled'; ?>>
					</div>

					<div class="col-md-6 form-group">

						<label for="servicioEstatusId">Estatus:</label>
						<select <?php echo ( !isset($servicio->id) ) ? 'name="servicioEstatusId"' : ''; ?> id="servicioEstatusId" class="custom-select form-controls select2" <?php echo ( !$formularioEditable || isset($servicio->id) ) ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
						<!-- <select id="servicioEstatusId" class="form-control select2" style="width: 100%" disabled> -->
						<?php else: ?>
						<!-- <select name="servicioEstatusId" id="servicioEstatusId" class="form-control select2Add" style="width: 100%"> -->
							<option value="">Selecciona un Estatus</option>
						<?php endif; ?>
							<?php foreach($servicioStatus as $servicioEstatus) { ?>
							<?php if ( $servicioEstatus["servicioAbierto"] || ( $servicioEstatus["servicioCerrado"] && isset($servicio->id) ) ) : ?>
							<option value="<?php echo $servicioEstatus["id"]; ?>"
								<?php echo $servicioEstatusId == $servicioEstatus["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($servicioEstatus["descripcion"])); ?>
							</option>
							<?php endif; ?>
							<?php } ?>
						</select>

					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">

						<label for="solicitudTipoId">Tipo de Solicitud:</label>
						<select name="solicitudTipoId" id="solicitudTipoId" class="custom-select form-controls select2" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
						<!-- <select id="solicitudTipoId" class="form-control select2" style="width: 100%" disabled> -->
						<?php else: ?>
							<option value="">Selecciona un Tipo de Solicitud</option>
						<?php endif; ?>
							<?php foreach($solicitudTipos as $solicitudTipo) { ?>
							<option value="<?php echo $solicitudTipo["id"]; ?>"
								<?php echo $solicitudTipoId == $solicitudTipo["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($solicitudTipo["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">
						<label for="fechaSolicitud">Fecha Solicitud:</label>
						<div class="input-group date" id="fechaSolicitudDTP" data-target-input="nearest">
							<?php if ( !isset($servicio->id) && $permitirModificarFechas ) : ?>
							<input type="text" name="fechaSolicitud" id="fechaSolicitud" value="<?php echo $fechaSolicitud; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaSolicitudDTP">
							<?php else: ?>
							<input type="text" id="fechaSolicitud" value="<?php echo $fechaSolicitud; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaSolicitudDTP" disabled>
							<?php endif; ?>
							<div class="input-group-append" data-target="#fechaSolicitudDTP" data-toggle="datetimepicker">
	                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
	                        </div>
						</div>
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="horasProyectadas">Horas Hombre Proyectadas:</label>
						<input type="text" id="horasProyectadas" name="horasProyectadas" value="<?php echo $horasProyectadas; ?>" class="form-control form-control-sm text-right campoConDecimal" placeholder="Ingresa las horas proyectadas" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
					</div>

					<div class="col-md-6 form-group">
						<label for="horasReales">Horas Hombre Reales:</label>
						<input type="text" id="horasReales" value="<?php echo $horasReales; ?>" class="form-control form-control-sm text-right campoConDecimal" disabled>
					</div>

				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">

				<div class="row">

					<div class="col-md-6 form-group">

						<label for="mantenimientoTipoId">Tipo de Mantenimiento:</label>
						<select name="mantenimientoTipoId" id="mantenimientoTipoId" class="custom-select form-controls select2" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
						<!-- <select id="mantenimientoTipoId" class="form-control select2" style="width: 100%" disabled> -->
						<?php else: ?>
						<!-- <select name="mantenimientoTipoId" id="mantenimientoTipoId" class="form-control select2Add" style="width: 100%"> -->
							<option value="">Selecciona un Tipo de Mantenimiento</option>
						<?php endif; ?>
							<?php foreach($mantenimientoTipos as $mantenimientoTipo) { ?>
							<option value="<?php echo $mantenimientoTipo["id"]; ?>"
								<?php echo $mantenimientoTipoId == $mantenimientoTipo["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($mantenimientoTipo["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">

						<label for="servicioTipoId">Tipo de Servicio:</label>
						<select name="servicioTipoId" id="servicioTipoId" class="custom-select form-controls select2" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
						<!-- <select id="servicioTipoId" class="form-control select2" style="width: 100%" disabled> -->
						<?php else: ?>
						<!-- <select name="servicioTipoId" id="servicioTipoId" class="form-control select2Add" style="width: 100%"> -->
							<option value="">Selecciona un Tipo de Servicio</option>
						<?php endif; ?>
							<?php foreach($servicioTipos as $servicioTipo) { ?>
							<option value="<?php echo $servicioTipo["id"]; ?>"
								<?php echo $servicioTipoId == $servicioTipo["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($servicioTipo["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">

						<label for="maquinariaId">Número Económico:</label>
						<select name="maquinariaId" id="maquinariaId" class="custom-select form-controls select2" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
						<?php if ( isset($servicio->id) ) : ?>
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
						<input type="text" name="maquinariaSerie" id="maquinariaSerie" value="<?php echo fString($maquinariaSerie); ?>" class="form-control form-control-sm text-uppercase" readonly>
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="maquinariaTipoDescripcion">Tipo de Maquinaria:</label>
						<input type="text" name="maquinariaTipoDescripcion" id="maquinariaTipoDescripcion" value="<?php echo fString($maquinariaTipoDescripcion); ?>" class="form-control form-control-sm text-uppercase" readonly>
					</div>

					<div class="col-md-6 form-group">
						<label for="maquinariaUbicacionDescripcion">Ubicación:</label>
						
						<select name="ubicacionId" id="ubicacionId" class="custom-select form-controls select2" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
							<?php if ( !isset($servicio->id) || isset($servicio->id) ) : ?>
							<!-- <input type="hidden" name="ubicacionId" id="ubicacionId" value="<?php echo fString($ubicacionId); ?>"> -->
							<?php else: ?>
								<?php endif; ?> 
								<option value="">Selecciona Ubicacion</option>
							<?php foreach($ubicaciones as $ubicacion) { ?>
								<option value="<?php echo $ubicacion["id"]; ?>"
									<?php echo $ubicacionId == $ubicacion["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
								</option>
							<?php } ?>
						</select>
					</div>

					<div class="col-md-6 form-group">
						<label for="maquinariaObraDescripcion">Obra:</label>
						<select name="obraId" id="obraId" class="custom-select form-controls select2" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
							<?php if ( !isset($servicio->id) || isset($servicio->id) ) : ?>
							<!-- <input type="hidden" name="ubicacionId" id="ubicacionId" value="<?php echo fString($obraId); ?>"> -->
							<?php else: ?>
								<?php endif; ?> 
								<option value="">Selecciona Obra</option>
							<?php foreach($obras as $obra) { ?>
								<option value="<?php echo $obra["id"]; ?>"
									<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
								</option>
							<?php } ?>
						</select>
					</div>

				</div>

				<?php if ( isset($servicio->id) && $servicio->solicitudTipo['nombreCorto'] == 'PROGRAMADO' ) : ?>
					<div class="row">
						<div class="col-md-6 form-group">
							<label for="horoOdometro">Horómetro / Odómetro:</label>
							<input type="text" id="horoOdometro" name="horoOdometro" value="<?php echo fString($horoOdometro); ?>" class="form-control form-control-sm text-right campoConDecimal" decimales="1" placeholder="Ingresa el indicador" <?php echo ( $permitirFinalizar && $servicio->servicioEstatusId == 8 ) ? '' : ' disabled'; ?>>
						</div>
					</div>
				<?php endif; ?>

				<div class="row d-none">

					<div class="col-md-6 form-group">
						<label for="maquinariaMarcaDescripcion">Marca:</label>
						<input type="text" name="maquinariaMarcaDescripcion" id="maquinariaMarcaDescripcion" value="<?php echo fString($maquinariaMarcaDescripcion); ?>" class="form-control form-control-sm text-uppercase" readonly>
					</div>

					<div class="col-md-6 form-group">
						<label for="maquinariaModeloDescripcion">Modelo:</label>
						<input type="text" name="maquinariaModeloDescripcion" id="maquinariaModeloDescripcion" value="<?php echo fString($maquinariaModeloDescripcion); ?>" class="form-control form-control-sm text-uppercase" readonly>
					</div>

				</div>

				<div class="form-group d-none">
					<label for="maquinariaDescripcion">Descripción:</label>
					<input type="text" name="maquinariaDescripcion" id="maquinariaDescripcion" value="<?php echo fString($maquinariaDescripcion); ?>" class="form-control form-control-sm text-uppercase" readonly>
				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="fechaProgramacion">Fecha de finalización estimada:</label>
						<div class="input-group date" id="fechaProgramacionDTP" data-target-input="nearest">
							<input type="text" name="fechaProgramacion" id="fechaProgramacion" value="<?php echo $fechaProgramacion; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaProgramacionDTP" <?php echo !$formularioEditable ? ' disabled' : ''; ?>>
							<div class="input-group-append" data-target="#fechaProgramacionDTP" data-toggle="datetimepicker">
	                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
	                        </div>
						</div>
					</div>

					<?php if ( isset($servicio->id) ) : ?>
					<div class="col-md-6 form-group">
						<label for="fechaFinalizacion">Fecha de finalización real:</label>
						<div class="input-group date" id="fechaFinalizacionDTP" data-target-input="nearest">
							<input type="text" name="fechaFinalizacion" id="fechaFinalizacion" value="<?php echo $fechaFinalizacion; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaFinalizacionDTP" <?php echo ( $permitirModificarFechas && $servicio->servicioEstatusId == 8 ) ? '' : ' disabled'; ?>>
							<div class="input-group-append" data-target="#fechaFinalizacionDTP" data-toggle="datetimepicker">
	                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
	                        </div>
						</div>
					</div>
					<?php endif; ?>

				</div>

				<div class="form-group">
					<label for="descripcion">Descripción del Trabajo a realizar:</label>
					<textarea name="descripcion" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa la Descripción del Trabajo a realizar" <?php echo !$formularioEditable ? ' disabled' : ''; ?>><?php echo fString($descripcion); ?></textarea>
				</div>

				<?php if ( isset($servicio->id) ) : ?>
				<hr>

				<div class="form-group">
					<label for="descripcion">Evidencia del trabajo terminado:</label>
					<div class="subir-fotos mb-1">

						<?php if ( $formularioEditable ) : ?>
						<button type="button" class="btn btn-info mb-2" id="btnSubirFotos">
							<i class="fas fa-images"></i> Subir Fotos
						</button>
						<?php endif; ?>

						<?php if ( $formularioEditable ) : ?>
						<button type="button" class="btn btn-info mb-2 float-right" id="verImagenes" servicioId="<?php echo $servicio->id; ?>" folio="<?php echo $folio; ?>" verBotonEliminar="true" data-toggle="modal" data-target="#modalVerImagenes" <?php echo ( $servicio->cant_imagenes == 0 ) ? 'disabled' : '' ?>>
							<i class="fas fa-eye"></i> Ver 
						<?php else: ?>
						<button type="button" class="btn btn-info mb-2 float-left" id="verImagenes" servicioId="<?php echo $servicio->id; ?>" folio="<?php echo $folio; ?>" verBotonEliminar="false" data-toggle="modal" data-target="#modalVerImagenes" <?php echo ( $servicio->cant_imagenes == 0 ) ? 'disabled' : '' ?>>
							<i class="fas fa-eye"></i> Ver Fotos 
						<?php endif; ?>
							<?php if ( $servicio->cant_imagenes > 0 ) : ?>
							<span class="badge badge-light"><?php echo $servicio->cant_imagenes; ?></span>
							<?php endif; ?>
						</button>

						<span class="previsualizar">
						</span>

						<input type="file" class="form-control form-control-sm d-none" id="imagenes" name="imagenes[]" multiple>

					</div>
					<?php if ( $formularioEditable ) : ?>
					<div class="mb-1 text-muted">Archivos permitidos JPG O PNG (con capacidad máxima de 1MB)</div>
					<?php endif; ?>

					<div class="subir-archivos mb-1">

						<?php if ( $formularioEditable ) : ?>
						<button type="button" class="btn btn-info mb-2" id="btnSubirArchivos" servicioId="<?php echo $servicio->id; ?>">
							<i class="fas fa-folder-open"></i> Subir Documentos
						</button>
						<?php endif; ?>

						<button type="button" class="btn btn-info mb-2 float-right" id="verArchivos" servicioId="<?php echo $servicio->id; ?>" folio="<?php echo $folio; ?>" verBotonEliminar="<?php echo $formularioEditable ? 'true' : 'false' ?>" data-toggle="modal" data-target="#modalVerArchivos" <?php echo ( $servicio->cant_archivos == 0 ) ? 'disabled' : '' ?>>
						<?php if ( $formularioEditable ) : ?>
							<i class="fas fa-eye"></i> Ver 
						<?php else: ?>
							<i class="fas fa-eye"></i> Ver Documentos
						<?php endif; ?>
							<?php if ( $servicio->cant_archivos > 0 ) : ?>
							<span class="badge badge-light"><?php echo $servicio->cant_archivos; ?></span>
							<?php endif; ?>
						</button>

						<!-- <input type="file" class="form-control form-control-sm d-none" id="archivos" name="archivos[]" multiple> -->

					</div>
					<?php if ( $formularioEditable ) : ?>
					<div class="mb-1 text-muted">Archivos permitidos PDF (con capacidad máxima de 4MB)</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
