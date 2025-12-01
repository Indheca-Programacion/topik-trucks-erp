<?php
    $maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : $presupuesto->maquinariaId;
    $clienteId = isset($old["clienteId"]) ? $old["clienteId"] : $presupuesto->clienteId;
    $fuente = isset($old["fuente"]) ? $old["fuente"] : $presupuesto->fuente;

    use App\Route;
?>

<div class="row">
    <div class="col-md-2">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">Información del Presupuesto</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="maquinaria_id">Maquinaria <span class="text-danger">*</span></label>
                            <select name="maquinaria_id" id="maquinaria_id" class="form-control select2" style="width: 100%;">
                                <option value="">Seleccionar maquinaria</option>
                                <?php foreach ( $maquinarias as $maquinaria ) : ?>
                                    <option value="<?= $maquinaria["id"] ?>" <?= ( isset($presupuesto) && $maquinariaId == $maquinaria["id"] ) ? 'selected' : '' ?> >
                                        <?= $maquinaria["numeroEconomico"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="clienteId">Cliente</label>
                            <select name="clienteId" id="clienteId" class="form-control select2" style="width: 100%;">
                                <option value="">Seleccionar cliente</option>
                                <?php foreach ( $clientes as $cliente ) : ?>
                                    <option value="<?= $cliente["id"] ?>" <?= ( isset($presupuesto) && $clienteId == $cliente["id"] ) ? 'selected' : '' ?> >
                                        <?= $cliente["nombreCompleto"] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="fuente">Fuente</label>
                            <input type="text" name="fuente" id="fuente" class="form-control form-control-sm text-uppercase" value="<?=  $fuente ?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="totalPresupuesto">Total Presupuesto</label>
                            <input type="text" class="form-control form-control-sm text-uppercase" value="$ <?= isset($totalPresupuesto) ? number_format($totalPresupuesto, 2) : '0.00' ?>" readonly>
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-10">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">Servicios del Presupuesto</h3>
            </div>
            <div class="card-body">
                <div class="accordion" id="accordionServicios">
                    <?php
                        if ( isset($serviciosPresupuesto) && count($serviciosPresupuesto) > 0 ) :
                            foreach ( $serviciosPresupuesto as $key => $servicio ) :
                    ?>
                        <div class="card">
                            <div class="card-header" id="heading<?= $key ?>">
                                <h2 class="mb-0">
                                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?= $key ?>" aria-expanded="<?= $key === 0 ? 'true' : 'false' ?>" aria-controls="collapse<?= $key ?>">
                                        Servicio <?= $servicio["id"] ?> - <?= $servicio["descripcion"] ?>
                                    </button>
                                </h2>
                            </div>
                            <div id="collapse<?= $key ?>" class="collapse <?= $key === 0 ? 'show' : '' ?>" aria-labelledby="heading<?= $key ?>" data-parent="#accordionServicios">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped table-sm text-uppercase">
                                            <thead>
                                                <tr>
                                                    <th>Partida</th>
                                                    <th>Cantidad</th>
                                                    <th>Unidad</th>
                                                    <th>Descripción</th>
                                                    <th>Costo Base</th>
                                                    <th>Costo Total</th>
                                                    <th>Logistica (%)</th>
                                                    <th>Mantenimiento (%)</th>
                                                    <th>Utilidad (%)</th>
                                                    <th>Precio Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if ( !isset( $servicio["partidas"] ) || count( $servicio["partidas"] ) === 0 ) : ?>
                                                    <tr>
                                                        <td colspan="10" class="text-center">No hay partidas agregadas</td>
                                                    </tr>
                                                <?php else : ?>
                                                    <?php
                                                        foreach ( $servicio["partidas"] as $key => $partida ) :
                                                    ?>
                                                        <tr>
                                                            <td><?= $key + 1 ?> <button type="button" class="btn btn-danger btn-sm eliminarPartida" data-id="<?= $partida["id"] ?>"><i class="fas fa-trash-alt"></i></button></td>
                                                            <td><?= $partida["cantidad"] ?></td>
                                                            <td><?= $partida["unidad"] ?></td>
                                                            <td><?= $partida["descripcion"] ?></td>
                                                            <td>$ <?= number_format($partida["costo_base"], 2) ?></td>
                                                            <td>$ <?= number_format($partida["costoTotal"], 2) ?></td>
                                                            <td>$ <?= number_format($partida["logistica"], 2) ?></td>
                                                            <td>$ <?= number_format($partida["mantenimiento"], 2) ?></td>
                                                            <td>$ <?= number_format($partida["utilidad"], 2) ?></td>
                                                            <td>$ <?= number_format($partida["precioTotal"], 2) ?></td>
                                                        </tr>
                                                    <?php
                                                        endforeach;
                                                    ?>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="9" class="text-right">Subtotal:</th>
                                                    <th class="text-left">$ <?= number_format($servicio["subtotal"], 2) ?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="9" class="text-right">Comisiones:</th>
                                                    <th class="text-left">$ <?= number_format($servicio["comisiones"], 2) ?></th>
                                                </tr>
                                                <tr>
                                                    <th colspan="9" class="text-right">Total:</th>
                                                    <th class="text-left">$ <?= number_format($servicio["total"], 2) ?></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                    <button type="button" class="btn btn-success btnAgregarPartida" data-servicio-id="<?= $servicio["id"] ?>" data-toggle="modal" data-target="#modalAgregarPartida"><i class="fas fa-plus"></i> Agregar Partida</button>
                                    <a href="<?= Route::names('servicios.edit', $servicio["id"]) ?>" class="btn btn-info" target="_blank"><i class="fas fa-eye"></i> Ver Servicio</a>
                                </div>
                            </div>
                        </div>
                    <?php
                            endforeach;
                        else :
                    ?>
                        <div class="alert alert-info">No hay servicios agregados</div>
                    <?php
                        endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>