<div class="row">

	<div class="col-12">

		<?php if ( $servicio->actividades ) : ?>

		<div class="card card-info card-outline">

			<div class="card-body pt-0">

				<table class="table table-hover text-nowrap" id="tablaServicioActividades" width="100%">
					<thead>
						<tr>
							<th style="width: 10px">#</th>
							<th>Fecha</th>
							<th>Avance de Reparación</th>
							<th class="text-right">Horas</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($servicio->actividades as $key => $value) { ?>
						<tr>
							<td><?php echo $key+1; ?></td>
							<td><?php echo fFechaLarga($value["fecha"]); ?></td>
							<td class="text-uppercase"><?php echo fString($value['descripcion']); ?></td>
							<td class="text-right"><?php echo number_format($value['horas'], 2); ?></td>
						</tr>
	                    <?php } ?>
					</tbody>
				</table>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

		<?php else: ?>

		<div class="jumbotron text-center">
			<p class="display-4">Orden de Trabajo sin Actividades</p>
		</div>

		<?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->
