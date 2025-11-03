<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">

					<div class="col-md-12 form-group">

						<label for="empresaId">Empresa:</label>
						<!-- <select name="empresaId" id="empresaId" class="custom-select form-controls select2" style="width: 100%"> -->
						<select name="empresaId" id="empresaId" class="custom-select form-controls select2">
						<?php if ( isset($maquinaria->id) ) : ?>
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
						<label for="numeroEconomico">Número Económico:</label>
						<input type="text" name="numeroEconomico" value="<?php echo fString($numeroEconomico); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número económico">
					</div>

					<div class="col-md-6 form-group">
						<label for="numeroFactura">Número de Factura:</label>
						<input type="text" name="numeroFactura" value="<?php echo fString($numeroFactura); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número o folio de Factura">
					</div>

				</div>

				<div class="row">

					<!-- <div class="form-group"> -->
					<div class="col-md-6 form-group">
						<label for="maquinariaTipoId">Tipo de Maquinaria:</label>

						<div class="input-group">

							<!-- <select name="maquinariaTipoId" id="maquinariaTipoId" class="custom-select form-controls xselect2Add" style="width: 100%"> -->
							<select name="maquinariaTipoId" id="maquinariaTipoId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="maquinariaTipoId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
								<option value="">Selecciona un Tipo de Maquinaria</option>
							<?php endif; ?>
								<?php foreach($maquinariaTipos as $maquinariaTipo) { ?>
								<option value="<?php echo $maquinariaTipo["id"]; ?>"
									<?php echo $maquinariaTipoId == $maquinariaTipo["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($maquinariaTipo["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddMaquinariaTipoId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>
					</div>

					<div class="col-md-6 form-group">
						<label for="marcaId">Marca:</label>

						<div class="input-group">

							<select name="marcaId" id="marcaId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="marcaId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
							<!-- <select name="marcaId" id="marcaId" class="form-control select2Add" style="width: 100%"> -->
								<option value="">Selecciona una Marca</option>
							<?php endif; ?>
								<?php foreach($marcas as $marca) { ?>
								<option value="<?php echo $marca["id"]; ?>"
									<?php echo $marcaId == $marca["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($marca["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddMarcaId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="modeloId">Modelo:</label>

						<div class="input-group">

							<select name="modeloId" id="modeloId" class="custom-select form-controls select2Add">
							<?php if ( !isset($maquinaria->id) ) : ?>
								<option value="">Selecciona un Modelo</option>
							<?php else: ?>
							<?php endif; ?>
								<?php foreach($modelos as $modelo) { ?>

								<!-- $maquinaria->modelo["marcaId"] == $modelo["marcaId"] -->

								<?php if ( $marcaId == $modelo["marcaId"] ) : ?>

								<option value="<?php echo $modelo["id"]; ?>"
									<?php echo $modeloId == $modelo["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($modelo["descripcion"])); ?>
								</option>

								<?php endif; ?>

								<?php } ?>
							
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddModeloId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>
					</div>
	
					<div class="col-md-6 form-group">
						<label for="year">Año:</label>
						<input type="text" name="year" value="<?php echo fString($year); ?>" class="form-control form-control-sm campoSinDecimal" placeholder="Ingresa el año de la Maquinaria" maxlength="4">
					</div>

				</div>

				<div class="row">
					
					<div class="col-md-12 form-group">
						<label for="descripcion">Descripción:</label>
						<input type="text" name="descripcion" value="<?php echo fString($descripcion); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la descripción">
					</div>

				</div>

				<div class="row">
					
					<div class="col-md-12 form-group">
						<label for="serie">Serie:</label>
						<input type="text" name="serie" value="<?php echo fString($serie); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la serie de la Maquinaria">
					</div>

				</div>

				<div class="row">

					<div class="col-md-6 form-group">
						<label for="colorId">Color:</label>

						<div class="input-group">

							<select name="colorId" id="colorId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="colorId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
							<?php endif; ?>
								<option value="">Selecciona un Color</option>
								<?php foreach($colores as $color) { ?>
								<option value="<?php echo $color["id"]; ?>"
									<?php echo $colorId == $color["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($color["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddColorId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>

					</div>

					<div class="col-md-6 form-group">
						<label for="estatusId">Estatus:</label>

						<div class="input-group">

							<select name="estatusId" id="estatusId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="estatusId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
								<option value="">Selecciona un Estatus</option>
							<?php endif; ?>
								<?php foreach($estatus as $status) { ?>
								<option value="<?php echo $status["id"]; ?>"
									<?php echo $estatusId == $status["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($status["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddEstatusId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>

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
						<label for="ubicacionId">Ubicación:</label>

						<div class="input-group">

							<select name="ubicacionId" id="ubicacionId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="ubicacionId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
								<option value="">Selecciona una Ubicación</option>
							<?php endif; ?>
								<?php foreach($ubicaciones as $ubicacion) { ?>
								<option value="<?php echo $ubicacion["id"]; ?>"
									<?php echo $ubicacionId == $ubicacion["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddUbicacionId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>

					</div>

					<div class="col-md-6 form-group">
						<label for="almacenId">Almacén:</label>

						<div class="input-group">

							<select name="almacenId" id="almacenId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="almacenId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
								<option value="">Selecciona un Almacén</option>
							<?php endif; ?>
								<?php foreach($almacenes as $almacen) { ?>
								<option value="<?php echo $almacen["id"]; ?>"
									<?php echo $almacenId == $almacen["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($almacen["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

							<div class="input-group-append">
								<button type="button" id="btnAddAlmacenId" class="btn btn-sm btn-success" disabled>
									<i class="fas fa-plus-circle"></i>
								</button>
							</div>

						</div>

					</div>

					<div class="col-md-6 form-group">
						<label for="obraId">Obra:</label>

							<select name="obraId" id="obraId" class="custom-select form-controls select2Add">
							<?php if ( isset($maquinaria->id) ) : ?>
							<!-- <select id="ubicacionId" class="form-control select2" style="width: 100%" disabled> -->
							<?php else: ?>
								<option value="">Selecciona una Obra</option>
							<?php endif; ?>
								<?php foreach($obras as $obra) { ?>
								<option value="<?php echo $obra["id"]; ?>"
									<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
									><?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
								</option>
								<?php } ?>
							</select>

					</div>
	
				</div>				

				<div class="form-group">
					<label for="observaciones">Observaciones:</label>
					<textarea name="observaciones" id="editor" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa las características del Producto"><?php echo fString($observaciones); ?></textarea>
				</div>

				<?php if ( isset($maquinaria->id) ) : ?>
				<div class="form-group">
					<picture>
						<img src="<?= $ruta; ?>" id="imgFoto" class="img-fluid img-thumbnail previsualizar" style="width: 50%" alt="QR">
					</picture>
				</div>
				<?php endif; ?>
			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

	<input type="checkbox" class="d-none" id ="fugas" name="fugas" <?php echo $fugas == 1 ? 'checked' : ''; ?>>
	<input type="checkbox" class="d-none" id="transmision" name="transmision" <?php echo $transmision == 1 ? 'checked' : ''; ?>>
	<input type="checkbox" class="d-none" id="sistema" name="sistema" <?php echo $sistema == 1 ? 'checked' : ''; ?>>
	<input type="checkbox" class="d-none" id="motor" name="motor" <?php echo $motor == 1 ? 'checked' : ''; ?>>
	<input type="checkbox" class="d-none" id="pintura" name="pintura" <?php echo $pintura == 1 ? 'checked' : ''; ?>>
	<input type="checkbox" class="d-none" id="seguridad" name="seguridad" <?php echo $seguridad == 1 ? 'checked' : ''; ?>>


</div> <!-- <div class="row"> -->
