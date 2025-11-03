<?php
	if ( isset($servicioEstatus->id) ) {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : $servicioEstatus->descripcion;
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : $servicioEstatus->nombreCorto;
		$colorTexto = isset($old["colorTexto"]) ? $old["colorTexto"] : $servicioEstatus->colorTexto;
		$colorFondo = isset($old["colorFondo"]) ? $old["colorFondo"] : $servicioEstatus->colorFondo;
		$servicioAbierto = isset($old["descripcion"]) ? ( isset($old["servicioAbierto"]) && $old["servicioAbierto"] == "on" ? true : false ) : $servicioEstatus->servicioAbierto;
		$servicioCerrado = isset($old["descripcion"]) ? ( isset($old["servicioCerrado"]) && $old["servicioCerrado"] == "on" ? true : false ) : $servicioEstatus->servicioCerrado;
		$requisicionAbierta = isset($old["descripcion"]) ? ( isset($old["requisicionAbierta"]) && $old["requisicionAbierta"] == "on" ? true : false ) : $servicioEstatus->requisicionAbierta;
		$requisicionCerrada = isset($old["descripcion"]) ? ( isset($old["requisicionCerrada"]) && $old["requisicionCerrada"] == "on" ? true : false ) : $servicioEstatus->requisicionCerrada;
		$requisicionOrden = isset($old["requisicionOrden"]) ? $old["requisicionOrden"] : $servicioEstatus->requisicionOrden;
		$requisicionAgregarPartidas = isset($old["descripcion"]) ? ( isset($old["requisicionAgregarPartidas"]) && $old["requisicionAgregarPartidas"] == "on" ? true : false ) : $servicioEstatus->requisicionAgregarPartidas;
		$requisicionUsuarioCreacion = isset($old["descripcion"]) ? ( isset($old["requisicionUsuarioCreacion"]) && $old["requisicionUsuarioCreacion"] == "on" ? true : false ) : $servicioEstatus->requisicionUsuarioCreacion;
		$ordenCompraAbierta = isset($old["ordenCompraAbierta"]) && $old["ordenCompraAbierta"] == "on" ? true :  $servicioEstatus->ordenCompraAbierta;
		$ordenCompraCerrada = isset($old["ordenCompraCerrada"]) && $old["ordenCompraCerrada"] == "on" ? true : $servicioEstatus->ordenCompraCerrada;
	} else {
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";
		$nombreCorto = isset($old["nombreCorto"]) ? $old["nombreCorto"] : "";
		$colorTexto = isset($old["colorTexto"]) ? $old["colorTexto"] : "";
		$colorFondo = isset($old["colorFondo"]) ? $old["colorFondo"] : "";
		$servicioAbierto = isset($old["servicioAbierto"]) && $old["servicioAbierto"] == "on" ? true : false;
		$servicioCerrado = isset($old["servicioCerrado"]) && $old["servicioCerrado"] == "on" ? true : false;
		$requisicionAbierta = isset($old["requisicionAbierta"]) && $old["requisicionAbierta"] == "on" ? true : false;
		$requisicionCerrada = isset($old["requisicionCerrada"]) && $old["requisicionCerrada"] == "on" ? true : false;
		$requisicionOrden = isset($old["requisicionOrden"]) ? $old["requisicionOrden"] : "0";
		$requisicionAgregarPartidas = isset($old["requisicionAgregarPartidas"]) && $old["requisicionAgregarPartidas"] == "on" ? true : false;
		$requisicionUsuarioCreacion = isset($old["requisicionUsuarioCreacion"]) && $old["requisicionUsuarioCreacion"] == "on" ? true : false;
		$ordenCompraAbierta = isset($old["ordenCompraAbierta"]) && $old["ordenCompraAbierta"] == "on" ? true : false;
		$ordenCompraCerrada = isset($old["ordenCompraCerrada"]) && $old["ordenCompraCerrada"] == "on" ? true : false;
	}
	$colorEstilos = '';
	if ( !is_null($colorTexto) && $colorTexto != '' ) $colorEstilos .= "color: {$colorTexto};";
	if ( !is_null($colorFondo) && $colorFondo != '' ) $colorEstilos .= "background-color: {$colorFondo};";
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="form-group">
	<label for="descripcion">Descripción:</label>
	<input type="text" id="descripcion" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción del estatus" style="<?php echo $colorEstilos; ?>">
</div>

