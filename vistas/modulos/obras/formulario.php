<?php
	$estatuses = [
		["id" => 1, "descripcion" => "ABIERTO"],
		["id" => 2, "descripcion" => "CERRADO"],
	];
	if ( isset($obra->id) ) {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $obra->empresaId;
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $obra->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $obra->nombreCorto;
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : $obra->estatusId;
		$periodos = isset($old["periodos"]) ? $old["periodos"] : $obra->periodos;
		$fechaInicio = isset($old["fechaInicio"]) ? $old["fechaInicio"] : fFechaLarga($obra->fechaInicio);
		$almacen = isset($old["almacen"]) ? $old["almacen"] : $obra->almacen;
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$estatusId = isset($old["estatusId"]) ? $old["estatusId"] : "";
		$fechaInicio = isset($old["fechaInicio"]) ? $old["fechaInicio"] : fFechaLarga(date("Y-m-d"));
		$periodos = isset($old["periodos"]) ? $old["periodos"] : "1";
		$almacen = isset($old["almacen"]) ? $old["almacen"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">

	<label for="empresaId">Empresa:</label>
	<select name="empresaId" id="empresaId" class="custom-select form-controls select2">
	<?php if ( isset($obra->id) ) : ?>
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

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción de la obra">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>

<div class="row">

	<div class="col-md-6 form-group">
		<label for="periodos">Semanas:</label>
		<input type="text" id="periodos" name="periodos" min=1 value="<?php echo fString($periodos); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingrese las Semanas">
	</div>

	<div class="col-md-6 form-group">
		<label for="fechaInicio">Fecha de Inicio:</label>
		<div class="input-group date" id="fechaInicioDTP" data-target-input="nearest">
			<input type="text" name="fechaInicio" id="fechaInicio" value="<?php echo $fechaInicio; ?>" class="form-control form-control-sm datetimepicker-input" placeholder="Ingresa la fecha de inicio" data-target="#fechaInicioDTP">
			<div class="input-group-append" data-target="#fechaInicioDTP" data-toggle="datetimepicker">
                <div class="input-group-text"><i class="fas fa-calendar-alt"></i></div>
            </div>
		</div>
	</div>

</div>

<div class="row">

	<div class="col-md-6 form-group">
		<label for="almacen">Almacén:</label>
		<input type="text" id="almacen" name="almacen" value="<?php echo fString($almacen); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el almacén">
	</div>

	<div class="col-md-6 form-group">
		<label for="estatusId">Estatus:</label>
		<select name="estatusId" id="estatusId" class="custom-select form-controls select2">
			<?php if ( isset($obra->id) ) : ?>
			<!-- <select id="estatusId" class="custom-select form-controls select2" style="width: 100%" disabled> -->
			<?php else: ?>
				<option value="">Selecciona un Estatus</option>
			<?php endif; ?>
			<?php foreach($estatuses as $estatus) { ?>
			<option value="<?php echo $estatus["id"]; ?>"
				<?php echo $estatusId == $estatus["id"] ? ' selected' : ''; ?>
				><?php echo mb_strtoupper(fString($estatus["descripcion"])); ?>
			</option>
			<?php } ?>
		</select>
</div>
