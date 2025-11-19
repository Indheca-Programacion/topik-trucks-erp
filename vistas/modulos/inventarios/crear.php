<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Inventarios <small class="font-weight-light">Crear</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('inventarios.index')?>"> <i class="fas fa-boxes"></i> Inventarios</a></li>
	            <li class="breadcrumb-item active">Crear inventario</li>
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
				<div class="col-12">
					<div class="card card-primary card-outline">
						<div class="card-header">
							<h3 class="card-title">
								<i class="fas fa-plus"></i>
								Crear inventario
							</h3>
						</div> <!-- <div class="card-header"> -->
						<div class="card-body">
							<?php include "vistas/modulos/errores/form-messages.php"; ?>
							<form id="formSend" method="POST">
								<?php include "vistas/modulos/inventarios/formulario.php"; ?>
								<button type="button" id="btnGuardar"class="btn btn-outline-primary">
									<i class="fas fa-save"></i> Guardar
								</button>										
								<div id="msgSend"></div>
							</form>
							<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
						</div> <!-- /.card-body -->
					</div> <!-- /.card -->
				</div> <!-- /.col -->
			</div> <!-- ./row -->
		</div><!-- /.container-fluid -->

		<!-- Modal -->
		<div class="modal fade" id="firmaModal" role="dialog" aria-labelledby="firmaModalTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="firmaModalTitle">
							Firma de entrega
						</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="" role="alert">
							<strong>Nota:</strong> Para firmar, dibuje su firma en el recuadro de abajo.
							<canvas class="border" id="canvas" ></canvas>

						</div>
					</div>
					<div class="modal-footer">
						<button id="btnLimpiar" type="button" class="btn btn-outline-info"><i class="fas fa-broom"></i>Limpiar</button>
						<button type="button" class="btn btn-primary" data-dismiss="modal">Confirmar</button>
					</div>
				</div>
			</div>
		</div>
		
	</section>
	
	<!-- Modal -->
		
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/inventarios.js?v=5.0');
?>
