<?php
	if ( isset($presupuesto->id) ) {
		$nombre = isset($old["nombre"]) ? $old["nombre"] : $presupuesto->nombre;
	} else {
		$empresaId = isset($old["empresaId"]) ? $old["empresaId"] : "";
		$servicioCentroId = isset($old["servicioCentroId"]) ? $old["servicioCentroId"] : "";
		$numero = "";
		$folio = isset($old["folio"]) ? $old["folio"] : "";
		$maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : "";
		$ubicacionId = isset($old["ubicacionId"]) ? $old["ubicacionId"] : "";
		$obraId = isset($old["obraId"]) ? $old["obraId"] : "";
		// $horoOdometro = isset($old["horoOdometro"]) ? $old["horoOdometro"] : "0.0";
		$mantenimientoTipoId = isset($old["mantenimientoTipoId"]) ? $old["mantenimientoTipoId"] : "";
		$servicioTipoId = isset($old["servicioTipoId"]) ? $old["servicioTipoId"] : "";
		$servicioEstatusId = isset($old["servicioEstatusId"]) ? $old["servicioEstatusId"] : "";
		$solicitudTipoId = isset($old["solicitudTipoId"]) ? $old["solicitudTipoId"] : "";
		$horasProyectadas = isset($old["horasProyectadas"]) ? $old["horasProyectadas"] : "0.00";
		$horasReales = "";
		$fechaSolicitud = isset($old["fechaSolicitud"]) ? $old["fechaSolicitud"] : fFechaLarga(date("Y-m-d"));
		// $fechaSolicitud = fFechaLarga(date("Y-m-d"));
		$fechaProgramacion = isset($old["fechaProgramacion"]) ? $old["fechaProgramacion"] : "";
		$fechaFinalizacion = isset($old["fechaFinalizacion"]) ? $old["fechaFinalizacion"] : "";
		$descripcion = isset($old["descripcion"]) ? $old["descripcion"] : "";

		// Datos de la Maquinaria
		$maquinariaTipoDescripcion = isset($old["maquinariaTipoDescripcion"]) ? $old["maquinariaTipoDescripcion"] : "";
		$maquinariaUbicacionDescripcion = isset($old["maquinariaUbicacionDescripcion"]) ? $old["maquinariaUbicacionDescripcion"] : "";
		$maquinariaMarcaDescripcion = isset($old["maquinariaMarcaDescripcion"]) ? $old["maquinariaMarcaDescripcion"] : "";
		$maquinariaModeloDescripcion = isset($old["maquinariaModeloDescripcion"]) ? $old["maquinariaModeloDescripcion"] : "";
		$maquinariaDescripcion = isset($old["maquinariaDescripcion"]) ? $old["maquinariaDescripcion"] : "";
		$maquinariaSerie = isset($old["maquinariaSerie"]) ? $old["maquinariaSerie"] : "";
	}
?>
<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" id="periodo" name="periodo" value="<?php echo date('W'); ?>">

<!-- Stepper -->
<div class="bs-stepper-header" role="tablist">
    <div class="step">
        <button type="button" class="step-trigger active" id="stepper1trigger1">
            <span class="bs-stepper-circle">1</span>
            <span class="bs-stepper-label">Obra</span>
        </button>
    </div>
    <div class="bs-stepper-line"></div>
    <div class="step">
        <button type="button" class="step-trigger" id="stepper1trigger2">
            <span class="bs-stepper-circle">2</span>
            <span class="bs-stepper-label">Detalles</span>
        </button>
    </div>
    <div class="bs-stepper-line"></div>
    <div class="step">
        <button type="button" class="step-trigger" id="stepper1trigger3">
            <span class="bs-stepper-circle">3</span>
            <span class="bs-stepper-label">Finalizar</span>
        </button>
    </div>
</div>

<style>
    .bs-stepper-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }
    .bs-stepper-line {
        flex: 1 1 0%;
        height: 2px;
        background: #dee2e6;
        margin: 0 8px;
    }
    .step-trigger {
        background: none;
        border: none;
        outline: none;
        padding: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        color: #6c757d;
        font-weight: 500;
    }
    .step-trigger.active,
    .step-trigger:focus,
    .step-trigger:hover {
        color: #0d6efd;
    }
    .bs-stepper-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 2.2rem;
        height: 2.2rem;
        border-radius: 50%;
        background: #dee2e6;
        color: #6c757d;
        font-weight: bold;
        margin-bottom: 0.25rem;
        font-size: 1.1rem;
        transition: background 0.2s, color 0.2s;
    }
    .step-trigger.active .bs-stepper-circle {
        background: #0d6efd;
        color: #fff;
    }
