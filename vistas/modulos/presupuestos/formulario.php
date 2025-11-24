<?php
    $maquinariaId = isset($old["maquinariaId"]) ? $old["maquinariaId"] : $presupuesto->maquinariaId;
    $clienteId = isset($old["clienteId"]) ? $old["clienteId"] : $presupuesto->clienteId;
    $fuente = isset($old["fuente"]) ? $old["fuente"] : $presupuesto->fuente;
?>

<div class="row">
    <div class="col-md-6">
        <div class="card card-success card-outline">
            <div class="card-header">
                <h3 class="card-title">Información del Presupuesto</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
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
                    <div class="col-md-6">
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
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="fuente">Fuente</label>
                            <input type="text" name="fuente" id="fuente" class="form-control form-control-sm text-uppercase" value="<?=  $fuente ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card card-warning card-outline">
            <div class="card-header">
                <h3 class="card-title">Servicios del Presupuesto</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Descripción</th>
                            <th>Estatus</th>
                            <th>Costo</th>
                        </tr>
                    </thead>
                    <tbody class="text-uppercase">
                        <?php
                            if ( isset($serviciosPresupuesto) && count($serviciosPresupuesto) > 0 ) :
                                foreach ( $serviciosPresupuesto as $servicio ) :
                        ?>
                            <tr>
                                <td><?= $servicio["descripcion"] ?></td>
                                <td><?= $servicio["servicioEstatus"] ?></td>
                                <td>$ 0</td>
                                <!-- <td>$ <?= number_format( $servicio["costo"], 2 ) ?></td> -->
                            </tr>
                        <?php
                                endforeach;
                            else :
                        ?>
                            <tr>
                                <td colspan="2" class="text-center">No hay servicios agregados</td>
                            </tr>
                        <?php
                            endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>