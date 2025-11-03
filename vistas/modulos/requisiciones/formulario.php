<div class="card card-primary card-outline card-outline-tabs col-12">
	<div class="card-header p-0 border-bottom-0">
		<ul class="nav nav-tabs" id="tabServicio" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" id="requisiciones-tab" data-toggle="pill" href="#requisiciones" role="tab" aria-controls="requisiciones" aria-selected="true">Requisiciones</a>
			</li>
			<li class="nav-item">
				<a class="nav-link " id="orden-trabajo-tab" data-toggle="pill" href="#orden-trabajo" role="tab" aria-controls="orden-trabajo" aria-selected="false">Orden de Compra</a>
			</li>
			<li class="nav-item">
				<?php if ( isset($requisicion->id) ) : ?>
				<a class="nav-link <?php echo (isset($requisicion->usuarioIdResponsable)) ? '' : 'disabled'; ?>" id="cotizaciones-tab" data-toggle="pill" href="#cotizaciones" role="tab" aria-controls="cotizaciones" aria-selected="false">Cotizaciones</a>
				<?php else: ?>
				<a class="nav-link disabled" id="cotizaciones-tab" data-toggle="pill" role="tab" aria-controls="cotizaciones" aria-selected="false">Cotizaciones</a>
				<?php endif; ?>
			</li>
		</ul>
	</div>
	<div class="card-body px-2">
		<div class="tab-content " id="tabServicioContent">

			<div class="tab-pane fade show active" id="requisiciones" role="tabpanel" aria-labelledby="requisiciones-tab">
				<?php
				include "vistas/modulos/requisiciones/form-section-requisiciones.php";
				?>
			</div>
			
			<div class="tab-pane fade " id="orden-trabajo" role="tabpanel" aria-labelledby="orden-trabajo-tab">
				<?php
				include "vistas/modulos/requisiciones/form-section-orden-compra.php";
				?>
			</div>
			<div class="tab-pane fade" id="cotizaciones" role="tabpanel" aria-labelledby="cotizaciones-tab">
				<?php
				if ( isset($requisicion->id) ) include "vistas/modulos/requisiciones/form-section-cotizaciones.php";
				?>
			</div>
		</div>
	</div> <!-- /.card -->
</div>