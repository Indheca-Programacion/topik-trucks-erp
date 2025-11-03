<div class="row">

	<div class="col-md-8">

		<div class="card card-info card-outline">

            <div class="card-body">

                <div class="box-body">    
                    
                    <div class="row">

                        <div class="col">

                            <div class="row">

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <input type="checkbox" name="fugas" <?php echo $fugas == 1 ? 'checked' : ''; ?>>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control-sm text-uppercase" placeholder="Fugas" disabled>
                                        </div> <!-- <div class="input-group"> -->
                                    </div> <!-- <div class="form-group"> -->

                                </div> <!-- <div class="col-md-6"> -->

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <input type="checkbox" name="transmision" <?php echo $transmision == 1 ? 'checked' : ''; ?>>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control-sm text-uppercase" placeholder="Trasmisión" disabled>
                                        </div> <!-- <div class="input-group"> -->
                                    </div> <!-- <div class="form-group"> -->
                                        
                                </div> <!-- <div class="col-md-6"> -->

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <input type="checkbox" name="sistema" <?php echo $sistema == 1 ? 'checked' : ''; ?>>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control-sm text-uppercase" placeholder="Sistema hco." disabled>
                                        </div> <!-- <div class="input-group"> -->
                                    </div> <!-- <div class="form-group"> -->
                                        
                                </div> <!-- <div class="col-md-6"> -->
                                
                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <input name='motor' type="checkbox" <?php echo $motor == 1 ? 'checked' : ''; ?>>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control-sm text-uppercase" placeholder="Motor" disabled>
                                        </div> <!-- <div class="input-group"> -->
                                    </div> <!-- <div class="form-group"> -->
                                        
                                </div> <!-- <div class="col-md-6"> -->

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <input name='pintura' type="checkbox" <?php echo $pintura == 1 ? 'checked' : ''; ?>>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control-sm text-uppercase" placeholder="Pintura" disabled>
                                        </div> <!-- <div class="input-group"> -->
                                    </div> <!-- <div class="form-group"> -->
                                        
                                </div> <!-- <div class="col-md-6"> -->

                                <div class="col-md-6">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text">
                                                <input name='seguridad' type="checkbox" <?php echo $seguridad == 1 ? 'checked' : ''; ?>>
                                                </span>
                                            </div>
                                            <input type="text" class="form-control-sm text-uppercase" placeholder="Seguridad" disabled>
                                        </div> <!-- <div class="input-group"> -->
                                    </div> <!-- <div class="form-group"> -->
                                        
                                </div> <!-- <div class="col-md-6"> -->

                            </div> <!-- <div class="row"> -->

                        </div> <!-- <div class="col"> -->

                        <div class="col-md-12">

                            <div class="accordion" id="accordionExample">
                                <?php foreach ($arrayEvidencias as $key => $value) { ?>
                                    <div class="card">
                                        <div class="card-header" id="heading<?= $key?>">
                                            <h2 class="mb-0">
                                                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapse<?= $key?>" aria-expanded="true" aria-controls="collapse<?= $key?>">
                                                <?= $key ?>
                                                </button>
                                            </h2>
                                        </div>

                                        <div id="collapse<?= $key?>" class="collapse" aria-labelledby="heading<?= $key?>" data-parent="#accordionExample">
                                            <div class="card-body">
                                                <div class="row">
                                                <?php foreach ($value as $key2 => $value2) { ?>
                                                    <div class="card col-6">
                                                        <img src="<?= $value2['ruta'] ;?>" class="card-img-top" alt="Responsive image">
                                                        <div class="card-body">
                                                            <p class="card-text text-center"><?php echo fFechaLarga($value2["fechaCreacion"]) ?></p>
                                                            <button type="button" class="btn btn-danger btn-sm btnEliminarImagen" data-id="<?= $value2['id'] ?>">Eliminar</button>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                        </div> <!-- <div class="col-md-12"> -->

                        <div class="col">
                            <button type="button" id="btnSend2" class="btn btn-outline-primary"><i class="fas fa-save"></i> Actualizar</button>
                        </div>

                    </div>

                </div>

			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-info"> -->

	</div> <!-- <div class="col-md-6"> -->

	<div class="col-md-4">

		<div class="card card-warning card-outline">

			<div class="card-body">
                <div class="row">
                    <div class="col-6 form-group">
                        <label for="detalle">Detalle:</label>
                        <select class="form-control select2" name="detalle" id="detalle">
                            <option value="0">Seleccione una detalle</option>
                            <option value="1">Fugas</option>
                            <option value="2">Transmision</option>
                            <option value="3">Sistemas hco.</option>
                            <option value="4">Motor</option>
                            <option value="5">Pintura</option>
                            <option value="6">Seguridad</option>
                            <option value="7">General</option>
                        </select>
                    </div>
                    <div class="col-md-6 form-group">
                        <label for="fecha">Fecha de Actualizacion:</label>
                        <input type="date" class="form-control form-control-sm" name="fecha" id="fecha">
                    </div>
                    <div class="col-12">
                        <form class="dropzone needsclick" id="demo-upload" action="">
                            <div id="dropzone">
        
                                <div class="dz-message needsclick">    
                                    Suelta las imagenes aquí o haz clic para subir.
                                    
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col -12 form-gruop mt-2">
                        <button type="button" class="btn btn-primary" id="btnGuardarImagenes">Enviar</button>
                    </div>
                </div>
            
			</div> <!-- <div class="box-body"> -->

		</div> <!-- <div class="box box-warning"> -->

	</div> <!-- <div class="col-md-6"> -->

</div> <!-- <div class="row"> -->
