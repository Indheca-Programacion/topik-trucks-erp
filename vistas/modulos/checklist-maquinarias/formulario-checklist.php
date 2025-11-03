<script>
    // Tus datos desde PHP, incrustados directamente
    const allSteps = <?php echo json_encode($tareasPorSeccion); ?>;
</script>

<?php 
    use App\Route;
?>

<input type="hidden" name="checklistMaquinaria" id="checklistMaquinaria" value="<?php echo $checklistMaquinarias->id; ?>">

<?php if ( count( $respuestas ) > 0   ) : ?>

     <div class="col-md-6">
        <h1 class="mb-4 text-center">CheckList</h1>
        <div class="progress mb-4">
            <div class="progress-bar bg-primary" role="progressbar" style="width: 100%;" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <div class="card shadow p-4 mt-5" >
            
            <ul class="list-group list-group-flush">
                <?php foreach ($respuestas as $key => $respuesta): ?>
                    <li class="list-group-item active">
                        <?php echo mb_strtoupper($key); ?>
                        <?php foreach ($respuestas[$key] as $tarea): ?>
                            <li class="list-group-item"> 
                                 <?php echo mb_strtoupper($tarea["descripcion"]).': '.$tarea["respuesta"]; ?>
                            </li>
                        <?php endforeach; ?>
                        <?php if(count($observacionesPorSeccion) >0 & isset($observacionesPorSeccion[$key]) ) : ?>
                            <li class="list-group-item list-group-item-light">
                                <strong>Observaciones:</strong><br>
                                <?php echo mb_strtoupper($observacionesPorSeccion[$key]["observaciones"]); ?>
                            </li>
                        <?php endif ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="d-grid gap-2 mt-4">
            </div>

            <div class="col-12">
                <!--begin::Form-->
                <form class="form" action="" method="post" enctype="multipart/form-data">
                    <!--begin::Input group-->
                    <div class="fv-row">
                        <!--begin::Dropzone-->
                        <div class="dropzone" id="checklistDropzone">
                            <!--begin::Message-->
                            <div class="dz-message needsclick">
                                <i class="ki-duotone ki-file-up fs-3x text-primary"><span class="path1"></span><span class="path2"></span></i>
                                <!--begin::Info-->
                                <div class="ms-4">
                                    <h3 class="fs-5 fw-bold text-gray-900 mb-1">Arrastra archivos aquí o haz clic para subir.</h3>
                                </div>
                                <!--end::Info-->
                            </div>
                        </div>
                        <!--end::Dropzone-->
                    </div>
                    <!--end::Input group-->
                </form>
                <!--end::Form-->
            </div>

            <?php if (!empty($imagenesSubidas)): ?>
                <h5>Imágenes subidas:</h5>
                <div class="row">
                    <?php foreach ($imagenesSubidas as $img): ?>
                        <div class="col-4 mb-2">
                            <img src="<?php echo Route::rutaServidor().$img['ruta']; ?>" class="img-fluid rounded border" alt="Imagen subida">
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.css" />

            <script>
                Dropzone.autoDiscover = false;
                let myDropzone = new Dropzone("#checklistDropzone", {
                    url: 'app/Ajax/ChecklistMaquinariaAjax.php',
                    paramName: "file",
                    maxFiles: 10,
                    maxFilesize: 5, // MB
                    acceptedFiles: ".jpg,.jpeg,.png,.gif,.bmp,.webp",
                    addRemoveLinks: true,
                    dictDefaultMessage: "Arrastra archivos aquí o haz clic para subir.",
                    dictRemoveFile: "Eliminar archivo",
                    params: {
                        accion: "subirImagen",
                        checklistMaquinaria: document.getElementById("checklistMaquinaria").value

                    },
                    init: function() {
                        this.on("success", function(file, response) {
                            console.log(response);
                        });
                        this.on("error", function(file, errorMessage) {
                            // Maneja errores si es necesario
                        });
                    }
                });
            </script>

        </div>
    </div>
    

<?php elseif ( count( $secciones ) > 0 ) : ?>

    <div class="col-md-6">
        <h1 class="mb-4 text-center">CheckList</h1>

        <div class="progress mb-4">
            <div class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="progressBar"></div>
        </div>

        <div class="card shadow p-4">
            <div class="card-body">
                <h3 class="card-title text-center mb-4" id="stepDescription"></h3>
                <div class="row justify-content-center g-3" id="optionsContainer">
                    </div>
                <div class="mt-4" id="observationsContainer">
                    </div>
            </div>
            <div class="card-footer d-flex justify-content-between mt-4">
                <button type="button" class="btn btn-secondary" id="prevStepBtn" disabled>Anterior</button>
                <button type="button" class="btn btn-primary" id="nextStepBtn" disabled>Siguiente</button>
            </div>
        </div>

        <div class="card shadow p-4 mt-5 d-none" id="resultsContainer">
            <h3 class="card-title text-center mb-4">¡Checklist Completado!</h3>
            <p class="text-center">Aquí están tus selecciones:</p>
            <ul class="list-group list-group-flush" id="selectionList">
            </ul>
            <div class="d-grid gap-2 mt-4">
                <button type="button" class="btn btn-success" id="resetBtn">Reiniciar Checklist</button>
                <button type="button" class="btn btn-info" id="backToChecklistBtn">Volver al Checklist</button>
                <button type="button" class="btn btn-primary" id="finishBtn">Finalizar Checklist</button>
            </div>
        </div>

    </div>

<?php else : ?>

    <div class="col-md-6">
        <div class="alert alert-danger" role="alert">
            No se encontraron tareas para el checklist de la maquinaria seleccionada.
        </div>
    </div>

<?php endif; ?>