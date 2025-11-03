<?php
	if ( isset($traslado->id) ) {
		$operador = isset($old["operador"]) ? $old["operador"] : $traslado->operador;
		$ruta = isset($old["ruta"]) ? $old["ruta"] : $traslado->ruta;
		$fecha = isset($old["fecha"]) ? $old["fecha"] : $traslado->fecha;
		$kmInicial = isset($old["kmInicial"]) ? $old["kmInicial"] : $traslado->kmInicial;
		$kmFinal = isset($old["kmFinal"]) ? $old["kmFinal"] : $traslado->kmFinal;
		$kmRecorrido = isset($old["kmRecorrido"]) ? $old["kmRecorrido"] : $traslado->kmRecorrido;
		$combustibleInicial = isset($old["combustibleInicial"]) ? $old["combustibleInicial"] : $traslado->combustibleInicial;
		$combustibleFinal = isset($old["combustibleFinal"]) ? $old["combustibleFinal"] : $traslado->combustibleFinal;
		$combustibleGastado = isset($old["combustibleGastado"]) ? $old["combustibleGastado"] : $traslado->combustibleGastado;
		$rendimientoTeorico = isset($old["rendimientoTeorico"]) ? $old["rendimientoTeorico"] : $traslado->rendimientoTeorico;
		$rendimientoReal = isset($old["rendimientoReal"]) ? $old["rendimientoReal"] : $traslado->rendimientoReal;
		$maquinariaId = isset($old["maquinaria"]) ? $old["maquinaria"] : $traslado->maquinaria;
		$deposito = isset($old["deposito"]) ? $old["deposito"] : $traslado->deposito;
		$empresaId = isset($old["empresa"]) ? $old["empresa"] : $traslado->empresa;
		$estatus = isset($old["estatus"]) ? $old["estatus"] : $traslado->estatus;
		$folio = $servicio->folio;

		$tipoRequisicion = isset($requisicion->tipoRequisicion) ? $requisicion->tipoRequisicion : '0';
		$fechaRequerida = isset($requisicion->fechaRequerida) && !empty($requisicion->fechaRequerida)
			? fFechaLarga($requisicion->fechaRequerida)
			: fFechaLarga(date('Y-m-d'));

		$permitirEditar = false;
	} else {
		$operador = isset($old["operador"]) ? $old["operador"] : "";
		$ruta = isset($old["ruta"]) ? $old["ruta"] : "";
		$fecha = isset($old["fecha"]) ? $old["fecha"] : "";
		$kmInicial = isset($old["kmInicial"]) ? $old["kmInicial"] : 0;
		$kmFinal = isset($old["kmFinal"]) ? $old["kmFinal"] : 0;
		$kmRecorrido = isset($old["kmRecorrido"]) ? $old["kmRecorrido"] : 0;
		$combustibleInicial = isset($old["combustibleInicial"]) ? $old["combustibleInicial"] : 0;
		$combustibleFinal = isset($old["combustibleFinal"]) ? $old["combustibleFinal"] : 0;
		$combustibleGastado = isset($old["combustibleGastado"]) ? $old["combustibleGastado"] : 0;
		$rendimientoTeorico = isset($old["rendimientoTeorico"]) ? $old["rendimientoTeorico"] : 0;
		$rendimientoReal = isset($old["rendimientoReal"]) ? $old["rendimientoReal"] : 0;
		$maquinariaId = isset($old["maquinaria"]) ? $old["maquinaria"] : 0;
		$deposito = isset($old["deposito"]) ? $old["deposito"] : 0;
		$empresaId = isset($old["empresa"]) ? $old["empresa"] : 0;
		$estatus = isset($old["estatus"]) ? $old["estatus"] : 0;

		$tipoRequisicion = $requisicion->tipoRequisicion ?? '0';
		$fechaRequerida = isset($old["fechaRequerida"]) ? $old["fechaRequerida"] : fFechaLarga(date("Y-m-d"));

		$permitirEditar = true;
	}

	//TODO: Empresa por ahora solo sera la te ATC y se debe cambiar
	$empresaId = 3;
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="row ">
	<!-- Folio -->
	<?php if ( isset($traslado->id)) { ?>
		<div class="col-md-6 form-group">
			<label for="folio">Folio:</label>
			<input disabled type="text" id="folioTraslados" class="form-control form-control-sm text-uppercase" value="<?php echo $folio; ?>" readonly>
		</div>
	<?php } ?>
	<!-- Operador -->
	<div class="col-md-6 form-group">
		<label for="operador">Operador:</label>
		<select <?php if($permitirEditar) echo 'readonly' ?> id="operador"  name="operador" class="custom-select form-controls-sm select2">
			<option value="">Selecciona un operador</option>
			<?php foreach($empleados as $empleado) { ?>
				<option value="<?php echo $empleado["id"]; ?>" 
				<?php echo $operador == $empleado["id"] ? ' selected' : ''; ?>
				>
				<?php echo mb_strtoupper(fString($empleado["nombreCompleto"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<!-- Numero Economico -->
	<div class="col-md-6 form-group">
		<label for="maquinaria">Numero Económico:</label>
		<select <?php if($permitirEditar) echo 'readonly' ?> id="maquinaria"  name="maquinaria" class="custom-select form-controls-sm select2">
			<option value="">Selecciona una maquinaria</option>
			<?php foreach($maquinarias as $maquinaria) { ?>
				<option value="<?php echo $maquinaria["id"]; ?>" 
				<?php echo $maquinariaId == $maquinaria["id"] ? ' selected' : ''; ?>
				>
				<?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<!-- Estatus -->
	<?php if ( isset($traslado->id)) { ?>
		<div class="col-md-6 form-group">
			<label for="estatus">Estatus</label>
			<select name="estatus" id="estatus" class="custom-select form-control-sm select2">
				<?php
					if ($estatus == 0) {
						// Si es 0, solo mostrar la opción "Completado"
						echo '<option value="0" selected>Por Atender</option>';
						echo '<option value="2" >Completado</option>';
					} elseif ($estatus == 2) {
						// Si es 2, solo mostrar la opción "Revisado"
						echo '<option value="2" selected>Completado</option>';
						echo '<option value="1" >Revisado</option>';
					} else if ($estatus == 1) {
						// Si es 1, solo mostrar la opción "Revisado"
						echo '<option value="1" selected>Revisado</option>';
					} else {
						// Si no es 0 ni 1, mostrar ambas opciones sin selección
						echo '<option value="0">Por Atender</option>';
						echo '<option value="1">Revisado</option>';
						echo '<option value="2">Completado</option>';
					}
				?>
			</select>
		</div>
	<?php } ?>
	<!-- Empresa -->
	<div class="col-md-6 form-group d-none">
		<label for="empresa">Empresa:</label>
		<select <?php if($permitirEditar) echo 'readonly' ?> name="empresa" id="empresa">
			<option value="">Selecciona una empresa</option>
			<?php foreach($empresas as $empresa) { ?>
				<option value="<?php echo $empresa["id"]; ?>"
					<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>>
					<?php echo mb_strtoupper(fString($empresa["nombreCorto"])); ?>
				</option>
			<?php } ?>
		</select>
	</div>
</div>
<div class="row">
	<!-- Ruta -->
	<div class="col-12 form-group">
		<label for="ruta">Ruta:</label>
		<input type="text" id="ruta" name="ruta" class="form-control form-control-sm" value="<?php echo $ruta; ?>">
	</div>
	<!-- Fecha -->
	<div class="col-md-6 form-group">
		<label for="fecha">Fecha:</label>
		<div class="input-group date" id="fecha" data-target-input="nearest">
			<input type="text" name="fecha" id="fecha" value="<?= $fecha; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fecha">
			<div class="input-group-append" data-target="#fecha" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
			</div>
		</div>
	</div>
	<!-- KM Inicial -->
	<div class="col-md-6 form-group">
		<label for="kmInicial">KM Inicial:</label>
		<input type="text" id="kmInicial" name="kmInicial" class="form-control form-control-sm" value="<?php echo $kmInicial; ?>">
	</div>
	<!-- KM Final -->
	<div class="col-md-6 form-group">
		<label for="kmFinal">KM Final:</label>
		<input type="text" id="kmFinal" name="kmFinal" class="form-control form-control-sm" value="<?php echo $kmFinal; ?>">
	</div>
	<!-- KM Recorrido -->
	<div class="col-md-6 form-group">
		<label for="kmRecorrido">KM Recorrido:</label>
		<input type="text" id="kmRecorrido" name="kmRecorrido" class="form-control form-control-sm" value="<?php echo $kmRecorrido; ?>">
	</div>
	<!-- Combustible inicial -->
	<div class="col-md-6 form-group">
		<label for="combustibleInicial">Combustible Inicial:</label>
		<input type="text" id="combustibleInicial" name="combustibleInicial" class="form-control form-control-sm" value="<?php echo $combustibleInicial; ?>">
	</div>
	<!-- Combustible final -->
	<div class="col-md-6 form-group">
		<label for="combustibleFinal">Combustible Final:</label>
		<input type="text" id="combustibleFinal" name="combustibleFinal" class="form-control form-control-sm" value="<?php echo $combustibleFinal; ?>">
	</div>
	<!-- Combustible gastado -->
	<div class="col-md-6 form-group">
		<label for="combustibleGastado">Combustible Gastado:</label>
		<input type="text" id="combustibleGastado" name="combustibleGastado" class="form-control form-control-sm" value="<?php echo $combustibleGastado; ?>">
	</div>
	<!-- Rendimiento Teorico -->
	<div class="col-md-6 form-group">
		<label for="rendimientoTeorico">Rendimiento Teorico:</label>
		<input type="text" id="rendimientoTeorico" name="rendimientoTeorico" class="form-control form-control-sm" value="<?php echo $rendimientoTeorico; ?>">
	</div>
	<!-- Rendimiento Real -->
	<div class="col-md-6 form-group">
		<label for="rendimientoReal">Rendimiento Real:</label>
		<input type="text" id="rendimientoReal" name="rendimientoReal" class="form-control form-control-sm" value="<?php echo $rendimientoReal; ?>">
	</div>
	<!-- Deposito -->
	<div class="col-md-6 form-group">
		<label for="deposito">Deposito:</label>
		<input type="text" id="deposito" name="deposito" class="form-control form-control-sm" value="<?php echo $deposito; ?>">
	</div>
</div>