<div class="row">

  <?php
  use App\Route;
  // var_dump($maquinaria->servicios);
  ?>

	<div class="col-12">

		<?php if ( $maquinaria->checklist ) : ?>
			
		<div class="card card-info card-outline">
            <div class="card-tools pt-1">
                <a href="<?=Route::routes('maquinarias.crear-checklist', $maquinaria->id)?>" class="btn btn-outline-primary float-right">
                    <i class="fas fa-plus"></i> Crear checklist
                </a>
            </div>
			<!-- <div class="card-body table-responsive p-0"> -->
            <div class="card-body">

                <table class="table table-hover text-nowrap" id="tablaCheckList" width="100%">
                <!-- <table class="table table-bordered table-striped" id="tablaMaquinarias" width="100%"> -->
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Fecha Creacion</th>
                        <th>Estatus</th>
                        <th>Creó</th>
                        <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody>

                        <?php foreach($maquinaria->checklist as $key => $value) { ?>
                        <tr>
                        <td><?php echo $key+1; ?></td>
                        <td><?php echo fFechaLarga($value["fecha"]); ?></td>
                        <td class="text-uppercase"><?php echo fString($value['estatus']); ?></td>
                        <td class="text-uppercase"><?php echo fString($value['creo']); ?></td>
                        <td><a href="<?php echo Route::names('checklist-maquinarias.edit', $value['id']); ?>" class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a></td>
                        </tr>
                        <?php } ?>

                    </tbody>
                </table>    

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>
            <a href="<?=Route::routes('maquinarias.crear-checklist', $maquinaria->id)?>" class="btn btn-outline-primary mt-2 mr-2 float-right">
                <i class="fas fa-plus"></i> Crear checklist
            </a>
		<div class="jumbotron">
			<p class="display-4">Maquinaria sin CheckList realizados</p>
		</div>
		<?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->