<?php
// var_dump($arrayProgramacion);
?>
<div class="card">
	<div class="card-header">
		<h3 class="card-title">
			<i class="fas fa-flag mr-1 text-warning"></i>
			<?=fString($servicioProximoTitulo)?>
		</h3>
		<div class="card-tools">
			<button type="button" class="btn btn-tool text-dark" data-card-widget="collapse">
				<i class="fas fa-minus"></i>
			</button>
			<button type="button" class="btn btn-tool text-dark" data-card-widget="remove">
				<i class="fas fa-times"></i>
			</button>
		</div>
	</div>
	<div class="card-body p-0">
		<div class="table-responsive">
			<table class="table table-bordered table-striped m-0" width="100%">
				<thead>
					<tr>
						<th>Equipo</th>
						<th>Servicio</th>
						<th class="text-right">Pendiente</th>
					</tr>
				</thead>
				<tbody class="text-nowrap text-uppercase">
					<?php foreach($arrayProgramacion as $key => $detalle) : ?>
						<tr>
							<td><?=fString($detalle['equipo'])?></td>
							<td><?=fString($detalle['servicio'])?></td>
							<td class="text-right"><?=$detalle['pendiente']?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
            </table>
		</div>
	</div> <!-- /.card-body -->
</div> <!-- /.card -->
