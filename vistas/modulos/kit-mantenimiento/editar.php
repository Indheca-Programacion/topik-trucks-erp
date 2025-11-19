<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Kit de Mantenimiento <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('kit-mantenimiento.index')?>"> <i class="fas fa-list-alt"></i> Kit de Mantenimiento</a></li>
	            <li class="breadcrumb-item active">Editar Kit de Mantenimiento</li>
	          </ol>
	        </div>
	      </div>
	    </div><!-- /.container-fluid -->

	</section>

	<section class="content">

	<?php if ( !is_null(flash()) ) : ?>
      <div class="d-none" id="msgToast" clase="<?=flash()->clase?>" titulo="<?=flash()->titulo?>" subtitulo="<?=flash()->subTitulo?>" mensaje="<?=flash()->mensaje?>"></div>
    <?php endif; ?>

    <div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Editar Kit de Mantenimiento
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('kit-mantenimiento.update', $kitMantenimiento->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/kit-mantenimiento/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->

		<!-- Modal para editar detalles del Kit de Mantenimiento -->
		<div class="modal fade" id="modalEditarDetalle" tabindex="-1" role="dialog" aria-labelledby="modalEditarDetalleLabel" aria-hidden="true">
			<div class="modal-dialog" role="document">
				<form id="formEditarDetalle" method="POST">
					<div class="modal-content">
						<div class="modal-header">
							<h5 class="modal-title" id="modalEditarDetalleLabel">Editar Detalle</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<input type="hidden" name="id_detalle" id="id_detalle">
							<div class="form-group">
								<label for="cantidad_detalle">Cantidad</label>
								<input type="number" class="form-control" id="cantidad_detalle" name="cantidad_detalle" min="1" required>
							</div>
							<div class="form-group">
								<label for="unidad_detalle">Unidad</label>
								<input type="text" class="form-control text-uppercase" id="unidad_detalle" name="unidad_detalle" required>
							</div>
							<div class="form-group">
								<label for="numero_parte_detalle">Número de Parte</label>
								<input type="text" class="form-control text-uppercase" id="numero_parte_detalle" name="numero_parte_detalle" required>
							</div>
							<div class="form-group">
								<label for="concepto_detalle">Concepto</label>
								<textarea class="form-control text-uppercase" id="concepto_detalle" name="concepto_detalle" rows="3" required></textarea>
							</div>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-primary btnGuardarCambios">Guardar Cambios</button>
						</div>
					</div>
				</form>
			</div>
		</div>
    </div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/kit-mantenimiento.js');
?>