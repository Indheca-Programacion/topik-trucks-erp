<?php
use App\Route;
    if ( isset($gastos->id) ) {
		$tipoGasto = isset($old["tipoGasto"]) ? $old["tipoGasto"] : $gastos->tipoGasto;
		$obraId = isset($old["obra"]) ? $old["obra"] : $gastos->obra;
		$banco = isset($old["banco"]) ? $old["banco"] : $gastos->banco;
		$cuenta = isset($old["cuenta"]) ? $old["cuenta"] : $gastos->cuenta;
		$clave = isset($old["clave"]) ? $old["clave"] : $gastos->clave;
		$encargado = isset($old["encargado"]) ? $old["encargado"] : $gastos->encargado;
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : $gastos->fecha_inicio;
		$fecha_fin = isset($old["fecha_fin"]) ? $old["fecha_fin"] : $gastos->fecha_fin;
		$fecha_envio = isset($old["fecha_envio"]) ? $old["fecha_envio"] : $gastos->fecha_envio;
		$empresaId = isset($old["empresa"]) ? $old["empresa"] : $gastos->empresa;
        $requisicion = $gastos->requisicionId;
        $folio = $requisiciones->folio;
	} else {
		$tipoGasto = isset($old["tipoGasto"]) ? $old["tipoGasto"] : "";
		$banco = isset($old["banco"]) ? $old["banco"] : "";
		$obraId = isset($old["obra"]) ? $old["obra"] : "";
		$cuenta = isset($old["cuenta"]) ? $old["cuenta"] : "";
		$clave = isset($old["clave"]) ? $old["clave"] : "";
		$encargado = isset($old["encargado"]) ? $old["encargado"] : usuarioAutenticado()["id"];
		$fecha_inicio = isset($old["fecha_inicio"]) ? $old["fecha_inicio"] : "0000-00-00";
		$fecha_fin = isset($old["fecha_fin"]) ? $old["fecha_fin"] : "0000-00-00";
		$fecha_envio = isset($old["fecha_envio"]) ? $old["fecha_envio"] : "0000-00-00";
		$empresaId = isset($old["empresa"]) ? $old["empresa"] : "";
        $requisicion = null;
        $requisicion = null;
        $periodos = '';
	}
?>

<input type="hidden" id="_token" name="_token" value="<?php echo createToken(); ?>">
<input type="hidden" id="gastoId" value="<?= $gastos->id ?>">

<div class="row">
    <!-- EMPRESA -->
    <div class="col-md-6 form-group">
        <label for="empresa">Empresa:</label>
        <select id="empresa" name="empresa" class="custom-select form-controls select2"  <?php if(isset($gastos->id))echo ' disabled' ?>>
            <option value="">Selecciona una empresa</option>
            <?php foreach($empresas as $empresa) { ?>
				<option value="<?php echo $empresa["id"]; ?>" 
				<?php echo $empresaId == $empresa["id"] ? ' selected' : ''; ?>
				>
                <?php echo mb_strtoupper(fString($empresa["nombreCorto"])); ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <!-- OBRA -->
    <div class="col-md-6 form-group">
        <label for="obra">Obra:</label>
        <select id="obra" name="obra" class="custom-select form-controls select2"  <?php if(isset($gastos->id))echo ' disabled' ?>>
            <option value="">Selecciona una obra</option>
            <?php foreach($obras as $obra) { ?>
				<option value="<?php echo $obra["id"]; ?>" 
				<?php echo $obraId == $obra["id"] ? ' selected' : ''; ?>
				>
                <?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <!-- TIPO DE GASTO -->
    <div class="col-md-6 form-group">
        <label for="tipoGasto">Tipo de Gasto:</label>
        <select name="tipoGasto" id="tipoGasto" class="custom-select select2" <?php if(isset($gastos->id)) echo 'disabled'; ?>>
            <option value="">Seleccione un tipo</option>
            <option value="1" <?php echo $tipoGasto == 1 ? ' selected' : ''; ?>>Deducible</option>
            <option value="2" <?php echo $tipoGasto == 2 ? ' selected' : ''; ?>>No Deducible</option>
        </select>
    </div>
    <!-- ENCARGADO -->
    <div class="col-md-6 form-group">
        <label for="encargado">Encargado:</label>
        <select name="encargado" id="encargado" class="custom-select select2">
            <option value="">Selecciona un Encargado</option>
            <?php foreach($usuarios as $usuario) { ?>
            <option value="<?php echo $usuario["id"]; ?>"
            <?php echo $encargado == $usuario["id"] ? ' selected' : ''; ?>>
            <?php echo mb_strtoupper(fString($usuario["nombreCompleto"])); ?>
            </option>
            <?php } ?>
        </select>
    </div>
    <!-- FECHA INICIO -->
    <div class="col-md-6 form-group">
        <label for="fecha_inicio">Fecha de Inicio:</label>
        <input <?php if(isset($gastos->id)) echo 'disabled'; ?>  type="text" class="form-control form-control-sm datetimepicker-input" id="datetimepicker5" name="fecha_inicio" value="<?= $fecha_inicio ?>" data-toggle="datetimepicker" data-target="#datetimepicker5"/>
    </div>
    <!-- FECHA FINALIZACION -->
    <?php if( isset($gastos->id) ): ?>
        <div class="col-md-6 form-group">
            <label for="fecha_fin">Fecha de Finalizacion:</label>
            <input disabled type="text" class="form-control form-control-sm datetimepicker-input" id="datetimepicker1" name="fecha_fin" value="<?= $fecha_fin ?>" data-toggle="datetimepicker" data-target="#datetimepicker1"/>
        </div>
    <?php endif ?>
    <!-- BANCO -->
    <div class="col-md-6 form-group">
        <label for="banco">Banco:</label>
        <input type="text" id="banco" name="banco" value="<?php echo mb_strtoupper(fString($banco)); ?>" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el banco">
    </div>
    <!-- CUENTA -->
    <div class="col-md-6 form-group">
        <label for="cuenta">Cuenta:</label>
        <input type="text" id="cuenta" maxlength="10" name="cuenta" value="<?php echo $cuenta; ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el numero de cuenta" max="10">
    </div>
    <!-- CLABE -->
    <div class="col-md-6 form-group">
        <label for="clave">Clabe Interbancaria:</label>
        <input type="text" id="clave" maxlength="18" name="clave" value="<?php echo $clave; ?>" class="form-control form-control-sm text-uppercase campoSinDecimal" placeholder="Ingresa el numero de clave" max="18">
    </div>
</div>
<?php if( !is_null($requisicion) ): ?>
    <div class="row">
        <div class="col-md-6 form-group">
            <label for="">Folio de Requisicion:</label>
            <a href="<?= Route::names('requisicion-gastos.edit', $requisiciones->id); ?>" target="_blank"><span type="text" class="form-control form-control-sm text-uppercase"><?php echo $folio ?> </a></span>
        </div>
    </div>
<?php endif ?>