<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Pagos <small class="font-weight-light">Subir</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('pagos.index')?>"> <i class="fas fa-tools"></i> Pagos</a></li>
	            <li class="breadcrumb-item active">Subir pagos</li>
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
							<i class="fas fa-plus"></i>
							Crear requisición
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('pagos.store'); ?>" enctype="multipart/form-data">
							<?php include "vistas/modulos/pagos/formulario-step.php"; ?>
									
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

	<div class="modal fade" id="modalVerRequisicion" tabindex="-1" role="dialog" aria-labelledby="modalVerRequisicionLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerRequisicionLabel">Ver Requisición</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body p-0">
					<iframe id="iframeRequisicion" src="" frameborder="0" style="width:100%;height:80vh;"></iframe>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="modalVerOrdenes" tabindex="-1" role="dialog" aria-labelledby="modalVerOrdenesLabel" aria-hidden="true">
		<div class="modal-dialog modal-xl" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalVerOrdenesLabel">Ver Orden de Compra</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body p-0">
					<iframe id="iframeOrdenes" src="" frameborder="0" style="width:100%;height:80vh;"></iframe>
				</div>
			</div>
		</div>
	</div>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/pagos.js?v=2.0');
?>
