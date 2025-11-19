<?php 
    $obraId = isset($old['obraId']) ? $old['obraId'] : '';
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
                <label for="obra" class="text-capitalize">Hola. <?php echo $usuarioAutenticado->nombre; ?> ¿Los pagos que desea cargar son para requisiciones u ordenes de compra?</label>
                <select id="categoria" class="form-control select2" required>
                    <option value="1">Requisiciones</option>
                    <option value="2">Ordenes de Compra</option>
                </select>
            </div> <!-- /.form-group -->
        </div> <!-- /.col-md-6 -->
    </div> <!-- /.row -->

    <div class="row d-none" id="formulario-step-2">
        <div class="col-md-6">

            <span class="text-muted"> Se cargara el pago y actualizara el estatus de las siguientes ordenes  / requisiciones</span>
        </div>
        
        <div class="col-12 form-group mt-2">
            <div class="table-responsive" id="tablaRequisiciones_wrapper">
                <table class="table table-sm table-bordered table-striped mb-0" id="tablaRequisiciones" width="100%">
                    <thead>
                        <tr>
                            <th class="text-right" style="width: 10px;">Partida</th>
                            <th>Folio</th>
                            <th>Estatus Actual</th>
                            <th>Subir Pago</th>
                            <th>Nuevo Estatus</th>
                            <th>Empresa</th>
                        </tr>
                    </thead>
                    <tbody class="text-uppercase">
                        <?php foreach($requisiciones as $key => $requisicion) : ?>
                            <tr>
                                <td class="text-right align-middle" style="width: 10px;"><?php echo $key + 1; ?></td>
                                <td class="align-middle"><?php echo $requisicion['folio']; ?> <button type="button" class="btn btn-link p-0" data-id="<?php echo $requisicion['id']; ?>" data-toggle="modal" data-target="#modalVerRequisicion"><i class="fas fa-eye"></i></button></td>
                                <td class="align-middle"><?php echo $requisicion['servicio_estatus.descripcion']; ?></td>
                                <td class="align-middle">
                                    <input type="file" name="pagoArchivo[<?php echo $key; ?>]" id="pagoArchivo_<?php echo $key; ?>" class="form-control form-control-sm" required accept="application/pdf">
                                    <input type="hidden" name="requisicionId[<?php echo $key; ?>]" value="<?php echo $requisicion['id']; ?>">
                                    <button type="button" class="btn btn-link p-0" onclick="document.getElementById('pagoArchivo_<?php echo $key; ?>').value = ''; $(this).closest('tr').find('select').prop('disabled', true);">Vaciar archivos</button>
                                </td>
                                <td class="align-middle"><select name="nuevoEstatus[<?php echo $key; ?>]" id="nuevoEstatus_<?php echo $key; ?>" disabled class="form-control form-control-sm">
                                    <option value="5">Pagada</option>
                                    <option value="14">Pagado Parcial</option>
                                </td>
                                <td class="align-middle"><?php echo $requisicion['empresas.nombreCorto']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="table-responsive d-none" id="tablaOrdenes_wrapper">
                <table class="table table-sm table-bordered table-striped mb-0" id="tablaOrdenes" width="100%">
                    <thead>
                        <tr>
                            <th class="text-right" style="width: 10px;">Partida</th>
                            <th>Folio</th>
                            <th>Estatus Actual</th>
                            <th>Subir Pago</th>
                            <th>Nuevo Estatus</th>
                            <th>Empresa</th>
                        </tr>
                    </thead>
                    <tbody class="text-uppercase">
                        <?php foreach($ordenes as $key => $orden) : ?>
                            <tr>
                                <td class="text-right align-middle" style="width: 10px;"><?php echo $key + 1; ?></td>
                                <td class="align-middle"><?php echo $orden['folio']; ?> <button type="button" class="btn btn-link p-0" data-id="<?php echo $orden['id']; ?>" data-toggle="modal" data-target="#modalVerOrdenes"><i class="fas fa-eye"></i></button></td>
                                <td class="align-middle"><?php echo $orden['estatus.descripcion']; ?></td>
                                <td class="align-middle">
                                    <input type="file" name="pagoArchivo[<?php echo $key; ?>]" id="pagoArchivoOrden_<?php echo $key; ?>" class="form-control form-control-sm" required accept="application/pdf">
                                    <input type="hidden" name="ordenId[<?php echo $key; ?>]" value="<?php echo $orden['id']; ?>">
                                    <button type="button" class="btn btn-link p-0" onclick="document.getElementById('pagoArchivoOrden_<?php echo $key; ?>').value = ''; $(this).closest('tr').find('select').prop('disabled', true);">Vaciar archivos</button>
                                </td>
                                <td class="align-middle"><select name="nuevoEstatus[<?php echo $key; ?>]" id="nuevoEstatus_<?php echo $key; ?>" disabled class="form-control form-control-sm">
                                    <option value="4">Pagada</option>
                                    <option value="13">Pagado Parcial</option>
                                </td>
                                <td class="align-middle"><?php echo $requisicion['empresas.nombreCorto']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div> <!-- /.row -->

    <div class="row d-none" id="formulario-step-3">

        <div class="col-md-12">
            <h5 class="text-success"><i class="fas fa-check-circle"></i> ¡Listo para subir los Pagos!</h5>
            <p class="text-muted">Revise que toda la información sea correcta antes de subir los pagos.</p>
            <div id="resumenRequisicion" class="form-group"></div>

        </div> <!-- /.col-md-12 -->

    </div>

    <button type="button" class="btn btn-secondary d-none" id="btnAnterior">
        <i class="fas fa-arrow-left"></i> Anterior
    </button>

    <button type="button" class="btn btn-primary" id="btnSiguiente">
        <i class="fas fa-arrow-right"></i> Siguiente
    </button>

    <button type="button" id="btnSubirPagos" class="btn btn-outline-primary d-none" >
        <i class="fas fa-plus"></i> Subir pagos
    </button>

</div>

<div id="terminacionRequisicion" class="d-none">
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Pagos subidos exitosamente!</h4>
        <p>Los pagos han sido subidos.</p>
        <hr>
    </div>

</div>