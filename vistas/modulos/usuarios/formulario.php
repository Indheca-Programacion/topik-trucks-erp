<?php
	if ( isset($usuario->id) ) {
		$usuarioField = $usuario->usuario;
		// $activo = isset($old["nombre"]) ? ( isset($old["activo"]) && $old["activo"] == "on" ? true : false ) : $usuario->activo;
		$contrasena = isset($old["contrasena"]) ? $old["contrasena"] : "";
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $usuario->nombre;
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : $usuario->apellidoPaterno;
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : $usuario->apellidoMaterno;
		$correo = isset($old["correo"]) ? $old["correo"] : $usuario->correo;
		$fotoAnterior = $usuario->foto;
		$foto = $usuario->foto;
		$firmaAnterior = $usuario->firma;
		$firma = $usuario->firma;
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : $usuario->empresaId;
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : $usuario->ubicacionId;
		$salario = isset($old["salario"]) ? $old["salario"] : ( isset($usuario->salario) ? $usuario->salario : "" );
		$costoManoObra = isset($old["costoManoObra"]) ? $old["costoManoObra"] : ( isset($usuario->costoManoObra) ? $usuario->costoManoObra : "" );

	} else {
		$usuarioField = isset($old["usuario"]) ? $old["usuario"] : "";
		// $activo = isset($old["activo"]) && $old["activo"] == "on" ? true : false;
		$contrasena = isset($old["contrasena"]) ? $old["contrasena"] : "";
		$nombre = isset($old["nombre"]) ? $old["nombre"] : "";
		$apellidoPaterno = isset($old["apellidoPaterno"]) ? $old["apellidoPaterno"] : "";
		$apellidoMaterno = isset($old["apellidoMaterno"]) ? $old["apellidoMaterno"] : "";
		$correo = isset($old["correo"]) ? $old["correo"] : "";
		// $fotoAnterior = null;
		$foto = null;
		// $firmaAnterior = null;
		$firma = null;
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : "";
		$salario = isset($old["salario"]) ? $old["salario"] : 0;
		$costoManoObra = isset($old["costoManoObra"]) ? $old["costoManoObra"] : 0;

	}
?>

