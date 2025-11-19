<?php
	if ( isset($kitMantenimiento->id) ) {
		$tipoMantenimiento = isset($old["tipoMantenimiento"]) ? $old["tipoMantenimiento"] : $kitMantenimiento->tipoMantenimiento;
		$tipoMaquinaria = isset($old["tipoMaquinaria"]) ? $old["tipoMaquinaria"] : $kitMantenimiento->tipoMaquinaria;
        $proveedor = isset($old["proveedor"]) ? $old["proveedor"] : $kitMantenimiento->proveedorId;
        $modelo = isset($old["modelo"]) ? $old["modelo"] : $kitMantenimiento->modelo;
        $observacion = isset($old["observacion"]) ? $old["observacion"] : $kitMantenimiento->observacion;

        $cantidad = isset($old["cantidad"]) ? $old["cantidad"] : "";
        $unidad = isset($old["unidad"]) ? $old["unidad"] : "";
        $numeroParte = isset($old["numeroParte"]) ? $old["numeroParte"] : "";
        $concepto = isset($old["concepto"]) ? $old["concepto"] : "";
        
	} else {
		$tipoMantenimiento = isset($old["tipoMantenimiento"]) ? $old["tipoMantenimiento"] : "";
		$tipoMaquinaria = isset($old["tipoMaquinaria"]) ? $old["tipoMaquinaria"] : "";
        $proveedor = isset($old["proveedor"]) ? $old["proveedor"] : "";
        $modelo = isset($old["modelo"]) ? $old["modelo"] : "";
        $observacion = isset($old["observacion"]) ? $old["observacion"] : "";
        $cantidad = isset($old["cantidad"]) ? $old["cantidad"] : "";
        $unidad = isset($old["unidad"]) ? $old["unidad"] : "";
        $numeroParte = isset($old["numeroParte"]) ? $old["numeroParte"] : "";
        $concepto = isset($old["concepto"]) ? $old["concepto"] : "";
	}
?>

<input type="hidden" name="_token" value="<?php echo createToken(); ?>">

