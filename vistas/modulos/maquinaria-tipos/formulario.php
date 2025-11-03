<?php
	if ( isset($maquinariaTipo->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $maquinariaTipo->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $maquinariaTipo->nombreCorto;
		$orden = isset($old["orden"]) ? $old["orden"] : $maquinariaTipo->orden;
	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$orden = isset($old["orden"]) ? $old["orden"] : "0";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del tipo de maquinaria">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>

<div class="form-group">
	<label for="orden">Orden:</label>
	<input type="text" id="orden" name="orden" value="<?php echo fString($orden); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el orden">
</div>
