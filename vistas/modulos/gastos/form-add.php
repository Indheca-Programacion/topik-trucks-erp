<form action="/file-upload" method="post" id="formAddPartida" enctype="multipart/form-data">
    <div class="row">
        <!-- Fecha de facturacion -->
        <div class="col-md-6 form-group">
            <label for="fecha">Fecha <?php echo $tipoGasto == 1 ? ' de Facturacion:' : '';?></label>
            <input type="text" class="form-control form-control-sm datetimepicker-input" id="datetimepicker3" name="fecha" data-toggle="datetimepicker" data-target="#datetimepicker3"/>
        </div>
        <!-- Tipo de Gasto -->
        <div class="col-md-6">
            <label for="tipo">Tipo de Gasto:</label>
            <select class="custom-select select2" id="tipo" name="tipoGasto">
                <option value="" >Selecciona un tipo de gasto</option>
                <option value="1" >Herrmienta Menor</option>
                <option value="2" >Mantto. Preventivo</option>
                <option value="3" >Mantto. Correctivo</option>
                <option value="4" >Material de Limpieza</option>
                <option value="5" >Material Primeros Auxilios</option>
                <option value="6" >Mobiliario de Oficina</option>
                <option value="7" >Gastos Generales</option>
                <option value="8" >Materiales</option>
            </select>
        </div>
        <!-- No. Economico -->
        <div class="col-md-6 form-group">
            <label for="maquinariaId">Número Económico:</label>
            <select name="maquinaria" id="maquinariaId" class="custom-select form-controls select2">
                <option value="">Selecciona un Número Económico</option>
                <?php foreach($maquinarias as $maquinaria) { ?>
                <option value="<?php echo $maquinaria["id"]; ?>"
                    
                    ><?php echo mb_strtoupper(fString($maquinaria["numeroEconomico"])); ?>
                </option>
                <?php } ?>
            </select>

        </div>
        <!-- SERIE -->
        <div class="col-md-6 form-group">
            <label for="maquinariaSerie">Serie:</label>
            <input type="text" id="maquinariaSerie" class="form-control form-control-sm text-uppercase" readonly>
        </div>
        <!-- TIPO MAQUINARIA -->
        <div class="col-md-6 form-group">
            <label for="maquinariaTipoDescripcion">Tipo de Maquinaria:</label>
            <input type="text" id="maquinariaTipoDescripcion" class="form-control form-control-sm text-uppercase" readonly>
        </div>
        <!-- UBICACION -->
        <div class="col-md-6 form-group">
            <label for="maquinariaUbicacionDescripcion">Ubicación:</label>
            <select name="ubicacion" id="ubicacionId" class="custom-select form-controls select2">
                <option value="">Selecciona Ubicacion</option>
                <?php foreach($ubicaciones as $ubicacion) { ?>
                    <option value="<?php echo $ubicacion["id"]; ?>"
                        ><?php echo mb_strtoupper(fString($ubicacion["descripcion"])); ?>
                    </option>
                <?php } ?>
            </select>
        </div>
        <!-- OBRA -->
        <div class="col-md-6 form-group">
            <label for="obra">Obra:</label>
            <select id="obraPartida" name="obra" class="custom-select form-controls select2">
                <option value="">Selecciona una obra</option>
                <?php foreach($obras as $obra) { ?>
                    <option value="<?php echo $obra["id"]; ?>" >
                    <?php echo mb_strtoupper(fString($obra["descripcion"])); ?>
                </option>
                <?php } ?>
            </select>
        </div>
        <!-- SOLICITO -->
        <div class="col-md-6 form-group">
            <label for="solicito">Solicito:</label>
            <input type="text" name="solicito" id="solicito" class="form-control form-control-sm text-uppercase" placeholder="Ingresa la persona que solicito">
        </div>
        <!-- NUMERO DE PARTE -->
        <div class="col-md-6 form-group">
            <label for="numeroParte">Número de Parte:</label>
            <input type="text" name="numeroParte" id="numeroParte" class="form-control form-control-sm text-uppercase" placeholder="Ingresa el número de parte">
        </div>
        <!-- COSTO -->
        <div class="col-md-6 form-group">
            <label for="costo">Costo c/iva</label>
            <input type="text" id="costo" value="0" maxlength="10" name="costo" class="form-control form-control-sm campoConDecimal">
        </div>
        <!-- CANTIDAD -->
        <div class="col-md-6 form-group">
            <label for="cantidad">Cantidad</label>
            <input type="text" id="cantidad" maxlength="10" value="0" name="cantidad" class="form-control form-control-sm campoConDecimal">
        </div>
        <!-- UNIDAD -->
        <div class="col-md-6 form-group">
            <label for="unidad">Unidad: </label>
            <input type="text" id="unidad" name="unidad" class="form-control form-control-sm text-uppercase">
        </div>
        <!-- PROVEEDOR -->
        <div class="col-md-6 form-group">
            <label for="proveedor">Proveedor: </label>
            <input type="text" id="proveedor" name="proveedor" class="form-control form-control-sm text-uppercase">
        </div>
        <!-- FACTURA -->
        <div class="col-md-6 form-group">
            <label for="factura">Factura</label>
            <input type="text" id="factura" name="factura" class="form-control form-control-sm text-uppercase">
        </div>
        <!-- OBSERVACIONES -->
        <div class="col-12 form-group">
            <label for="observaciones">Observaciones:</label>
            <textarea name="observaciones" id="observaciones" class="form-control form-control-sm text-uppercase" rows="4"></textarea>
        </div>
    </div>
    <!-- Boton de Facturas -->
    <div class="row">
        <div class="col subir-archivos">
            <button type="button" class="btn btn-info float-left" id="btnSubirArchivos">
                <i class="fas fa-folder-open"></i> Cargar Facturas
            </button>
            <span class="lista-archivos">
            </span>
		    <input type="file" class="form-control form-control-sm d-none" id="archivo" multiple>
        </div>
        <div class="col d-flex justify-content-end">
            <button type="button" id="btnAddPartida" class="btn btn-outline-primary">
                <i class="fas fa-plus"></i> Añadir Partida
            </button>
        </div>
    </div>
</form>