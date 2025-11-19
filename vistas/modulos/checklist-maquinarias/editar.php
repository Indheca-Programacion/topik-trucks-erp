<?php
	$old = old();

	use App\Route;
	use App\Controllers\Autorizacion;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
		  <div class="row mb-2">
			<div class="col-sm-6">
			  <h1>Checklist Maquinarias <small class="font-weight-light">Editar</small></h1>
			</div>
			<div class="col-sm-6">
			  <ol class="breadcrumb float-sm-right">
				<li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
				<li class="breadcrumb-item"><a href="<?=Route::names('checklist-maquinarias.index')?>"> <i class="fas fa-clipboard-list"></i> Checklist Maquinarias</a></li>
				<li class="breadcrumb-item active">Editar checklist</li>
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
							<i class="fas fa-edit"></i>
							Editar checklist
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('checklist-maquinarias.update', $checklistMaquinarias->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<div class="row">
								<?php include "vistas/modulos/checklist-maquinarias/formulario.php"; ?>
								<?php include "vistas/modulos/checklist-maquinarias/formulario-checklist.php"; ?>
							</div>
								<button type="button" id="btnSend" class="btn btn-outline-primary">
									<i class="fas fa-save"></i> Actualizar
								</button>
								<?php if(
											Autorizacion::permiso($usuario, 'auth-indheca-cl', 'ver') && is_null($checklistMaquinarias->usuarioIdAutorizacion)
										): ?>
									<button type="button" id="btnAutorizar" auth="indheca" class="btn btn-outline-success ml-2">
										<i class="fas fa-check"></i> Autorizar
									</button>
								<?php elseif(
											Autorizacion::permiso($usuario, 'auth-cliente-cl', 'ver') && is_null($checklistMaquinarias->usuarioIdAutorizacionCliente)
										): ?>
									<button type="button" id="btnAutorizar" auth="cliente" class="btn btn-outline-success ml-2">
										<i class="fas fa-check"></i> Autorizar
									</button>
								<?php endif; ?>
								<a href="<?= Route::names('checklist-maquinarias.print', $checklistMaquinarias->id); ?>" target="_blank" id="btnImprimir" class="btn btn-outline-info float-right">
									<i class="fas fa-print"></i> Imprimir
								</a>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
				</div> <!-- /.card -->
			</div> <!-- /.col -->
		</div> <!-- ./row -->
	</div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/checklist-maquinarias.js');
?>