<div class="form-group">
	<label for="nombreCorto">Nombre Corto:</label>
	<input type="text" id="nombreCorto" name="nombreCorto" value="<?php echo fString($nombreCorto); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre corto">
</div>

<div class="row">

	<div class="col-md-6 form-group">
		<label for="colorTexto">Color Texto:</label>
		<div class="input-group input-group-sm my-colorpicker2">
			<input type="text" id="colorTexto" name="colorTexto" value="<?php echo fString($colorTexto); ?>" class="form-control text-uppercase" placeholder="selecciona un color">
			<div class="input-group-append">
				<?php if ( is_null($colorTexto) || $colorTexto == '' ) : ?>
				<span class="input-group-text"><i class="fas fa-square"></i></span>
				<?php else: ?>
				<span class="input-group-text"><i class="fas fa-square" style="color: <?php echo fString($colorTexto); ?>;"></i></span>
				<?php endif; ?>
			</div>
		</div>
	</div>

	<div class="col-md-6 form-group">
		<label for="colorFondo">Color Fondo:</label>
		<div class="input-group input-group-sm my-colorpicker2">
			<input type="text" id="colorFondo" name="colorFondo" value="<?php echo fString($colorFondo); ?>" class="form-control text-uppercase" placeholder="selecciona un color">
			<div class="input-group-append">
				<?php if ( is_null($colorFondo) || $colorFondo == '' ) : ?>
				<span class="input-group-text"><i class="fas fa-square"></i></span>
				<?php else: ?>
				<span class="input-group-text"><i class="fas fa-square" style="color: <?php echo fString($colorFondo); ?>;"></i></span>
				<?php endif; ?>
			</div>
		</div>
	</div>

</div>

<hr>

<div class="row">

	<div class="col-md-6 form-group">
		<label for="servicioAbierto">Servicio:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Abierto" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="servicioAbierto" id="servicioAbierto" <?php echo $servicioAbierto ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6 form-group">
		<label for="servicioCerrado">Servicio:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Cerrado" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="servicioCerrado" id="servicioCerrado" <?php echo $servicioCerrado ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

</div>

<hr class="mt-0">

<div class="row">

	<div class="col-md-6 form-group">
		<label for="requisicionAbierta">Requisicion:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Abierta" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="requisicionAbierta" id="requisicionAbierta" <?php echo $requisicionAbierta ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6 form-group">
		<label for="requisicionCerrada">Requisicion:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Cerrada" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="requisicionCerrada" id="requisicionCerrada" <?php echo $requisicionCerrada ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="row">

	<div class="col-md-6 form-group">
		<label for="ordenCompraAbierta">Orden de Compra:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Abierta" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="ordenCompraAbierta" id="ordenCompraAbierta" <?php echo $ordenCompraAbierta ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6 form-group">
		<label for="ordenCompraCerrada">Orden de Compra:</label>
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Cerrada" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="ordenCompraCerrada" id="ordenCompraCerrada" <?php echo $ordenCompraCerrada ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="row">
	<div class="col form-group">
		<label for="requisicionOrden">Orden:</label>
		<input type="text" id="requisicionOrden" name="requisicionOrden" value="<?php echo fString($requisicionOrden); ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el Orden">
	</div>
</div>

<div class="row">

	<!-- <div class="col-md-6 form-group"> -->
	<div class="col form-group">
		<label for="requisicionAgregarPartidas">Permitir:</label>
		<!-- <label>Permitir:</label> -->
		<div class="input-group">
			<input type="text" class="form-control form-control-sm" value="Agregar/Eliminar partidas" readonly>
			<div class="input-group-append">
				<div class="input-group-text">
					<input type="checkbox" name="requisicionAgregarPartidas" id="requisicionAgregarPartidas" <?php echo $requisicionAgregarPartidas ? "checked" : ""; ?>>
				</div>
			</div>
		</div>
	</div>

</div>

<div class="form-group">
	<label for="requisicionUsuarioCreacion">Permitir solo al usuario que crea la Requisición:</label>
	<div class="input-group">
		<input type="text" class="form-control form-control-sm" value="Ver estatus" readonly>
		<div class="input-group-append">
			<div class="input-group-text">
				<input type="checkbox" name="requisicionUsuarioCreacion" id="requisicionUsuarioCreacion" <?php echo $requisicionUsuarioCreacion ? "checked" : ""; ?>>
			</div>
		</div>
	</div>
</div>
