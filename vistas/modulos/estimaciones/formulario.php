<?php
	if ( isset($generador->id) ) {

		$obra = isset($generador->obra) ? $generador->obra : '';
		$ubicacionId = isset($generador->ubicacionId) ? $generador->ubicacionId : '';
		$obraId = isset($generador->obraId) ? $generador->obraId : '';
		$mes = isset($generador->mes) ? $generador->mes : '';
		$empresaId = isset($generador->empresaId) ? $generador->empresaId : '';
		// Obtener el año y el mes desde el string de fecha
		list($año, $month) = explode("-", $mes);
		$folio = isset($generador->folio) ? $generador->folio : '';
		// Calcular el número de días en el mes dado
		$numDias = cal_days_in_month(CAL_GREGORIAN, $month, $año);
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : '';
		$generadorId = isset($old["generadorId"]) ? $old["generadorId"] : '';
		$obra = isset($old["obra"]) ? $old["obra"] : "";
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : "";
		$obraId = isset($old["obraId"]) ? $old["obraId"] : "";
		$mes = isset($old["mes"]) ? $old["mes"] : "";
		// list($year, $month, $day) = explode('-', $generador->mes);
		// $monthName = fNombreMes($month);
	}
?>

<input type="hidden" name="_token" id="token" value="<?php echo createToken(); ?>">
<input type="hidden" id="generadorId" name="generadorId" value="<?= $generador->id ?>">
<div class="row">
	<!-- Folio -->
	<?php if (isset($generador->id)) : ?>
	<div class="col-md-6 form-group">
		<label>Folio:</label>
		<input type="text" value="<?= $folio ;?>" class="form-control form-control-sm text-uppercase" <?php if(isset($generador->id))echo 'readonly' ?>>
	</div>
	<?php endif ?>
	<!-- Obra -->
	<div class="col-md-6 form-group">

		<label>Obra:</label>
		<!-- <input type="text" value="<?= $obra ;?>" class="form-control form-control-sm text-uppercase" <?php if(isset($generador->id))echo 'readonly' ?>> -->
		<select class="custom-select form-controls select2"  <?php if(isset($generador->id)) echo 'disabled' ?>>
			<option value="">Selecciona una Obra</option>
			<?php foreach($obras as $obra) { ?>
			<option value="<?php echo $obra["id"]; ?>"
				<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<!-- Ubicacion -->
	<div class="col-md-6 form-group">
		<label>Ubicación:</label>
		<select class="custom-select form-controls select2"  <?php if(isset($generador->id))echo ' disabled' ?>>
			<option value="">Selecciona una Ubicación</option>
			<?php foreach($ubicaciones as $ubicacion) { ?>
				<option value="<?php echo $ubicacion["id"]; ?>" 
				<?php echo $ubicacionId == $ubicacion["id"] ? ' selected' : ''; ?>
				>
				<?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>

	</div>
	<!-- Mes -->
	<div class="col-md-6 form-group">

		<label>Mes:</label>
		<input type="month" value="<?= $mes ; ?>" class="form-control form-control-sm text-uppercase"  <?php if(isset($generador->id))echo 'readonly' ?>>
	
	</div>
	<!-- Empresa -->
	<div class="col-md-6 form-group">
		<label>Empresa:</label>
		<select class="custom-select form-controls select2"  <?php if(isset($generador->id)) echo 'disabled' ?>>
			<option value="">Selecciona una Empresa</option>
			<?php foreach($empresas as $empresa) { ?>
			<option value="<?php echo $empresa["id"]; ?>"
				<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($empresa["razonSocial"])); ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<!-- Copiar -->
	<?php if (!isset($generador->id)) : ?>
	<div class="col-md-6 form-group">
		<label>Copiar Generador:</label>
		<select class="custom-select form-controls select2">
		<option value="">Selecciona un Generador</option>
			<?php foreach($generadores as $generador) { ?>
			<option value="<?php echo $generador["id"]; ?>"
				<?php echo $generadorId == $generador["id"] ? ' selected' : ''; ?>
				><?php list($year, $month, $day) = explode('-', $generador["mes"]);
						$monthName = fNombreMes($month); echo mb_strtoupper(fString($generador["obra"])) ." [". $monthName ."-".$year. "]"; ?>
			</option>
			<?php } ?>
		</select>
	</div>
	<?php endif ?>
</div>