<div class="row">

	<div class="col-md-6">

		<div class="card card-info card-outline">

			<div class="card-header">
              <h3 class="card-title">Datos generales</h3>
            </div>

			<div class="card-body">

				<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

				<div class="row">
				
					<div class="col-md-6">

					<input type="hidden" name="usuarioId" id="usuarioId" value="<?php echo fString($usuario->id); ?>">

					<!-- <div class="row"> -->
						<div class="form-group">
							<label for="usuario">Usuario:</label>
							<?php if ( isset($usuario->id) ) : ?>
								<input type="text" value="<?php echo fString($usuarioField); ?>" class="form-control form-control-sm text-uppercase" disabled>
							<?php else: ?>
								<input type="text" name="usuario" value="<?php echo fString($usuarioField); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el usuario">
							<?php endif; ?>
						</div>

					<!-- 	<div class="col-md-6 form-group form-check">
							<label for="activo">Activo:</label>
							<div class="input-group">
								<input type="checkbox" name="activo" 
								<?php 
								// echo $activo ? "checked" : ""; 
								?> 
								class="form-check-input">
							</div>
						</div> -->
					<!-- </div> -->

						<div class="form-group">
							<label for="nombre">Nombre(s):</label>
							<input type="text" name="nombre" value="<?php echo fString($nombre); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el nombre(s) del usuario">
						</div>

						<div class="form-group">
							<label for="apellidoPaterno">Apellido Paterno:</label>
							<input type="text" name="apellidoPaterno" value="<?php echo fString($apellidoPaterno); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el apellido paterno">
						</div>

						<div class="form-group">
							<label for="apellidoMaterno">Apellido Materno:</label>
							<input type="text" name="apellidoMaterno" value="<?php echo fString($apellidoMaterno); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el apellido materno">
						</div>
					</div>

					<?php
						if ( is_null($foto) ) {
							$previsual = App\Route::rutaServidor()."vistas/img/usuarios/default/anonymous.png";
						} else {
							$previsual = App\Route::rutaServidor().$foto;
						}
					?>
					<div class="col-md-6 form-group">
						<label for="foto">Imágen:</label>
						<!-- <figure style="height: 18rem;"> -->
						<picture>
							<img src="<?php echo $previsual; ?>" id="imgFoto" class="img-fluid img-thumbnail previsualizar" style="width: 100%">
						</picture>
						<span class="text-muted">Presione sobre la imágen si desea cambiarla (Resolución recomendada 500 x 500 pixeles)</span>
						<?php if ( isset($usuario->id) ) : ?>
							<input type="hidden" name="fotoAnterior" value="<?php echo $fotoAnterior; ?>">
						<?php endif; ?>
						<input type="file" class="form-control form-control-sm" id="foto" name="foto" style="display: none">
					</div>

				</div>

				<div class="form-group">
					<label for="correo">Correo Electrónico:</label>
					<input type="email" name="correo" value="<?php echo fString($correo); ?>" class="form-control form-control-sm text-lowercase" placeholder="Ingresa el correo electrónico">
				</div>

				<div class="row">
					<div class="col-lg-6 form-group">
						<label for="contrasena">Contraseña:</label>
						<input type="password" name="contrasena" value="<?php echo $contrasena; ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la contraseña">
						<?php if ( isset($usuario->id) ) : ?>
							<span class="text-muted">Dejar en blanco para no cambiar la contraseña</span>
						<?php endif; ?>
					</div>
				</div>

				<?php
					if ( is_null($firma) ) {
						$previsualFirma = App\Route::rutaServidor()."vistas/img/usuarios/firmas/default.jpg";
					} else {
						$previsualFirma = App\Route::rutaServidor().$firma;
					}
				?>
				<div class="form-group">
					<label for="firma">Firma:</label>
					<picture>
						<img src="<?php echo $previsualFirma; ?>" id="imgFirma" class="img-fluid img-thumbnail previsualizarFirma" style="width: 100%">
					</picture>
					<span class="text-muted">Presione sobre la firma si desea cambiarla (Resolución recomendada 400 x 100 pixeles)</span>
					<?php if ( isset($usuario->id) ) : ?>
						<input type="hidden" name="firmaAnterior" value="<?php echo $firmaAnterior; ?>">
					<?php endif; ?>
					<input type="file" class="form-control form-control-sm" id="firma" name="firma" style="display: none">
				</div>

				<div class="form-group">
					<label for="empresaId">Empresa:</label>
					<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
					<?php if ( $usuarioAutenticado->checkAdmin() || App\Controllers\Autorizacion::permiso($usuarioAutenticado, "usuarios", "actualizar") ) : ?>

						<select name="empresaId" id="empresaId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
							<option value="">Selecciona una Empresa</option>
					<?php else: ?>

						<select id="empresaId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>

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
					<label for="ubicacionId">Ubicacion:</label>
					<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
					<?php if ( $usuarioAutenticado->checkAdmin() || App\Controllers\Autorizacion::permiso($usuarioAutenticado, "usuarios", "actualizar") ) : ?>

						<select name="ubicacionId" id="ubicacionId" class="custom-select form-controls form-control-sms select2" style="width: 100%">
							<option value="">Selecciona una ubicación</option>
					<?php else: ?>

						<select id="ubicacionId" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>

					<?php endif; ?>

						<?php foreach($ubicaciones as $ubicacion) { ?>
						<option value="<?php echo $ubicacion["id"]; ?>"
							<?php echo $ubicacionId == $ubicacion["id"] ? ' selected' : ''; ?>
							><?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
						</option>
						
					<?php } ?>
					</select>	
				</div>


			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-6">

		<div class="card card-warning card-outline">

			<div class="card-header">
              <h3 class="card-title">Perfiles</h3>
            </div>

			<div class="card-body">

				<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
			    <?php if ( $usuarioAutenticado->checkAdmin() || App\Controllers\Autorizacion::permiso($usuarioAutenticado, "usuarios", "actualizar") ) : ?>

					<?php foreach($perfiles as $perfil) { ?>
					<div class="form-check">
						<!-- <div class="input-group perfiles"> -->
							<?php if ( mb_strtolower($usuarioField) == mb_strtolower(CONST_ADMIN) && mb_strtolower($perfil["nombre"]) == mb_strtolower(CONST_ADMIN) ) : ?>
								<input type="hidden" name="perfiles[]" value="<?php echo fString($perfil["nombre"]); ?>" checked>
								<input type="checkbox" value="<?php echo fString($perfil["nombre"]); ?>" <?php echo in_arrayi($perfil["nombre"], $usuario->perfiles) ? "checked" : "" ; ?> class="form-check-input" disabled>
								<label class="form-check-label text-uppercase">
									<?php echo fString($perfil["nombre"]); ?>
								</label>
							<?php else: ?>
								<input type="checkbox" name="perfiles[]" id="<?=fString($perfil["nombre"])?>" value="<?php echo fString($perfil["nombre"]); ?>" <?php echo in_arrayi($perfil["nombre"], $usuario->perfiles) ? "checked" : "" ; ?> class="form-check-input">
								<label class="form-check-label text-uppercase" for="<?=fString($perfil["nombre"])?>">
									<?php echo fString($perfil["nombre"]); ?>
								</label>
							<?php endif; ?>
						<!-- </div> -->
					</div>
					<?php } ?>

				<?php else: ?>

					<ul class="list-group">
						<?php if ( $usuario->perfiles ) : ?>
							<?php foreach($usuario->perfiles as $perfil) { ?>
								<li class="list-group-item text-uppercase"><?php echo fString($perfil); ?></li>
							<?php } ?>
						<?php else: ?>
							<li class="list-group-item">No tiene perfiles asignados</li>
						<?php endif; ?>
					</ul>

			    <?php endif; ?>

			</div> <!-- <div class="box-body"> -->

		</div> 
		<!-- <div class="box box-warning"> -->
			
		<?php if ( isset($usuario->id) ) : ?>
		<div class="card card-success card-outine">
			<div class="card-header">
				<h3 class="card-title">Documentacion</h3>
				<div class="card-tools">
					<input type="file" id="inputDocumentoUsuario" style="display: none;" multiple accept="application/pdf">
					<button type="button" class="btn btn-sm btn-success" id="btnAgregarDocumento" title="Agregar Documento">
						<i class="fas fa-plus"></i> Agregar
					</button>
				</div>
			</div>
			<div class="card-body">

				<?php foreach($usuario->documentos as $documento) { ?>					
					<div class="d-flex justify-content-between align-items-center mb-2">
						<span class="text-uppercase"><?php echo fString($documento["titulo"]); ?></span>
						<div>
							<?php if (!empty($documento["archivo"])) : ?>
								<button data-ruta="<?php echo $documento["ruta"]; ?>" class="btn btn-sm btn-info" type="button" data-toggle="modal" data-target="#modalVerDocumentoUsuario" title="Ver Documento">
									<i class="fas fa-eye"></i>
								</button>
								<button type="button" class="btn btn-sm btn-danger btnEliminarDocumento" data-id="<?php echo $documento["id"]; ?>" title="Eliminar">
									<i class="fas fa-trash"></i>
								</button>
							<?php else: ?>
								<span class="text-muted">Sin archivo</span>
							<?php endif; ?>
						</div>
					</div>
				<?php } ?>
			</div> <!-- <div class="box-body"> -->
		</div> <!-- <div class="box box-success"> -->
		<?php endif; ?>

		<div class="card card-info card-outline">
			<div class="card-header">
			  <h3 class="card-title">Salario</h3>
			</div>
			<div class="card-body">

				<div class="form-group">
					<label for="salario">Salario:</label>
					<input type="text" step="0.01" name="salario" value="<?php echo $salario; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el salario del usuario">
				</div>

				<div class="form-group">
					<label for="costoManoObra">Costo Mano de obra (H):</label>
					<input type="text" step="0.01" name="costoManoObra" value="<?php echo $costoManoObra; ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa el costo de mano de obra por hora">
				</div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
