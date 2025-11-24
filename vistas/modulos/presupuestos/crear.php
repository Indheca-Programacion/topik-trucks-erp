<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Presupuestos <small class="font-weight-light">Crear</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('presupuestos.index')?>"> <i class="fas fa-truck"></i> Presupuestos</a></li>
	            <li class="breadcrumb-item active">Crear presupuesto</li>
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
			<div class="col">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-plus"></i>
							Crear Presupuesto
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('presupuestos.store'); ?>" enctype="multipart/form-data">
							
							<?php include "vistas/modulos/presupuestos/formulario.php"; ?>									
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>

	<div class="modal fade" id="modalAgregarCliente" aria-labelledby="modalAgregarClienteLabel" aria-hidden="true">
	  <div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      <div class="modal-header">
	        <h5 class="modal-title" id="modalAgregarClienteLabel">Agregar Cliente</h5>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          <span aria-hidden="true">&times;</span>
	        </button>
	      </div>
	      <div class="modal-body">
			<form method="POST" action="<?php echo Route::names('clientes.store'); ?>">
				<?php include "vistas/modulos/clientes/formulario.php"; ?>
				<button type="submit" class="btn btn-outline-primary">
					<i class="fas fa-save"></i> Guardar Cliente
				</button>										
				<div id="msgSendCliente"></div>
			</form>
	      </div>
	    </div>
	  </div>

	</div>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/presupuestos.js?v=1.00');
?>
