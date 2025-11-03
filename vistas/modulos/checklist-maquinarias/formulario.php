<?php
	if ( isset($checklistMaquinarias->id) ) {
		$obraId = isset($old["obraId"]) ? $old["obraId"] : $checklistMaquinarias->obraId;
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : $checklistMaquinarias->ubicacionId;
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : $checklistMaquinarias->maquinariaId;
		$fecha = isset($old["fecha"]) ? $old["fecha"] : $checklistMaquinarias->fecha;
		$horometroInicial = isset($old["horometroInicial"]) ? $old["horometroInicial"] : $checklistMaquinarias->horometroInicial;
		$horometroFinal = isset($old["horometroFinal"]) ? $old["horometroFinal"] : $checklistMaquinarias->horometroFinal;
		$observaciones = isset($old["observaciones"]) ? $old["observaciones"] : $checklistMaquinarias->observaciones;
		$combustibleInicial = isset($old["combustibleInicial"]) ? $old["combustibleInicial"] : $checklistMaquinarias->combustibleInicial;
		$combustibleFinal = isset($old["combustibleFinal"]) ? $old["combustibleFinal"] : $checklistMaquinarias->combustibleFinal;
		$acMotor = isset($old["acMotor"]) ? $old["acMotor"] : $checklistMaquinarias->acMotor;
		$acHidraulico = isset($old["acHidraulico"]) ? $old["acHidraulico"] : $checklistMaquinarias->acHidraulico;
		$acTransmision = isset($old["acTransmision"]) ? $old["acTransmision"] : $checklistMaquinarias->acTransmision;
		$anticongelante = isset($old["anticongelante"]) ? $old["anticongelante"] : $checklistMaquinarias->anticongelante;
		$acMalacatePrinc = isset($old["acMalacatePrinc"]) ? $old["acMalacatePrinc"] : $checklistMaquinarias->acMalacatePrinc;
		$acMalacateAux = isset($old["acMalacateAux"]) ? $old["acMalacateAux"] : $checklistMaquinarias->acMalacateAux;

	} else {
		$obraId = isset($old["obraId"]) ? $old["obraId"] : "";
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : "";
		// $maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : "";
		$fecha = isset($old["fecha"]) ? $old["fecha"] : "";
		$horometroInicial = isset($old["horometroInicial"]) ? $old["horometroInicial"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">
<div class="col-md-6">

	<div class="form-group">
		<label for="obraId">Obra:</label>
		<select name="obraId" id="obraId" class="custom-select form-controls select2">
			<option value="">Selecciona Obra</option>
			<?php foreach($obras as $obra) { ?>
				<option value="<?php echo $obra["id"]; ?>"
					<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
					><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
				</option>
			<?php } ?>
		</select>
	</div>

	<div class="form-group">
		<label for="ubicacionId">Ubicacion:</label>
		<select name="ubicacionId" id="ubicacionId" class="custom-select form-controls select2">
			<option value="">Selecciona Ubicacion</option>
			<?php foreach($ubicaciones as $ubicacion) { ?>
				<option value="<?php echo $ubicacion["id"]; ?>"
					<?php echo $ubicacionId == $ubicacion["id"] ? ' selected' : ''; ?>
					><?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
				</option>
			<?php } ?>
		</select>
	</div>

	<div class="form-group">
		<label for="maquinariaId">Número Económico:</label>
		<select name="maquinariaId" id="maquinariaId" class="custom-select form-controls select2">
			<option value="">Selecciona un Número Económico</option>
			<?php foreach($maquinarias as $maquinaria) { ?>
			<option value="<?php echo $maquinaria["id"]; ?>"
				<?php echo $maquinariaId == $maquinaria["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>

	<div class="form-group">
		<label for="fecha">Fecha de Realizacion:</label>
		<div class="input-group date" id="fechaDTP" data-target-input="nearest">
			<input type="text" name="fecha" id="fecha" value="<?php echo $fecha; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha" data-target="#fechaDTP">
			<div class="input-group-append" data-target="#fechaDTP" data-toggle="datetimepicker">
				<div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<label for="horometroInicial">Horómetro Inicial:</label>
		<input type="text" step="any" name="horometroInicial" id="horometroInicial" class="form-control form-control-sm campoConDecimal" value="<?php echo $horometroInicial; ?>" placeholder="Ingresa el horómetro inicial">
	</div>

	<?php if ( isset($checklistMaquinarias->id) ) { ?>
		
		<div class="form-group">
			<label for="horometroFinal">Horómetro Final:</label>
			<input type="text" step="any" name="horometroFinal" id="horometroFinal" class="form-control form-control-sm campoConDecimal" value="<?php echo $horometroFinal; ?>" placeholder="Ingresa el horómetro final">
		</div>

		<div class="form-group">
			<label for="observaciones">Observaciones:</label>
			<textarea name="observaciones" id="observaciones" class="form-control form-control-sm" rows="3" placeholder="Ingresa observaciones"><?php echo $observaciones; ?></textarea>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="combustibleInicial" class="form-label">Combustible Inicial:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $combustibleInicial; ?>" id="combustibleInicial" name="combustibleInicial" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="combInicialValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="combustibleFinal" class="form-label">Combustible Final:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $combustibleFinal ?>" id="combustibleFinal" name="combustibleFinal" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="combFinalValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="acMotor" class="form-label">Ac Motor:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $acMotor ?>" id="acMotor" name="acMotor" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="acMotorValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="acHidraulico" class="form-label">Ac Hidráulico:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $acHidraulico; ?>" id="acHidraulico" name="acHidraulico" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="acHidraulicoValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="acTransmision" class="form-label">Ac Transmisión:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $acTransmision; ?>" id="acTransmision" name="acTransmision" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="acTransmisionValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="anticongelante" class="form-label">Anticongelante:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $anticongelante; ?>" id="anticongelante" name="anticongelante" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="anticongelanteValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="acMalacatePrinc" class="form-label">Ac Malacate Princ:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $acMalacatePrinc; ?>" id="acMalacatePrinc" name="acMalacatePrinc" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="acMalacatePrincValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

		<div class="row col-md-8 form-group">
			<div class="col-6">
				<label for="acMalacateAux" class="form-label">Ac Malacate Aux:</label>
			</div>
			<div class="col-6">
				<input type="range" class="form-range" min="0" max="100" value="<?php echo $acMalacateAux; ?>" id="acMalacateAux" name="acMalacateAux" style="accent-color: #007bff;">
				<p class="text-center mt-3"><span id="acMalacateAuxValue" class="fw-bold">50 %</span></p>
			</div>
		</div>

	<?php } ?>
</div> <!-- /.row -->