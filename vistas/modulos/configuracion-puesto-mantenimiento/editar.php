<?php
	$old = old();


	$id_puesto = isset($old["id_puesto"]) ? $old["id_puesto"] : "";
	$id_mantenimiento_tipo = isset($old["id_mantenimiento_tipo"]) ? $old["id_mantenimiento_tipo"] : "";

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">

		<div class="container-fluid">
	      <div class="row mb-2">
	        <div class="col-sm-6">
	          <h1>Configuración Puesto Mantenimiento <small class="font-weight-light">Editar</small></h1>
	        </div>
	        <div class="col-sm-6">
	          <ol class="breadcrumb float-sm-right">
	            <li class="breadcrumb-item"><a href="<?=Route::routes('inicio')?>"> <i class="fas fa-tachometer-alt"></i> Inicio</a></li>
	            <li class="breadcrumb-item active">Editar configuración puesto mantenimiento</li>
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

			<div class="col-md-9">
			<div class="card card-secondary card-outline">
				<div class="card-header">
				<h3 class="card-title">
					<i class="fas fa-list-ol"></i> 
					Listado de Puestos Tipo Mantenimiento
				</h3>
				<div class="card-tools">
					<button type="button" class="btn btn-outline-primary " data-toggle="modal" data-target="#modalAsignarPuestoMantenimiento">Crear asignacion</button>
				</div>
				</div>

				<div class="card-body">
			
				<table class="table table-bordered table-striped" id="tablaPuestoTipo" width="100%">
					
					<thead>
					<tr>
					<th style="width:10px">#</th>
					<th>Puesto</th>
					<th>Tipo Mantenimiento</th>
					<th>Acciones</th>
					</tr> 
					</thead>

					<tbody class="text-uppercase">
					</tbody>

				</table>

				</div> <!-- /.card-body -->
			</div> <!-- /.card -->
			</div> <!-- /.col -->

		</div> <!-- <div class="row"> -->

    </div><!-- /.container-fluid -->

	</section>

</div>

<?php
	array_push($arrayArchivosJS, 'vistas/js/configuracion-puesto-mantenimiento.js?v=1.01');
?>


<!-- modalAsignarPuesto -->
<div class="modal fade" id="modalAsignarPuestoMantenimiento" tabindex="-1" role="dialog" aria-labelledby="modalAsignarPuestoMantenimientoLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    	<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="modalAgregarIncidenciaLongTitle">Asignar Puesto Tipo Mantenimiento</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>

			<form id="formSendAsignarPuestoMantenimiento" method="POST" enctype="multipart/form-data">
								
			<input type="hidden" name="_token" value="<?php echo createToken(); ?>">


			<div class="modal-body">
				<div class="alert alert-danger error-validacion mb-2 d-none">
					<ul class="mb-0">
						<!-- <li></li> -->
					</ul>
				</div>
				<div class="row">
					<!-- Puesto-->
					<div class="col-md-6">

							<div class="form-group">
								<label for="empresaId">Puesto:</label>
								<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
								<?php if ( $usuarioAutenticado->checkAdmin() ) : ?>

									<select name="id_puesto" id="id_puesto" class="custom-select form-controls form-control-sms select2" style="width: 100%">
										<option value="">Selecciona un puesto</option>
								<?php else: ?>

									<select id="id_puesto" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>

								<?php endif; ?>

									<?php foreach($puestos as $puesto) { ?>
									<option value="<?php echo $puesto["id"]; ?>"
										<?php echo $id_puesto == $puesto["id"] ? ' selected' : ''; ?>
										><?php echo mb_strtoupper(fString($puesto["nombre"])); ?>
									</option>
									
								<?php } ?>
								</select>	
							</div>
					</div>

					<!-- ZONA -->

					<div class="col-md-6">
						<div class="form-group">
								<label for="id_mantenimiento_tipo">Tipo de mantenimiento:</label>
								<!-- $usuarioAutenticado->checkPermiso("usuarios") -->
								<?php if ( $usuarioAutenticado->checkAdmin() ) : ?>

									<select name="id_mantenimiento_tipo" id="id_mantenimiento_tipo"  class="custom-select form-controls form-control-sms select2" style="width: 100%">
										<option value="">Selecciona un tipo de mantenimiento</option>
								<?php else: ?>

									<select id="id_mantenimiento_tipo" class="custom-select form-controls form-control-sms select2" style="width: 100%" disabled>

								<?php endif; ?>

									<?php foreach($mantenimientoTipos as $mantenimientoTipo) { ?>
									<option value="<?php echo $mantenimientoTipo["id"]; ?>"
										<?php echo $id_mantenimiento_tipo == $mantenimientoTipo["id"] ? ' selected' : ''; ?>
										><?php echo mb_strtoupper(fString($mantenimientoTipo["descripcion"])); ?>
									</option>
									
								<?php } ?>
								</select>	
							</div>
					</div>
					 
				</div>
			</div>

			</form>	
			<div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
			<button type="button" class="btn btn-primary btnAsignarPuestoMantenimiento">Asignar</button>
			</div>
    	</div>
	</div>
</div>
