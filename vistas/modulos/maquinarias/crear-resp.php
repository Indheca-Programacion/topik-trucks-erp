<?php
	include "vistas/modulos/plantilla/encabezado.php";
	$old = old();

	use App\Route;
?>

<div class="content-wrapper">

	<section class="content-header">
    
	    <h1>
	      
	      Maquinarias

	      <small>Crear</small>
	    
	    </h1>

	    <ol class="breadcrumb">
	      
	      <li><a href="<?php echo Route::routes('inicio'); ?>"><i class="fa fa-dashboard"></i> Inicio</a></li>

	      <li><a href="<?php echo Route::names('maquinarias.index'); ?>"><i class="fa fa-truck"></i> Maquinarias</a></li>
	      
	      <li class="active">Crear maquinaria</li>
	    
	    </ol>

	</section>

	<section class="content">

		<div class="row">
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Crear maquinaria</h3>
					</div>
					<div class="box-body">

						<?php
							include "vistas/modulos/errores/form-messages.php";
						?>

						<form id="formSend" method="POST" action="<?php echo Route::names('maquinarias.store'); ?>" enctype="multipart/form-data">

							<?php
								include "vistas/modulos/maquinarias/formulario.php";
							?>
							
							<input type="button" id="btnSend" class="btn btn-primary" value="Crear maquinaria">
							<div id="msgSend"></div>
						</form>
					</div>
				</div>
			</div>
		</div>

	</section>

</div>

<script>
	function enviar(){
    		btnEnviar.disabled = true;
    		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

            padre = btnEnviar.parentNode;
            padre.removeChild(btnEnviar);

            formulario.submit();
	}
	formulario = document.getElementById("formSend");
	mensaje = document.getElementById("msgSend");
	btnEnviar = document.getElementById("btnSend");
	btnEnviar.addEventListener("click", enviar);

	var rutaAjax = "<?php echo Route::ruta(); ?>";
	var token = $('input[name="_token"]').val();
</script>

<?php
	$archivoJS = "vistas/bower_components/select2/dist/js/select2.full.min.js";
	$archivoJS2 = "vistas/bower_components/ckeditor4/ckeditor.js";
	$archivoJS3 = "/vistas/js/maquinarias.js";

	$comandoJS = "$('.select2').select2({
			tags: false
		});
		$('.select2Add').select2({
			tags: true
		});
		CKEDITOR.replace('editor');
		//CKEDITOR.config.height = 315;
		";
	include "vistas/modulos/plantilla/pie-de-pagina.php";
?>
