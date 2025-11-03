<?php
	use App\Route;
	// if ( isset($old) ) var_dump($old);
	if ( isset($actividad->id) ) {
		// var_dump($actividad);
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $actividad->empresaId;
		$folio = isset($old["folio"]) ? $old["folio"] : $actividad->folio;
		$empleadoId = isset($old["empleadoId"]) ? $old["empleadoId"] : $actividad->empleadoId;
		$fechaInicial = isset($old["fechaInicial"]) ? $old["fechaInicial"] : fFechaLarga($actividad->fechaInicial);
		$fechaFinal = isset($old["fechaFinal"]) ? $old["fechaFinal"] : fFechaLarga($actividad->fechaFinal);

		// $fecha = '';
		$horasTrabajadas = '0.00';
		// $servicioId = '';
		// $avance = '';
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$folio = isset($old["folio"]) ? $old["folio"] : "";
		$empleadoId = isset($old["empleadoId"]) ? $old["empleadoId"] : "";
		$fechaInicial = isset($old["fechaInicial"]) ? $old["fechaInicial"] : "";
		$fechaFinal = "";
		if ( $fechaInicial != "" ) { 
			$fechaFinal = date('Y-m-d', strtotime(fFechaSQL($fechaInicial).' + 6 days'));
			$fechaFinal = fFechaLarga($fechaFinal);
		}

		// $fecha = '';
		$horasTrabajadas = '0.00';
		// $servicioId = '';
		// $avance = '';
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<!-- <div class="row"> -->

					<!-- <div class="col-md-12 form-group"> -->
					<div class="form-group">

						<label for="empresaId">Empresa:</label>
						<?php if ( isset($actividad->id) ) : ?>
						<!-- <select id="empresaId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
						<select id="empresaId" class="custom-select form-controls select2" disabled>
						<?php else: ?>
						<select name="empresaId" id="empresaId" class="custom-select form-controls select2">
							<option value="">Selecciona una Empresa</option>
						<?php endif; ?>
							<?php foreach($empresas as $empresa) { ?>
							<option value="<?php echo $empresa["id"]; ?>"
								<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
							</option>
							<?php } ?>
						</select>
						<div class="mt-1" id="msgConsultarOrdenesTrabajo"></div>

					</div>

				<!-- </div> -->

				<div class="row">

					<div class="col-sm-6 form-group">
						<label for="folio">Folio:</label>
						<input type="text" id="folio" value="<?php echo fString($folio); ?>" class="form-control form-control-sm" placeholder="Asignado por sistema" disabled>
					</div>

				</div>

				<!-- <div class="row"> -->

					<!-- <div class="col-12 form-group"> -->
					<div class="form-group">

						<label for="empleadoId">Empleado:</label>
						<?php if ( isset($actividad->id) ) : ?>
						<!-- <select id="empleadoId" class="form-control select2" style="width: 100%" disabled> -->
						<select id="empleadoId" class="custom-select form-controls select2" disabled>
						<?php else: ?>
						<select name="empleadoId" id="empleadoId" class="custom-select form-controls select2">
							<option value="">Selecciona un Empleado</option>
						<?php endif; ?>
							<?php foreach($empleados as $empleado) { ?>
							<?php if ( isset($actividad->id) || in_array($empleadoFuncion->id, json_decode($empleado["funciones"])) ) : ?>
							<option value="<?php echo $empleado["id"]; ?>"
								<?php echo $empleadoId == $empleado["id"] ? ' selected' : ''; ?>
								><?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
							</option>
							<?php endif; ?>
							<?php } ?>
						</select>

					</div>

				<!-- </div> -->

				<div class="row">

					<div class="col-sm-6 form-group">
						<label for="fechaInicial">Fecha Inicial:</label>
						<div class="input-group date" id="fechaInicialDTP" data-target-input="nearest">
							<input type="text" name="fechaInicial" id="fechaInicial" value="<?php echo $fechaInicial; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha inicial" data-target="#fechaInicialDTP" <?php echo ( !$formularioEditable || isset($actividad->id) ) ? ' disabled' : ''; ?>>
							<div class="input-group-append" data-target="#fechaInicialDTP" data-toggle="datetimepicker">
				                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
				            </div>
						</div>
					</div>

					<div class="col-sm-6 form-group">
						<label for="fechaFinal">Fecha Final:</label>
						<div class="input-group date" id="fechaFinalDTP" data-target-input="nearest">
							<input type="text" name="fechaFinal" id="fechaFinal" value="<?php echo $fechaFinal; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Asignada por sistema" data-target="#fechaFinalDTP" disabled>
							<div class="input-group-append" data-target="#fechaFinalDTP" data-toggle="datetimepicker">
				                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
				            </div>
						</div>
					</div>
				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">

				<div class="row">

					<div class="col-sm-6 form-group">
						<label for="fechaActividad">Fecha:</label>
						<div class="input-group date" id="fechaActividadDTP" data-target-input="nearest">
							<input type="text" id="fechaActividad" value="" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaActividadDTP">
							<div class="input-group-append" data-target="#fechaActividadDTP" data-toggle="datetimepicker">
				                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
				            </div>
						</div>
					</div>

					<div class="col-sm-6 form-group">
						<label for="horasActividad">Horas Trabajadas:</label>
						<input type="text" id="horasActividad" value="<?php echo fString($horasTrabajadas); ?>" class="form-control form-control-sm text-right campoConDecimal" placeholder="Ingresa las horas trabajadas">
					</div>

				</div>

				<div class="form-group">

					<label for="servicioId">Orden de Trabajo:</label>
					<select id="servicioId" class="custom-select form-controls select2">
						<option value="">Selecciona una Orden de Trabajo</option>
						<?php if ( isset($actividad->id) ) : ?>
						<?php foreach($servicios as $servicio) { ?>
						<option value="<?php echo $servicio["id"]; ?>" folio="<?php echo mb_strtoupper(fString($servicio["folio"])) ?>">
							<?php echo mb_strtoupper(fString($servicio["folio"])) . " [ " . mb_strtoupper(fString($servicio["maquinarias.numeroEconomico"])) . " ] "; ?>
						</option>
						<?php } ?>
						<?php endif; ?>
					</select>

				</div>

				<div class="form-group">
					<label for="avanceActividad">Avance de Reparación:</label>
					<textarea id="avanceActividad" class="form-control form-control-sm text-uppercase" rows="1" placeholder="Ingresa el avance de la reparación"></textarea>
				</div>

				<button type="button" id="btnAgregarActividad" class="btn btn-primary float-right">
					<i class="fas fa-plus"></i> Agregar actividad
				</button>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-warning card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->

<!-- <hr class="my-2"> -->

<div class="card card-info card-outline">

	<div class="card-body">

		<div class="table-responsive">

			<table class="table table-sm table-bordered table-striped" id="tablaActividadDetalles" width="100%">

				<thead>
					<tr>
						<th class="text-center" style="min-width: 120px;">Fecha</th>
						<th style="min-width: 100px;">Orden de Trabajo</th>
						<th style="min-width: 200px;">Avance de la Reparación</th>
						<th class="text-right" style="min-width: 80px;">Horas Trabajadas</th>
					</tr>
				</thead>

				<!-- <tbody class="text-uppercase"> -->
				<tbody>
					<?php if ( isset($actividad->id) ) : ?>
					<?php foreach($actividad->detalles as $key=>$detalle) : ?>
						<tr>
							<td class="text-center"><?php echo fFechaLarga($detalle['fecha']); ?></td>
							<td class="text-uppercase"><?php echo fString($detalle['servicios.folio']); ?></td>
							<td class="text-uppercase"><?php echo fString($detalle['descripcion']); ?></td>
							<td class="text-right"><?php echo number_format($detalle['horas'], 2); ?></td>
						</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>

			</table>

		</div> <!-- <div class="table-responsive"> -->

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->
