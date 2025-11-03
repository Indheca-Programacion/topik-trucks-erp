$(function(){

	/*=============================================
	Abrir el input al presionar la imágen (figure)
	=============================================*/
	$("#btnSubirArchivo").click(function(){
		document.getElementById('archivo').click();
	})

	/*=============================================
 	Validar tipo y tamaño del archivo
 	=============================================*/
 	$("#archivo").change(function(){

 		if ( this.files.length == 0 ) {
 			$("#archivoActual").val("");
 			validaCamposHorometro();
 			return;
 		}

	    let archivo = this.files[0];
	    
		/*==========================================
		VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
		==========================================*/
		
		if ( archivo["type"] != "application/pdf" ) {

			$("#archivoActual").val("");
			$("#archivo").val("");

			Swal.fire({
			  title: 'Error en el tipo de archivo',
			  text: '¡El archivo debe ser PDF!',
			  icon: 'error',
			  confirmButtonText: '¡Cerrar!'
			})

		} else if ( archivo["size"] > 4000000 ) {

			$("#archivoActual").val("");
			$("#archivo").val("");

			Swal.fire({
			  title: 'Error en el tamaño del archivo',
			  text: '¡El archivo no debe pesar más de 4MB!',
			  icon: 'error',
			  confirmButtonText: '¡Cerrar!'
			})

		} else {

			$("#archivoActual").val(archivo["name"]);
			// $("#formato").val(archivo["type"]);

		}

		validaCamposHorometro();

	}) // $("#archivo").change(function(){

	let campoFecha = document.getElementById('fecha');
	let campoHorometroInicial = document.getElementById('horometroInicial');
	let campoKilometrajeInicial = document.getElementById('kilometrajeInicial');
	let campoHorometroFinal = document.getElementById('horometroFinal');
	let campoKilometrajeFinal = document.getElementById('kilometrajeFinal');
	let campoArchivo = document.getElementById('archivo');
	let campoArchivoActual = document.getElementById('archivoActual');

	function validaCamposHorometro(){

		// Habilita/Deshabilita el botor Agregar Horometro
		if ( campoFecha.value != "" && campoHorometroInicial.value != "" && campoKilometrajeInicial.value != "" && campoHorometroFinal.value != "" && campoKilometrajeFinal.value != "" && campoArchivo.value != "" ) {
			$("button#btnAgregarHorometro").prop('disabled', false);
		} else {
			$("button#btnAgregarHorometro").prop('disabled', true);
		}

	}

	$(campoFecha).on('change', function (e) {
		validaCamposHorometro();
	});

	$(campoHorometroInicial).on('change', function (e) {
		validaCamposHorometro();
	});

	$(campoKilometrajeInicial).on('change', function (e) {
		validaCamposHorometro();
	});

	$(campoHorometroFinal).on('change', function (e) {
		validaCamposHorometro();
	});

	$(campoKilometrajeFinal).on('change', function (e) {
		validaCamposHorometro();
	});

	$("button#btnAgregarHorometro").on('click', function (e) {

		if ( campoFecha.value == "" ) {
			return;
		}

		$(this).prop('disabled', true);

		let mensaje = document.getElementById("msgAgregarHorometro");
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		let token = $('input[name="_token"]').val();
		let maquinariaId = $(this).attr("maquinariaId");
		let fecha = campoFecha.value;
		// let horometroInicial = campoHorometroInicial.value;
		// let kilometrajeInicial = campoKilometrajeInicial.value;
		// let horometroFinal = campoHorometroFinal.value;
		// let kilometrajeFinal = campoKilometrajeFinal.value;
		// let archivo = campoArchivo.value;
		
		// let datos = new FormData();
		let datos = new FormData(document.getElementById("formHorometro"))
		datos.append("accion", "agregar");
		datos.append("_token", token);
		datos.append("maquinariaId", maquinariaId);
		// datos.append("fecha", fecha);
		// datos.append("horometroInicial", horometroInicial);
		// datos.append("kilometrajeInicial", kilometrajeInicial);
		// datos.append("horometroFinal", horometroFinal);
		// datos.append("kilometrajeFinal", kilometrajeFinal);
		// datos.append("archivo", archivo);

		$.ajax({
		    url: rutaAjax+"app/Ajax/MaquinariaHorometroAjax.php",
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

		    	mensaje.innerHTML = "";	    	

		    	// Si la respuesta es positiva pudo agregar el registro
		    	if (respuesta.respuesta) {

		    		$(mensaje).after('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

						// var rows = $("ul#listaVerificaciones li");
						// Elimina mensaje de Vehículo sin Verificaciones cuando la lista de verificaciones está vacía
						// if ( rows[0].innerHTML == "Vehículo sin Verificaciones realizadas") {
						// 	rows[0].remove();
						// }

		    		$('.horometroCaptura').after(`
		    				<div class="card card-info horometros">
							    <div class="card-header">
										<h3 class="card-title">Fecha: ${fecha}</h3>
										<div class="card-tools m-0">
											<button type="button" class="btn btn-tool" data-card-widget="collapse">
												<i class="fas fa-minus"></i>
											</button>
											<button type="button" class="btn btn-tool eliminarHorometro" fecha="${respuesta.respuesta.fecha}">
												<i class="fas fa-times text-danger"></i>
											</button>
										</div>
									</div>
									<div class="card-body">
										<div class="row">
											<div class="col-sm-6 col-md-3 form-group">
												<label>Horómetro Inicial:</label>
												<input type="text" value="${respuesta.respuesta.horometroInicial}" class="form-control form-control-sm" readonly>
											</div>
											<div class="col-sm-6 col-md-3 form-group">
												<label>Kilometraje Inicial:</label>
												<input type="text" value="${respuesta.respuesta.kilometrajeInicial}" class="form-control form-control-sm" readonly>
											</div>
											<div class="col-sm-6 col-md-3 form-group">
												<label>Horómetro Final:</label>
												<input type="text" value="${respuesta.respuesta.horometroFinal}" class="form-control form-control-sm" readonly>
											</div>
											<div class="col-sm-6 col-md-3 form-group">
												<label>Kilometraje Final:</label>
												<input type="text" value="${respuesta.respuesta.kilometrajeFinal}" class="form-control form-control-sm" readonly>
											</div>
										</div>
									</div>
		    				</div>
		    			`);

		    		// Vaciar los campos de captura
						$(campoFecha).val('');
						$(campoHorometroInicial).val('');
						$(campoKilometrajeInicial).val('');
						$(campoHorometroFinal).val('');
						$(campoKilometrajeFinal).val('');
						$(campoArchivo).val('');
						$(campoArchivoActual).val('');

		    	} else {

		    		$(mensaje).after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    		$("button#btnAgregarHorometro").prop('disabled', false);

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	});

	$("div.card.horometros").on("click", "button.eliminarHorometro", function (e) {

		$(this).prop('disabled', true);

		var horometro = $(this);

		let token = $('input[name="_token"]').val();
		let maquinariaId = $('#btnAgregarHorometro').attr("maquinariaId");
		let fecha = $(this).attr("fecha");

		let datos = new FormData();
		datos.append("accion", "eliminar");
		datos.append("_token", token);
		datos.append("maquinariaId", maquinariaId);
		datos.append("fecha", fecha);

		$.ajax({
	    url: rutaAjax+"app/Ajax/MaquinariaHorometroAjax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

	    	// Si la respuesta es positiva pudo eliminar el registro
	    	if (respuesta.respuesta) {

	    		$(horometro).parent().parent().parent().after('<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

	    		$(horometro).parent().parent().parent().remove();

	    		// Agrega mensaje de Equipo sin Software cuando la lista de programas está vacía
	    // 		var rows = $("ul#listaVerificaciones li");
    	// 		if ( rows.length == 0 ) {
					// $("ul#listaVerificaciones").append('<li class="list-group-item">Vehículo sin Verificaciones realizadas</li>');
    	// 		}

	    	} else {
	    		
	    		$(horometro).parent().parent().parent().after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    }

    		setTimeout(function(){ 
    			$(".alert").remove();
    		}, 5000);

	    }

		})

	});

	$("div.card.horometros").on("click", "button.downloadHorometro", function (e) {
		$(this).prop('disabled', true);
		let btnDownload = this;

		let token = $('input[name="_token"]').val();
		let maquinariaId = $('#btnAgregarHorometro').attr("maquinariaId");
		let fecha = $(this).attr("fecha");

		let datos = new FormData();
		datos.append("accion", "descargar");
		datos.append("_token", token);
		datos.append("maquinariaId", maquinariaId);
		datos.append("fecha", fecha);

		$.ajax({
	    url: rutaAjax+"app/Ajax/MaquinariaHorometroAjax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success:function(respuesta){

	    	// Si la respuesta es positiva descargar el archivo
	    	if (respuesta.respuesta) {

	    		const anchor = document.createElement("a");
	    		anchor.href = respuesta.respuesta.ruta;
	    		anchor.download = respuesta.respuesta.archivo;

	    		document.body.appendChild(anchor);
	    		anchor.click();
	    		document.body.removeChild(anchor);

	    	}

	    },
	    complete:function(){
	    	$(btnDownload).prop('disabled', false);
	    }
		}) // $.ajax({

	});

});