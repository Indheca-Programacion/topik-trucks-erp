<?php
	if ( isset($servicioCentro->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $servicioCentro->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $servicioCentro->nombreCorto;
		$nomenclaturaOT = isset($old["nomenclaturaOT"]) ? $old["nomenclaturaOT"] : $servicioCentro->nomenclaturaOT;
	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$nomenclaturaOT = isset($old["nomenclaturaOT"]) ? $old["nomenclaturaOT"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del centro de servicio">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>

<div class="form-group">
	<label for="nomenclaturaOT">Nomenclatura OT:</label>
	<input type="text" id="nomenclaturaOT" name="nomenclaturaOT" value="<?php echo fString($nomenclaturaOT); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la nomenclatura para la OT">
</div>
