<div class="row">

	<div class="col-12">

		<?php if ( $maquinaria->consumibles ) : ?>
			
		<div class="card card-info card-outline">

			<div class="card-body">

				<table class="table table-hover text-nowrap" id="tablaMaquinariaCargas" width="100%">
					<thead>
						<tr>
							<th>#</th>
							<th>Empresa</th>
							<th class="text-center">Fecha</th>
							<th class="text-center">Hora</th>
							<th>Ubicación</th>
							<th>Operador</th>
							<th class="text-right">Horómetro / Odómetro</th>
							<th>Observaciones</th>
							<th class="text-right">Litros</th>
						</tr>
					</thead>
					<tbody>

						<?php foreach($maquinaria->consumibles as $key => $value) { ?>
						<tr>
							<td><?php echo $key+1; ?></td>
							<td class="text-uppercase"><?php echo fString($value['empresas.nombreCorto']); ?></td>
							<td class="text-center"><?php echo fFechaLarga($value["fecha"]); ?></td>
							<td class="text-center"><?php echo substr($value["hora"], 0, 5); ?></td>
							<td class="text-uppercase"><?php echo fString($value['ubicaciones.descripcion']); ?></td>
							<td class="text-uppercase"><?php echo fString($value['empleados.nombreCompleto']); ?></td>
							<td class="text-right"><?php echo $value['horoOdometro']; ?></td>
							<td class="text-uppercase"><?php echo fString($value['observaciones']); ?></td>
							<td class="text-right"><?php echo $value['litros']; ?></td>
						</tr>
						<?php } ?>

					</tbody>
				</table>

				<?php
					$comandoJS = "fDataTable('#tablaMaquinariaCargas');";
				?>

			</div> <!-- <div class="card-body"> -->

		</div> <!-- <div class="card card-info"> -->

		<?php else: ?>
		<div class="jumbotron">
			<p class="display-4">Maquinaria sin Registros</p>
		</div>
		<?php endif; ?>

	</div> <!-- <div class="col-12"> -->

</div> <!-- <div class="row"> -->
