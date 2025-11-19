<?php
	// var_dump($servicioStatus);
	if ( isset($servicio->id) ) {
		// var_dump($servicio);
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $servicio->empresaId;
		$servicioCentroId = $servicio->servicioCentroId;
		$numero = $servicio->numero;
		$folio = isset($old["folio"]) ? $old["folio"] : $servicio->folio;
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : $servicio->maquinariaId;
		$mantenimientoTipoId = isset($old["mantenimientoTipoId"]) ? $old["mantenimientoTipoId"] : $servicio->mantenimientoTipoId;
		$servicioTipoId = isset($old["servicioTipoId"]) ? $old["servicioTipoId"] : $servicio->servicioTipoId;
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : $servicio->servicioEstatusId;
		$solicitudTipoId = isset($old["solicitudTipoId"]) ? $old["solicitudTipoId"] : $servicio->solicitudTipoId;
		$horasProyectadas = isset($old["horasProyectadas"]) ? $old["horasProyectadas"] : number_format($servicio->horasProyectadas, 2, '.', ',');
		$horasReales = number_format($servicio->horasReales, 2, '.', ',');
		$fechaSolicitud = isset($old["fechaSolicitud"]) ? $old["fechaSolicitud"] : fFechaLarga($servicio->fechaSolicitud);
		$fechaProgramacion = isset($old["fechaProgramacion"]) ? $old["fechaProgramacion"] : ( is_null($servicio->fechaProgramacion) ? "" : fFechaLarga($servicio->fechaProgramacion) );
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : ( is_null($servicio->fechaFinalizacion) ? "" : fFechaLarga($servicio->fechaFinalizacion) );

		// Datos de la Maquinaria
		$maquinariaTipoDescripcion = isset($old["maquinariaTipoDescripcion"]) ? $old["maquinariaTipoDescripcion"] : $servicio->maquinaria['maquinaria_tipos.descripcion'];
		$maquinariaUbicacionDescripcion = isset($old["maquinariaUbicacionDescripcion"]) ? $old["maquinariaUbicacionDescripcion"] : $servicio->maquinaria['ubicaciones.descripcion'];
		$maquinariaMarcaDescripcion = isset($old["maquinariaMarcaDescripcion"]) ? $old["maquinariaMarcaDescripcion"] : $servicio->maquinaria['marcas.descripcion'];
		$maquinariaModeloDescripcion = isset($old["maquinariaModeloDescripcion"]) ? $old["maquinariaModeloDescripcion"] : $servicio->maquinaria['modelos.descripcion'];
		$maquinariaDescripcion = isset($old["maquinariaDescripcion"]) ? $old["maquinariaDescripcion"] : $servicio->maquinaria['descripcion'];
		$maquinariaSerie = isset($old["maquinariaSerie"]) ? $old["maquinariaSerie"] : $servicio->maquinaria['serie'];
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$servicioCentroId = isset($old["servicioCentroId"]) ? $old["servicioCentroId"] : "";
		$numero = "";
		$folio = isset($old["folio"]) ? $old["folio"] : "";
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : "";
		$mantenimientoTipoId = isset($old["mantenimientoTipoId"]) ? $old["mantenimientoTipoId"] : "";
		$servicioTipoId = isset($old["servicioTipoId"]) ? $old["servicioTipoId"] : "";
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : "";
		$solicitudTipoId = isset($old["solicitudTipoId"]) ? $old["solicitudTipoId"] : "";
		$horasProyectadas = isset($old["horasProyectadas"]) ? $old["horasProyectadas"] : "0.00";
		$horasReales = "";
		$fechaSolicitud = isset($old["fechaSolicitud"]) ? $old["fechaSolicitud"] : fFechaLarga(date("Y-m-d"));
		$fechaProgramacion = isset($old["fechaProgramacion"]) ? $old["fechaProgramacion"] : "";
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : "";

		// Datos de la Maquinaria
		$maquinariaTipoDescripcion = isset($old["maquinariaTipoDescripcion"]) ? $old["maquinariaTipoDescripcion"] : "";
		$maquinariaUbicacionDescripcion = isset($old["maquinariaUbicacionDescripcion"]) ? $old["maquinariaUbicacionDescripcion"] : "";
		$maquinariaMarcaDescripcion = isset($old["maquinariaMarcaDescripcion"]) ? $old["maquinariaMarcaDescripcion"] : "";
		$maquinariaModeloDescripcion = isset($old["maquinariaModeloDescripcion"]) ? $old["maquinariaModeloDescripcion"] : "";
		$maquinariaDescripcion = isset($old["maquinariaDescripcion"]) ? $old["maquinariaDescripcion"] : "";
		$maquinariaSerie = isset($old["maquinariaSerie"]) ? $old["maquinariaSerie"] : "";
	}
?>

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
							<?php if ( $servicioEstatus["servicioAbierto"] || isset($servicio->id) ) : ?>
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
							<input type="text" name="fechaSolicitud" id="fechaSolicitud" value="<?php echo $fechaSolicitud; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaSolicitudDTP" <?php echo ( !$formularioEditable || isset($servicio->id) ) ? ' disabled' : ''; ?>>
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
						<input type="text" name="maquinariaUbicacionDescripcion" id="maquinariaUbicacionDescripcion" value="<?php echo fString($maquinariaUbicacionDescripcion); ?>" class="form-control form-control-sm text-uppercase" readonly>
					</div>

				</div>

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
							<input type="text" name="fechaFinalizacion" id="fechaFinalizacion" value="<?php echo $fechaFinalizacion; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de solicitud" data-target="#fechaFinalizacionDTP" <?php echo !$formularioEditable ? ' disabled' : ' disabled'; ?>>
							<div class="input-group-append" data-target="#fechaFinalizacionDTP" data-toggle="datetimepicker">
	                            <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
	                        </div>
						</div>
					</div>
					<?php endif; ?>

				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
