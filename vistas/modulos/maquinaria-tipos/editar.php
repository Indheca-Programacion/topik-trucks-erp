<?php
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Tipos de Maquinaria <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item"><a href="<?=Route::names('maquinaria-tipos.index')?>"> <i class="fas fa-truck"></i> Tipos de Maquinaria</a></li>
	            <li class="breadcrumb-item active">Editar tipo de maquinaria</li>
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
			<div class="col-md-6">
				<div class="card card-primary card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-edit"></i>
							Editar tipo de maquinaria
						</h3>
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						<?php include "vistas/modulos/errores/form-messages.php"; ?>
						<form id="formSend" method="POST" action="<?php echo Route::names('maquinaria-tipos.update', $maquinariaTipo->id); ?>">
							<input type="hidden" name="_method" value="PUT">
							<?php include "vistas/modulos/maquinaria-tipos/formulario.php"; ?>
							<button type="button" id="btnSend" class="btn btn-outline-primary">
								<i class="fas fa-save"></i> Actualizar
							</button>
							<div id="msgSend"></div>
						</form>
						<?php include "vistas/modulos/errores/form-messages-validation.php"; ?>
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
			<div class="col-md-6">
				<div class="card card-info card-outline">
					<div class="card-header">
						<h3 class="card-title">
							<i class="fas fa-clipboard-check"></i>
							CheckList
						</h3>
						<button type="button" class="btn btn-success btn-sm float-right" data-toggle="modal" data-target="#modalAddChecklist">
							<i class="fas fa-plus"></i> Agregar Tarea
						</button>

						
					</div> <!-- <div class="card-header"> -->
					<div class="card-body">
						
						<table class="table table-bordered table-striped table-sm" id="tableChecklist">
							<thead>
								<tr>
									<th>Sección</th>
									<th>Tarea</th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody id="tbodyChecklist">
								<?php foreach ($tareas as $tarea): ?>
									<tr>
										<td><?php echo mb_strtoupper($tarea["seccion"]); ?></td>
										<td><?php echo mb_strtoupper($tarea["descripcion"]); ?></td>
										<td>
											<button type="button" class="btn btn-danger btn-sm btnDeleteChecklist" data-id="<?php echo $tarea["id"]; ?>">
												<i class="fas fa-trash"></i>
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					
					</div> <!-- /.card-body -->
          		</div> <!-- /.card -->
        	</div> <!-- /.col -->
      	</div> <!-- ./row -->
    </div><!-- /.container-fluid -->

	</section>
	<!-- Modal -->
	<div class="modal fade" id="modalAddChecklist" role="dialog" aria-labelledby="modalAddChecklistLabel" aria-hidden="true">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="modalAddChecklistLabel">Agregar Tarea</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<form id="formAddChecklist">
						<input type="hidden" name="maquinariaTipoId" value="<?php echo $maquinariaTipo->id; ?>">
						<input type="hidden" name="_token" value="<?php echo token(); ?>">
						<div class="form-group">
							<label for="seccion">Sección</label>
							<div class="input-group">

								<select class="custom-select form-controls select2Add" id="seccion" name="sectionId" required>
									<option value="">Seleccione una sección</option>
									<?php foreach ($secciones as $seccion): ?>
										<option value="<?php echo $seccion["id"]; ?>"><?php echo $seccion["descripcion"]; ?></option>
									<?php endforeach; ?>
								</select>
								<div class="input-group-append">
									<button type="button" id="btnAddSectionId" class="btn btn-sm btn-success" disabled>
										<i class="fas fa-plus-circle"></i>
									</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="descripcion">Descripción</label>
							<input type="text" class="form-control form-control-sm text-uppercase" id="descripcion" name="descripcion" required>
						</div>
					</form>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
					<button type="button" id="btnAddChecklist" class="btn btn-primary">Agregar</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/maquinaria-tipos.js');
?>