<div class="row col-12">

    <div class="col-md-6">
        <div class="card card-info card-outline">

            <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-info-circle"></i>
                Información del Kit de Mantenimiento
            </h3>
            </div> <!-- /.card-header -->

            <div class="card-body row">

            <div class="col-md-12 form-group">
                <label for="tipoMantenimiento">Tipo de Mantenimiento <span class="text-danger">*</span></label>
                <textarea class="form-control text-uppercase" id="tipoMantenimiento" name="tipoMantenimiento" rows="3" placeholder="Ingresa el tipo de mantenimiento"><?php echo htmlspecialchars($tipoMantenimiento); ?></textarea>
            </div>

            <div class="col-md-6 form-group">
                <label for="tipoMaquinaria">Tipo de Maquinaria <span class="text-danger">*</span></label>
                <select class="form-control select2" id="tipoMaquinaria" name="tipoMaquinaria">
                <option value="">Seleccione un tipo de maquinaria</option>
                <?php foreach ($tiposMaquinaria as $key => $value) : ?>
                    <option value="<?php echo $value['id']; ?>" <?php echo ($tipoMaquinaria == $value['id']) ? 'selected' : ''; ?> ><?php echo $value['descripcion']; ?></option>
                <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="modelo">Modelo <span class="text-danger">*</span></label>
                <select class="form-control select2" id="modelo" name="modelo">
                <option value="">Seleccione un modelo</option>
                <?php foreach ($modelos as $key => $value) : ?>
                    <option value="<?php echo $value['id']; ?>" <?php echo ($modelo == $value['id']) ? 'selected' : ''; ?> ><?php echo $value['descripcion']; ?></option>
                <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6 form-group">
                <label for="proveedor">Proveedor (Opcional)</label>
                <select class="form-control select2" id="proveedor" name="proveedorId">
                <option value="">Seleccione una proveedor</option>
                <?php foreach ($proveedores as $key => $value) : ?>
                    <option value="<?php echo $value['id']; ?>" <?php echo ($proveedor == $value['id']) ? 'selected' : ''; ?> ><?php echo $value['proveedor']; ?></option>
                <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-12 form-group">
                <label for="observacion">Observación</label>
                <textarea class="form-control" id="observacion" name="observacion" rows="3"><?php echo $observacion; ?></textarea>
            </div>
            <p class="text-muted"><span class="text-danger">* </span>Campos obligatorios</p>
            </div>
        </div> <!-- /.card -->
    </div> <!-- /.col-md-6 -->
    
    <div class="col-md-6">
        <div class="card card-info card-outline">

            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-boxes"></i>
                    Componentes del Kit de Mantenimiento
                </h3>
            </div>

            <div class="card-body row">

                    <div class="col-sm-6 form-group">
                        <label for="cantidad">Cantidad:</label>
                        <input type="text" id="cantidad" value="<?php echo fString($cantidad); ?>" class="form-control form-control-sm campoConDecimal" placeholder="Ingresa la cantidad">
                    </div>

                    <div class="col-sm-6 form-group">
                        <label for="unidad">Unidad:</label>
                        <input type="text" id="unidad" value="<?php echo fString($unidad); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la unidad">
                    </div>

                    <div class="col-12 form-group">
                        <label for="numeroParte">Número de Parte:</label>
                        <input type="text" id="numeroParte" value="<?php echo fString($numeroParte); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número de parte">
                    </div>

                    <div class="col-12 form-group">
                        <label for="concepto">Concepto:</label>
                        <textarea id="concepto" class="form-control form-control-sm text-uppercase" rows="5" placeholder="Ingresa el concepto de la partida"><?php echo fString($concepto); ?></textarea>
                    </div>

                    <div class="col-md-12">
                        <button type="button" id="btnAgregarPartida" class="btn btn-outline-primary mb-3" >
                            <i class="fas fa-plus"></i> Agregar Partida
                        </button>
                    </div>
            </div>
        </div>
    </div>

</div> <!-- /.row -->

    <div class="row col-12">
        <div class="col-md-12">
            <div class="card card-info card-outline">

                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-boxes"></i>
                        Componentes del Kit de Mantenimiento
                    </h3>
                </div>

                <div class="card-body">
                    <table class="table table-sm table-bordered table-striped mb-0" id="tblComponentes">
                        <thead>
                            <tr>
                                <th style="width: 10px">#</th>
                                <th>Cantidad</th>
                                <th>Unidad</th>
                                <th>Número de Parte</th>
                                <th>Concepto</th>
                                <th style="width: 40px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="text-uppercase">
                            <?php if ( isset($kitMantenimiento->detalles) && count($kitMantenimiento->detalles) > 0 ) : ?>
                                <?php foreach ($kitMantenimiento->detalles as $key => $value) : ?>
                                    <tr>
                                        <td><?php echo $key + 1; ?></td>
                                        <td><?php echo $value['cantidad']; ?></td>
                                        <td><?php echo $value['unidad']; ?></td>
                                        <td><?php echo $value['numeroParte']; ?></td>
                                        <td><?php echo $value['concepto']; ?></td>
                                        <td><button type="button" class="btn btn-danger btn-sm btnEliminarComponente" data-id="<?php echo $value['id']; ?>" >
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>
                                            <button type="button" class="btn btn-warning btn-sm btnEditarComponente" 
                                                data-id="<?php echo $value['id']; ?>" 
                                                data-cantidad="<?php echo $value['cantidad']; ?>" 
                                                data-unidad="<?php echo htmlspecialchars($value['unidad']); ?>" 
                                                data-numeroparte="<?php echo htmlspecialchars($value['numeroParte']); ?>" 
                                                data-concepto="<?php echo htmlspecialchars($value['concepto']); ?>" >
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
