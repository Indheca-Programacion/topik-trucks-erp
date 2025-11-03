<div class="row">

  <?php
  use App\Route;
  // var_dump($servicio->requisiciones);
  $formularioEditable = true;

  ?>

	<div class="col-12">

		<?php if ( $traslado->requisiciones ) : ?>
			
		<div class="card card-info card-outline">

      <?php if ( $formularioEditable ) : ?>
      <div class="card-tools pt-1">
      </div>
      <?php endif; ?>

			<!-- <div class="card-body table-responsive p-0"> -->
      <div class="card-body pt-0">

  				<table class="table table-hover text-nowrap" id="tablaRequisiciones" width="100%">
          <!-- <table class="table table-bordered table-striped" id="tablaMaquinarias" width="100%"> -->
                  <thead>
                    <tr>
                      <th style="width: 10px">#</th>
                      <th>Folio</th>
                      <th>Estatus</th>
                      <th>Fecha Requisición</th>
                      <th>Acciones</th>
                    </tr>
                  </thead>
                  <tbody>

                  	<?php foreach($traslado->requisiciones as $key => $value) { ?>
                    <tr>
                      <td><?php echo $key+1; ?></td>
                      <td class="text-uppercase"><?php echo fString($value['folio']); ?></td>
                      <td class="text-uppercase"><?php echo fString($value['servicio_estatus.descripcion']); ?></td>
                      <td><?php echo fFechaLarga($value["fechaCreacion"]); ?></td>
                      <td><a target="_blank" href="<?php echo Route::names('requisiciones.edit', $value['id']); ?>" class='btn btn-xs btn-warning'><i class='fas fa-pencil-alt'></i></a></td>
                    </tr>
                    <?php } ?>

                  </tbody>
          </table>

          <?php
            // $comandoJS = "fDataTable('#tablaRequisiciones');"
          ?>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>

    <?php if ( $formularioEditable ) : ?>
    <a href="<?=Route::routes('traslados.crear-requisicion', $traslado->id)?>" class="btn btn-outline-primary mt-2 mr-2 float-right">
      <i class="fas fa-plus"></i> Crear requisición
    </a>
    <?php endif; ?>
		<div class="jumbotron text-center">
			<p class="display-4">Traslado sin Requisiciones</p>
		</div>

    <?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->
