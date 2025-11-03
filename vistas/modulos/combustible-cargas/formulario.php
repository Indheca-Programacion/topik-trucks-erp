<?php
	use App\Route;
	if ( isset($combustible->id) ) {
		// var_dump($combustible);
		// die();
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $combustible->empresaId;
		$empleadoId = isset($old["empleadoId"]) ? $old["empleadoId"] : $combustible->empleadoId;
		$fecha = isset($old["fecha"]) ? $old["fecha"] : fFechaLarga($combustible->fecha);
		$hora = $combustible->hora;

		$horoOdometro = '0.0';
		$litros = '0.0';
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$empleadoId = isset($old["empleadoId"]) ? $old["empleadoId"] : "";
		$fecha = isset($old["fecha"]) ? $old["fecha"] : "";
		$hora = isset($old["hora"]) ? $old["hora"] : "";

		$horoOdometro = '0.0';
		$litros = '0.0';
	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="form-group">

					<label for="empresaId">Empresa:</label>
					<?php if ( isset($combustible->id) ) : ?>
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
					<div class="mt-1" id="msgConsultarMaquinarias"></div>

				</div>

				<div class="form-group">

					<label for="empleadoId">Empleado:</label>
					<?php if ( isset($combustible->id) ) : ?>
					<select id="empleadoId" class="custom-select form-controls select2" disabled>
					<?php else: ?>
					<select name="empleadoId" id="empleadoId" class="custom-select form-controls select2">
						<option value="">Selecciona un Empleado</option>
					<?php endif; ?>
						<?php foreach($empleados as $empleado) { ?>
						<?php if ( isset($combustible->id) || in_array($empleadoFuncion->id, json_decode($empleado["funciones"])) ) : ?>
						<option value="<?php echo $empleado["id"]; ?>"
							<?php echo $empleadoId == $empleado["id"] ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
						</option>
						<?php endif; ?>
						<?php } ?>
					</select>

				</div>

				<div class="row">

					<div class="col-sm-6 form-group">
						<label for="fecha">Fecha:</label>
						<div class="input-group date" id="fechaDTP" data-target-input="nearest">
							<input type="text" name="fecha" id="fecha" value="<?php echo $fecha; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaDTP" <?php echo ( !$formularioEditable || isset($combustible->id) ) ? ' disabled' : ''; ?>>
							<div class="input-group-append" data-target="#fechaDTP" data-toggle="datetimepicker">
				                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
				            </div>
						</div>
					</div>

					<div class="col-sm-6 form-group">
						<label for="hora">Hora:</label>
						<div class="input-group hour" id="horaDTP" data-target-input="nearest">
							<input type="text" name="hora" id="hora" value="<?php echo $hora; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la hora" data-target="#horaDTP" <?php echo ( !$formularioEditable || isset($combustible->id) ) ? ' disabled' : ''; ?>>
							<div class="input-group-append" data-target="#horaDTP" data-toggle="datetimepicker">
				                <div class="input-group-text"><i class="far fa-clock"></i></div>
				            </div>
						</div>
					</div>

				</div>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->

	<?php if ( isset($combustible->id) ) : ?>
	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-body">

				<div class="row">

					<div class="col-md-6 form-group">

						<label for="maquinariaId">Número Económico:</label>
						<select id="maquinariaId" class="custom-select form-controls select2">
							<option value="">Selecciona un Número Económico</option>
							<?php foreach($maquinarias as $maquinaria) { ?>
							<option value="<?php echo $maquinaria["id"]; ?>">
								<?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

					<div class="col-md-6 form-group">
						<label for="maquinariaSerie">Serie:</label>
						<input type="text" id="maquinariaSerie" class="form-control form-control-sm text-uppercase" readonly>
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="maquinariaTipoDescripcion">Tipo de Maquinaria:</label>
						<input type="text" id="maquinariaTipoDescripcion" class="form-control form-control-sm text-uppercase" readonly>
					</div>

					<!-- <div class="col-md-6 form-group">
						<label for="maquinariaUbicacionDescripcion">Ubicación:</label>
						<input type="text" id="maquinariaUbicacionDescripcion" class="form-control form-control-sm text-uppercase" readonly>
					</div> -->

					<div class="col-md-6 form-group">

						<input type="hidden" id="ubicacionActualId">
						<label for="ubicacionId">Ubicación:</label>
						<select id="ubicacionId" class="custom-select form-controls select2">
							<option value="">Selecciona una Ubicación</option>
							<?php foreach($ubicaciones as $ubicacion) { ?>
							<option value="<?php echo $ubicacion["id"]; ?>">
								<?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
							</option>
							<?php } ?>
						</select>

					</div>

				</div>

				<div class="form-group">

					<label for="operadorId">Operador:</label>
					<select id="operadorId" class="custom-select form-controls select2">
						<option value="">Selecciona un Operador</option>
						<?php foreach($empleados as $empleado) { ?>
						<?php if ( in_array($empleadoFuncion->id, json_decode($empleado["funciones"])) ) : ?>
						<option value="<?php echo $empleado["id"]; ?>">
							<?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
						</option>
						<?php endif; ?>
						<?php } ?>
					</select>

				</div>

				<div class="row">

					<div class="col-sm-6 form-group">
						<label for="horoOdometro">Horómetro / Odómetro:</label>
						<input type="text" id="horoOdometro" value="<?php echo fString($horoOdometro); ?>" class="form-control form-control-sm text-right campoConDecimal" decimales="1" placeholder="Ingresa el indicador">
					</div>

					<div class="col-sm-6 form-group">
						<label for="litros">Litros:</label>
						<input type="text" id="litros" value="<?php echo fString($litros); ?>" class="form-control form-control-sm text-right campoConDecimal" decimales="1" placeholder="Ingresa los litros">
					</div>

					<div class="col form-group">
						<label for="observaciones">Observaciones:</label>
						<textarea id="observaciones" class="form-control form-control-sm" rows="4" placeholder="Ingresa las observaciones"></textarea>
					</div>

				</div>

				<button type="button" id="btnAgregarCarga" class="btn btn-primary float-right">
					<i class="fas fa-plus"></i> Agregar carga
				</button>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-warning card-outline"> -->

	</div> <!-- <div class="col-md-6"> -->
	<?php endif; ?>

</div> <!-- <div class="row"> -->

<!-- <hr class="my-2"> -->

<?php if ( isset($combustible->id) ) : ?>
<div class="card card-info card-outline">

	<div class="card-body">

		<div class="table-responsive">

			<table class="table table-sm table-bordered table-striped" id="tablaCombustibleDetalles" width="100%">

				<thead>
					<tr>
						<th style="min-width: 100px;">Equipo</th>
						<th style="min-width: 100px;">Ubicación</th>
						<th style="min-width: 100px;">Operador</th>
						<th class="text-right" style="min-width: 80px;">Horómetro / Odómetro</th>
						<th class="text-right" style="min-width: 80px;">Litros</th>
						<th class="text-right" style="min-width: 80px;">Observaciones</th>
					</tr>
				</thead>

				<tbody>
					<?php if ( isset($combustible->id) ) : ?>
					<?php foreach($combustible->detalles as $key=>$detalle) : ?>
						<tr maquinariaId="<?php echo $detalle['maquinariaId']; ?>">
							<td class="text-uppercase">
								<span><?php echo fString($detalle['maquinarias.numeroEconomico']); ?></span>
								<i class="ml-1 mt-1 fas fa-lg fa-trash-alt text-danger float-right eliminarPartida" style="cursor: pointer;" detalleId="<?php echo $detalle['id']; ?>"></i>
							</td>
							<td class="text-uppercase"><?php echo fString($detalle['ubicaciones.descripcion']); ?></td>
							<td class="text-uppercase"><?php echo fString($detalle['empleados.nombreCompleto']); ?></td>
							<td class="text-right"><?php echo number_format($detalle['horoOdometro'], 1); ?></td>
							<td class="text-right"><?php echo number_format($detalle['litros'], 1); ?></td>
							<td class="text-right"><?php echo fString($detalle['observaciones']); ?></td>
						</tr>
					<?php endforeach; ?>
					<?php endif; ?>
				</tbody>

			</table>

		</div> <!-- <div class="table-responsive"> -->

	</div> <!-- <div class="card-body"> -->

</div> <!-- <div class="card card-info card-outline"> -->
<?php endif; ?>