</style>

<div id="creacionRequisicion">

    <div class="row" id="formulario-step-1">
        <div class="col-md-6">
            <div class="form-group">
                <label for="obra" class="text-capitalize">Hola. <?php $usuarioAutenticado->nombre; ?> ¿El presupuesto es para una equipo registrado?</label>
				<div class="input-group">
					<select name="maquinariaId" id="maquinariaId" class="form-control select2" required>
						<option value="">Seleccione una maquinaria</option>
						<?php foreach ($maquinarias as $maquinaria): ?>
							<option value="<?= $maquinaria["id"] ?>"><?= $maquinaria["descripcion"] ?></option>
						<?php endforeach; ?>
					</select>
					<div class="input-group-append">
						<button type="button" class="btn btn-primary" id="btnAgregarMaquinaria" data-toggle="modal" data-target="#modalAgregarMaquinaria">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>
            </div> <!-- /.form-group -->
        </div> <!-- /.col-md-6 -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="clienteId">¿Es un cliente registrado?</label>
				<div class="input-group">
					<select id="clienteId" class="form-control select2">
						<option value="">Seleccione un cliente</option>
						<?php foreach ($clientes as $cliente): ?>
							<option value="<?= $cliente["id"] ?>"><?= $cliente["nombreCompleto"] ?></option>
						<?php endforeach; ?>
					</select>
					<div class="input-group-append">
						<button type="button" class="btn btn-primary" id="btnAgregarCliente" data-toggle="modal" data-target="#modalAgregarCliente">
							<i class="fas fa-plus"></i>
						</button>
					</div>
				</div>
            </div> <!-- /.form-group -->
        </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->

    <div class="row d-none" id="formulario-step-2">

		<div class="col">
            <div class="accordion" id="accordionLevantamientos">
                <div class="card servicios-levantamiento">
                    <div class="card-header" id="headingLevantamiento1">
                        <h2 class="mb-0">
                            <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseLevantamiento1" aria-expanded="true" aria-controls="collapseLevantamiento1">
                                Levantamiento Reparacion / Servicio 1
                            </button>
                        </h2>
                    </div>
                    <div id="collapseLevantamiento1" class="collapse show" aria-labelledby="headingLevantamiento1" data-parent="#accordionLevantamientos">
                        <div class="card-body">
                            <?php include "vistas/modulos/servicios/formulario-modal.php"; ?>
                        </div>
                    </div>
                </div>
            </div>
		</div>

    </div> <!-- /.row -->

    <div class="row d-none" id="formulario-step-3">

		<span class="font-weight-bold">Finalizar presupuesto</span>
		<table class="table table-bordered" id="tablaServiciosPresupuesto">
			<thead>
				<tr>
					<th>#</th>
					<th>Falla / Servicio</th>
				</tr>
			</thead>
			<tbody>

			</tbody>
		</table>

		<div class="form-group">
			<label for="fuente">Fuente:</label>
			<input type="text" class="form-control" id="fuente" name="fuente" placeholder="Fuente del presupuesto">
		</div>
    </div>

    <button type="button" class="btn btn-secondary d-none" id="btnAnterior">
        <i class="fas fa-arrow-left"></i> Anterior
    </button>

    <button type="button" class="btn btn-primary" id="btnSiguiente">
        <i class="fas fa-arrow-right"></i> Siguiente
    </button>

    <button type="button" id="btnAgregarServicio" class="btn btn-outline-success d-none" >
        <i class="fas fa-save"></i> Continuar Levantamiento
    </button>

    <button type="button" id="btnCrearPresupuesto" class="btn btn-outline-primary d-none" >
        <i class="fas fa-plus"></i> Crear presupuesto
    </button>

</div>

<div id="terminacionPresupuesto" class="d-none">
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Presupuesto creada exitosamente!</h4>
        <p>El presupuesto ha sido creado y se encuentra en proceso de aprobación.</p>
        <hr>
        <p class="mb-0">Puede revisar el estado del presupuesto <a id="presupuestoLink" href="#"> Aquí</a>.</p>
    </div>

</div>