<div class="row">

  <?php
  // var_dump($maquinaria->servicios);
  ?>

	<div class="col-12">

		<?php if ( $maquinaria->servicios ) : ?>
			
		<div class="card card-info card-outline">

      <div class="card-tools  pt-1">
        <button href="" class="btn btn-outline-primary float-right" data-toggle="modal" data-target="#modalCrearServicio">
          <i class="fas fa-plus"></i> Programar mantenimiento por activación de equipo
        </button>
      </div>

			<!-- <div class="card-body table-responsive p-0"> -->
      <div class="card-body">

  				<table class="table table-hover text-nowrap" id="tablaMaquinariaServicios" width="100%">
          <!-- <table class="table table-bordered table-striped" id="tablaMaquinarias" width="100%"> -->
                  <thead>
                    <tr>
                      <th>#</th>
                      <th>Folio</th>
                      <th>Estatus</th>
                      <th>Fecha Solicitud</th>
                      <th>Fecha Finalización</th>
                      <th>Tipo de Servicio</th>
                    </tr>
                  </thead>
                  <tbody>

                  	<?php foreach($maquinaria->servicios as $key => $value) { ?>
                    <tr>
                      <td><?php echo $key+1; ?></td>
                      <td class="text-uppercase"><?php echo fString($value['id']); ?></td>
                      <td class="text-uppercase"><?php echo fString($value['servicio_estatus.descripcion']); ?></td>
                      <td><?php echo fFechaLarga($value["fechaSolicitud"]); ?></td>
                      <td><?php echo ($value["fechaFinalizacion"]) ? fFechaLarga($value["fechaFinalizacion"]) : ''; ?></td>
                      <td class="text-uppercase"><?php echo fString($value['servicio_tipos.descripcion']); ?></td>
                    </tr>
                    <?php } ?>

                  </tbody>
          </table>

          <?php
            // $comandoJS = "fDataTable('#tablaMaquinariaServicios');"
            if ( isset($comandoJS) ) $comandoJS .= "fDataTable('#tablaMaquinariaServicios');";
            else $comandoJS = "fDataTable('#tablaMaquinariaServicios');";
          ?>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>
      <button href="" class="btn btn-outline-primary mt-2 mr-2 float-right" data-toggle="modal" data-target="#modalCrearServicio">
          <i class="fas fa-plus"></i> Programar mantenimiento por activación de equipo
        </button>
		<div class="jumbotron">
			<p class="display-4">Maquinaria sin Servicios realizados</p>
		</div>
		<?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->