<?php
    if ( isset($cotizacion->id) ) {
        
    } else {

    }

?>

<input type="hidden" name="_token" value="<?php echo token() ?>">
<input type="hidden" name="requisicionId" id="requisicionId" value="<?php echo isset($cotizacion->requisicionId) ? $cotizacion->requisicionId : ''; ?>">
<div class="row">

    <div class="col-md-6">
        <label for="direccionEntrega">Direccion de Entrega</label>
        <input type="text" class="form-control form-control-sm text-uppercase" id="direccionEntrega" value="<?php echo $requisicion->direccion ?? 'No definida'; ?>" readonly>
    </div>

    <div class="col-md-3">
        <label for="fechaRequerida">Fecha Requerida</label>
        <span type="date" class="form-control form-control-sm" id="fechaRequerida" readonly><?php echo fFechaLarga($requisicion->fechaRequerida); ?></span>
    </div>

    <div class="col-md-3 form-group">
        <label for="fechaLimite">Fecha Limite de Cotizacion</label>
        <span class="form-control form-control-sm" id="fechaLimite" readonly><?php echo fFechaLarga($cotizacion->fechaLimite); ?></span>
    </div>
        
    <div class="form-group col-6  mb-1 subir-cotizaciones d-flex flex-column mt-1">
        <input type="file" class="form-control d-none" id="cotizacionArchivos" name="cotizacionArchivos[]" accept="application/pdf">

        <div class="card shadow border-0 h-100">
            <div class="card-header bg-info text-white">
                <h3 class="card-title"> <i class="fas fa-file-alt mr-2"></i>Cotizaciones</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-sm btn-light btnSubirArchivo" id="btnSubirCotizaciones" title="Subir archivo" <?php echo ($cotizacion->fechaLimite < date('Y-m-d')) ? 'disabled' : ''; ?>>
                        <i class="fas fa-upload text-info"></i>
                    </button>
                </div>
            </div>
            <div class="card-body no-scroll" style="max-height: 120px; overflow-y: auto;">
                <?php if (!empty($requisicion->cotizacionesProveedor)) : ?>
                    <?php foreach($requisicion->cotizacionesProveedor as $key=>$cotizacionArchivo) : ?>
                        <div class="card text-center ">
                            <div class="card-body">
                                <p class="text-info mb-0 text-right"><?php echo $cotizacionArchivo['archivo']; ?>
                                <i  class="ml-1 fas fa-eye verArchivo" archivoRuta="<?php echo $cotizacionArchivo['ruta']?>" style="cursor: pointer;" ></i>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <h5 class="card-title text-muted">Sin archivos disponibles</h5>
                <?php endif; ?>
            </div>
        </div>
        <span class="lista-archivos float-left">
        </span>
    </div>

    <div  class="form-group col-12">
        <h4 class="text-primary">
            <i class="fas fa-list-alt"></i> Requerimientos
        </h4>
        <table class="table table-hover text-nowrap" id="tablaRequisicionCotizaciones" width="100%">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Descripcion</th>
                    <th>Cantidad</th>
                    <th>Unidad</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($requerimientos as $key => $value): ?>
                    <tr class="text-uppercase">
                        <td><?php echo $key + 1; ?></td>
                        <td><?php echo $value["concepto"]; ?></td>
                        <td><?php echo $value["cantidad"]; ?></td>
                        <td><?php echo $value["unidad"]; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
