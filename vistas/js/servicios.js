$(function(){

	let tableList = document.getElementById('tablaServicios');
	let parametrosTableList = { responsive: true };

	// Realiza la petición para actualizar el listado de servicios
	function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

		fetch( rutaAjax, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			// console.log(data.datos)
         //    let renderEstatus = {
         //    	data: 'estatus',
         //    	render: function (data, type, row, meta) {
         //    		let color = '';
         //    		if ( row.colorTexto != '' ) color += `color: ${row.colorTexto};`;
         //    		if ( row.colorFondo != '' ) color += `background-color: ${row.colorFondo};`;

         //        	return type === 'display'
         //            	? `<span style="${color}">${data}</span>`
         //            	: data;
         //    	}
        	// };
        	// data.datos.columnas[4] = renderEstatus;

			$(idTabla).DataTable({

				autoWidth: false,
				responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,

		        createdRow: function (row, data, index) {
		        	if ( data.colorTexto != '' ) $('td', row).eq(2).css("color", data.colorTexto);
		        	if ( data.colorFondo != '' ) $('td', row).eq(2).css("background-color", data.colorFondo);
		        },

				buttons: [{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }],

				language: LENGUAJE_DT,
				aaSorting: [],

			// }).buttons().container().appendTo(idTabla+'_wrapper .col-md-6:eq(0)'); // $(idTabla).DataTable({
			}).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); // $(idTabla).DataTable({
		}); // .then( data => {

	} // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	// if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ServicioAjax.php', '#tablaServicios', parametrosTableList);
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/ServicioAjax.php', '#tablaServicios', parametrosTableList);
	
	// $('#collapseFiltros').on('shown.bs.collapse', function (event) {
	$('#collapseFiltros').on('show.bs.collapse', function (event) {
		let btnVerFiltros = document.getElementById('btnVerFiltros');
		btnVerFiltros.querySelector('i').classList.remove("fa-eye");
		btnVerFiltros.querySelector('i').classList.add("fa-eye-slash");
	})
	
	// $('#collapseFiltros').on('hidden.bs.collapse', function (event) {
	$('#collapseFiltros').on('hide.bs.collapse', function (event) {
		let btnVerFiltros = document.getElementById('btnVerFiltros');
		btnVerFiltros.querySelector('i').classList.remove("fa-eye-slash");
		btnVerFiltros.querySelector('i').classList.add("fa-eye");
	})

	$('#btnFiltrar').on('click', function (e) {
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';

		let empresaId = $('#filtroEmpresaId').val();
		let servicioCentroId = $('#filtroServicioCentroId').val();
		let maquinariaId = $('#filtroMaquinariaId').val();
		let servicioEstatusId = $('#filtroServicioEstatusId').val();
		let fechaInicial = $('#filtroFechaInicial').val();
		let fechaFinal = $('#filtroFechaFinal').val();
		let servicioTipoId = $('#filtroServicioTipoId').val();
		let mantenimientoTipoId = $('#filtroMantenimientoTipoId').val();

		if ( fechaInicial == '' ) fechaInicial = 0;
		if ( fechaFinal == '' ) fechaFinal = 0;

		// fAjaxDataTable(`${rutaAjax}app/Ajax/ServicioAjax.php?empresaId=${empresaId}&servicioCentroId=${servicioCentroId}&maquinariaId=${maquinariaId}&servicioEstatusId=${servicioEstatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}`, '#tablaServicios');
		fActualizarListado(`${rutaAjax}app/Ajax/ServicioAjax.php?empresaId=${empresaId}&servicioCentroId=${servicioCentroId}&maquinariaId=${maquinariaId}&servicioEstatusId=${servicioEstatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}&servicioTipoId=${servicioTipoId}&mantenimientoTipoId=${mantenimientoTipoId}`, '#tablaServicios', parametrosTableList);
	});

	$('#btnGenerarPDF').on('click', function (event) {
		let empresaId = $('#filtroEmpresaId').val();
		let servicioCentroId = $('#filtroServicioCentroId').val();
		let maquinariaId = $('#filtroMaquinariaId').val();
		let servicioEstatusId = $('#filtroServicioEstatusId').val();
		let fechaInicial = $('#filtroFechaInicial').val();
		let fechaFinal = $('#filtroFechaFinal').val();

		if ( fechaInicial == '' ) fechaInicial = 0;
		if ( fechaFinal == '' ) fechaFinal = 0;

		let btnGenerarPDF = this;

		// Petición para generar reporte PDF
		$.ajax({
			url: `${rutaAjax}app/Ajax/ServicioAjax.php?accion=reporte&empresaId=${empresaId}&servicioCentroId=${servicioCentroId}&maquinariaId=${maquinariaId}&servicioEstatusId=${servicioEstatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}`,
			method: 'GET',
			cache: false,
        	contentType: false,
 			processData: false,
			// dataType: 'json',
			beforeSend: () =>{
				$(btnGenerarPDF).prop('disabled', true);
			}
		})
		.done(function(respuesta) {
			respuesta = JSON.parse(respuesta);
			if ( respuesta.error ) {
				// $('.toast-header').removeClass().addClass("toast-header text-info bg-white");
				// $('.toast-body').removeClass().addClass("toast-body text-white bg-info");
				// $('.toast-body').html("Ocurrió un Error al Ver el Escenario <br>"+respuesta.errorMessage);
				// let toast = new bootstrap.Toast( elementToast, { animation: true, autohide: true, delay: 7000 } );
				// toast.show();
				console.log(respuesta.errorMessage)
				return;
			}

			if ( respuesta.archivo ) {
				let rutaArchivo = respuesta.archivo;
				let random = Math.floor( Math.random() * 99 );
				let elementArchivo = document.querySelector('#modalVerReporte .archivo');

				$(elementArchivo).html(`<embed src="${rutaArchivo}?v=${random}#toolbar=1&navpanes=0" type="application/pdf" width="100%" height="500px" />`);

				let modalVerReporte = new bootstrap.Modal(document.getElementById('modalVerReporte'), {
					backdrop: 'static'
					// keyboard: false
				})

				modalVerReporte.show();

				// let anchor = document.createElement('a');
				// anchor.target = '_blank';
				// anchor.href = `${respuesta.archivo}`;
				// anchor.innerText = 'Generar Reporte';
				// anchor.click();
			}
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);

			// $('.toast-header').removeClass().addClass("toast-header text-info bg-white");
			// $('.toast-body').removeClass().addClass("toast-body text-white bg-info");
			// $('.toast-body').html("Ocurrió un Error al Ver el Escenario <br>"+error.responseJSON.message);
			// let toast = new bootstrap.Toast( elementToast, { animation: true, autohide: true, delay: 7000 } );
			// toast.show();
		})
		.always(function() {
			$(btnGenerarPDF).prop('disabled', false);
		});
	});

	// Confirmar la eliminación del Servicio
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Servicio (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarlo!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				form.submit();
			}
	    })

	});

	// Envio del formulario para Crear o Editar registros
	function enviar(deshabilitar = true){
		if ( deshabilitar ) btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		if ( deshabilitar ) {
			padre = btnEnviar.parentNode;
			padre.removeChild(btnEnviar);
		}

		formulario.submit();
	}
	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	// Envio del formulario para Solicitar Finalizar el registro
	function solicitarFinalizar(){
		btnSolicitar.disabled = true;

		padre = btnSolicitar.parentNode;
		padre.removeChild(btnSolicitar);

		var input = document.createElement('input'); // prepare a new input DOM element
		input.setAttribute('name', 'servicioEstatusId'); // set the param name
		input.setAttribute('value', 8); // set the value
		input.setAttribute('type', 'hidden') // set the type, like "hidden" or other

		formulario.appendChild(input); // append the input to the form
		enviar();
	}
	let btnSolicitar = document.getElementById("btnSolicitar");
	if ( btnSolicitar != null ) btnSolicitar.addEventListener("click", solicitarFinalizar);

	// Envio del formulario para Cancelar el registro
	function cancelar(){
		btnCancelar.disabled = true;

		padre = btnCancelar.parentNode;
		padre.removeChild(btnCancelar);

		var input = document.createElement('input'); // prepare a new input DOM element
		input.setAttribute('name', 'servicioEstatusId'); // set the param name
		input.setAttribute('value', 4); // set the value
		input.setAttribute('type', 'hidden') // set the type, like "hidden" or other

		formulario.appendChild(input); // append the input to the form
		enviar();
	}

	let btnCancelar = document.getElementById("btnCancelar");
	if ( btnCancelar != null ) btnCancelar.addEventListener("click", cancelar);

	// Envio del formulario para Solicitar el registro
	function finalizar(){
		btnFinalizar.disabled = true;

		padre = btnFinalizar.parentNode;
		padre.removeChild(btnFinalizar);

		var input = document.createElement('input'); // prepare a new input DOM element
		input.setAttribute('name', 'servicioEstatusId'); // set the param name
		input.setAttribute('value', 3); // set the value
		input.setAttribute('type', 'hidden') // set the type, like "hidden" or other

		formulario.appendChild(input); // append the input to the form
		enviar(false);
	}
	let btnFinalizar = document.getElementById("btnFinalizar");
	if ( btnFinalizar != null ) btnFinalizar.addEventListener("click", finalizar);

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	});
	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	});
	//Date picker
    // $('#fechaSolicitudDTP').datetimepicker({
    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });

    let minDate = moment().subtract(12, 'months')
	let maxDate = moment().add(1, 'hours');

	$('#fechaSolicitudDTP').datetimepicker('minDate', minDate);
	$('#fechaSolicitudDTP').datetimepicker('maxDate', maxDate);

	minDate = $('#fechaSolicitudDTP').datetimepicker('viewDate');

	$('#fechaFinalizacionDTP').datetimepicker();
	$('#fechaFinalizacionDTP').datetimepicker();

    let elementEmpresaId = $('#empresaId.select2.is-invalid');
    let elementServicioCentroId = $('#servicioCentroId.select2.is-invalid');
    let elementServicioEstatusId = $('#servicioEstatusId.select2.is-invalid');
    let elementSolicitudTipoId = $('#solicitudTipoId.select2.is-invalid');
    let elementMantenimientoTipoId = $('#mantenimientoTipoId.select2.is-invalid');
    let elementServicioTipoId = $('#servicioTipoId.select2.is-invalid');
    let elementMaquinariaId = $('#maquinariaId.select2.is-invalid');
    if ( elementEmpresaId.length == 1 ) {
		$('span[aria-labelledby="select2-empresaId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-empresaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementServicioCentroId.length == 1 ) {
		$('span[aria-labelledby="select2-servicioCentroId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-servicioCentroId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-servicioCentroId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-servicioCentroId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-servicioCentroId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementServicioEstatusId.length == 1) {
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementSolicitudTipoId.length == 1) {
		$('span[aria-labelledby="select2-solicitudTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-solicitudTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-solicitudTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-solicitudTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-solicitudTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementMantenimientoTipoId.length == 1) {
		$('span[aria-labelledby="select2-mantenimientoTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-mantenimientoTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-mantenimientoTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-mantenimientoTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-mantenimientoTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementServicioTipoId.length == 1) {
		$('span[aria-labelledby="select2-servicioTipoId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-servicioTipoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-servicioTipoId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-servicioTipoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-servicioTipoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}
	if ( elementMaquinariaId.length == 1) {
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

	let campoMaquinariaId = document.getElementById('maquinariaId');

	$(campoMaquinariaId).on('change', function (e) {

		$("#maquinariaTipoDescripcion").val('');
		$("#ubicacionId").val('');
		$("#maquinariaObraDescripcion").val('');
		$("#maquinariaMarcaDescripcion").val('');
		$("#maquinariaModeloDescripcion").val('');
		$("#maquinariaDescripcion").val('');
		$("#maquinariaSerie").val('');

		// Consultar los datos de la maquinaria seleccionada
		if ( campoMaquinariaId.value != '' ) {

		  	fetch( rutaAjax+'app/Ajax/MaquinariaAjax.php?maquinariaId='+campoMaquinariaId.value, {
				method: 'GET', // *GET, POST, PUT, DELETE, etc.
				cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
				headers: {
				'Content-Type': 'application/json'
				}
		  	} )
			.then( response => response.json() )
			.catch( error => console.log('Error:', error) )
			.then( data => {
				if ( data.datos.maquinaria ) {
					$("#maquinariaTipoDescripcion").val(data.datos.maquinaria['maquinaria_tipos.descripcion']);
					// let elementUbicacionId = document.getElementById('ubicacionId');
					// if ( elementUbicacionId !== null ) {
					console.log(data.datos.maquinaria['obraId'])
					if ( data.datos.maquinaria['obraId'] == 0 ) data.datos.maquinaria['obraId'] = '';
						$("#ubicacionId").val(data.datos.maquinaria['ubicacionId']).trigger('change');
						$("#obraId").val(data.datos.maquinaria['obraId']).trigger('change');
						$("#maquinariaObraDescripcion").val(data.datos.maquinaria['obras.descripcion']);
					// }
					$("#maquinariaMarcaDescripcion").val(data.datos.maquinaria['marcas.descripcion']);
					$("#maquinariaModeloDescripcion").val(data.datos.maquinaria['modelos.descripcion']);
					$("#maquinariaDescripcion").val(data.datos.maquinaria['descripcion']);
					$("#maquinariaSerie").val(data.datos.maquinaria['serie']);
				}
			}); // .then( data => {

		} // if ( campoMaquinariaId.value != '' )

	});

	/*==============================================
	Abrir el input al presionar el botón Subir Fotos
	==============================================*/
	$("#btnSubirFotos").click(function() {
		document.getElementById('imagenes').click();
	})

	/*===========================================================
 	Validar tipo y tamaño de las imágenes (Evidencia fotográfica)
 	===========================================================*/
 	$("#imagenes").change(function() {

 		$("div.subir-fotos span.previsualizar").html('');
 		let archivos = this.files;
 		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];

			/*================================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA JPG O PNG
			================================================*/
			
			if ( archivo["type"] != "image/jpeg" && archivo["type"] != "image/png" ) {

				error = true;
				// $("#imagenes").val("");
				// $("div.subir-fotos span.previsualizar").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser JPG o PNG!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 1000000 ) {

				error = true;
				// $("#imagenes").val("");
				// $("div.subir-fotos span.previsualizar").html('');

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 1MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else {

				let datosImagen = new FileReader;
				datosImagen.readAsDataURL(archivo);

				$(datosImagen).on("load", function(event) {
					let rutaImagen = event.target.result;

					let elementPicture = `<picture>
											<img src="${rutaImagen}" class="img-fluid img-thumbnail" style="width: 100%">
										</picture>
										<p class="font-italic text-info mb-0">${archivo["name"]}</p>`;
					$("div.subir-fotos span.previsualizar").append(elementPicture);
				})

			}

 		}

 		if ( error ) {
 			$("#imagenes").val("");

 			setTimeout(() => {
  				$("div.subir-fotos span.previsualizar").html('');
			}, "1000");
 		}

	}) // $("#fotos").change(function(){

	let modalVerImagenes = document.querySelector('#modalVerImagenes');
	/*==============================================================
	Visualizar las imágenes	
	==============================================================*/
	$("#verImagenes").click(function(e) {

		let verBotonEliminar = this.getAttribute('verBotonEliminar');
		let folio = this.getAttribute('folio').toUpperCase();
		$("#modalVerImagenesLabel span").html(folio);
		$("#modalVerImagenes div.imagenes").html('');

		let token = $('input[name="_token"]').val();
		let servicioId = $(this).attr("servicioId");

		let datos = new FormData();
		datos.append("accion", "verImagenes");
		datos.append("_token", token);
		datos.append("servicioId", servicioId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/ServicioAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerImagenes.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				respuesta.imagenes.forEach( (imagen, index) => {
					let elementImagen = ( verBotonEliminar ) === 'true'
						? `<div class="col mb-4">
								<div class="card">
									<i class="mt-1 mr-1 p-1 fas fa-trash-alt fa-lg text-danger eliminarImagen" style="cursor: pointer; position: absolute; top: 0; right: 0; background-color: rgba(255, 255, 255, 0.1);" servicioId="${servicioId}" imagenId="${imagen.id}" folio="${imagen.titulo}"></i>
									<img src="${imagen.ruta}" class="card-img-top" alt="${imagen.titulo}">
								</div>
							</div>`
						: `<div class="col mb-4">
								<div class="card">
									<img src="${imagen.ruta}" class="card-img-top" alt="${imagen.titulo}">
								</div>
							</div>`;

					$("#modalVerImagenes div.imagenes").append(elementImagen);
				});

		    }
		}) // $.ajax({

	})

	// Confirmar la eliminación de las Imágenes
	$("#modalVerImagenes div.imagenes").on("click", "i.eliminarImagen", function (e) {

		let btnEliminar = this;
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás seguro de querer eliminar esta Foto (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarla!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if ( result.isConfirmed ) eliminarImagen(btnEliminar);
	    })

	});

	// Envio del formulario para Eliminar la imágen
	function eliminarImagen(btnEliminar = null){

		if ( btnEliminar == null ) return;

		let token = $('input[name="_token"]').val();
		let imagenId = $(btnEliminar).attr("imagenId");
		let servicioId = $(btnEliminar).attr("servicioId");

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarImagen");
		datos.append("imagenId", imagenId);
		datos.append("servicioId", servicioId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/ServicioAjax.php",
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

		    	// console.log(respuesta)
		    	// Si la respuesta es positiva pudo eliminar el archivo
		    	if (respuesta.respuesta) {

		    		$(btnEliminar).parent().parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().parent().remove();

		    		let btnVerImagenes = document.getElementById("verImagenes");
		    		let elementSpan = btnVerImagenes.querySelector('span');
		    		let cantidadImagenes = elementSpan.innerHTML;
		    		btnVerImagenes.querySelector('span').innerHTML = --cantidadImagenes;

		    		if ( cantidadImagenes == 0 ) {
		    			btnVerImagenes.removeChild(elementSpan);
		    			$(btnVerImagenes).prop('disabled', true);
		    		}

		    	} else {

		    		$(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

	/*===================================================
	Abrir el input al presionar el botón Subir Documentos
	===================================================*/
	$("#btnSubirArchivos").click(function() {
		document.getElementById('archivos').click();
	})

	/*================================================
 	Validar tipo y tamaño de los archivos (Documentos)
 	================================================*/
 	$("#archivos").change(function() {

 		let archivos = this.files;
 		if ( archivos.length == 0) return;

 		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];

			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "application/pdf" ) {

				error = true;

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			}

 		}

 		if ( error ) {
 			$("#archivos").val("");

 			return;
 		}

 		// Petición Ajax para subir los archivos
		let token = $('input[name="_token"]').val();
		let servicioId = $('#btnSubirArchivos').attr("servicioId");
		let folio = $('#folio').val();

		let datos = new FormData(document.getElementById("formArchivosSend"));
		datos.append("_token", token);
		datos.append("accion", "subirArchivos");
		datos.append("servicioId", servicioId);
		datos.append("folio", folio);

		$.ajax({
		    url: rutaAjax+'app/Ajax/ServicioAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

		    	// console.log(respuesta)
				if ( respuesta.error ) {

					$("#btnSubirArchivos").parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

				} else {

					$("#btnSubirArchivos").parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

				}

				setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

	    		$("#archivos").val("");

	    		if ( respuesta.archivos ) {
	    			
	    			let btnVerArchivos = document.querySelector('#verArchivos');
	    			$(btnVerArchivos).prop('disabled', false);

	    			if ( btnVerArchivos.querySelector('span.badge') === null ) {
	    				let elementSpan = `<span class="badge badge-light">${respuesta.archivos.length}</span>`;

	    				$(btnVerArchivos).append(elementSpan);
	    			} else {
	    				btnVerArchivos.querySelector('span.badge').innerHTML = respuesta.archivos.length;
	    			}

	    		}

		    }

		})

	}) // $("#archivos").change(function() {

	let modalVerArchivos = document.querySelector('#modalVerArchivos');
	/*==============================================================
	Visualizar los archivos
	==============================================================*/
	$("#verArchivos").click(function(e) {

		let verBotonEliminar = this.getAttribute('verBotonEliminar');
		let folio = this.getAttribute('folio').toUpperCase();
		$("#modalVerArchivosLabel span").html(folio);
		$("#modalVerArchivos div#accordionArchivos").html('');

		let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');
		$(elementErrorValidacion).addClass("d-none");

		let token = $('input[name="_token"]').val();
		let servicioId = $(this).attr("servicioId");

		let datos = new FormData();
		datos.append("accion", "verArchivos");
		datos.append("_token", token);
		datos.append("servicioId", servicioId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/ServicioAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					// let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				respuesta.archivos.forEach( (archivo, index) => {
					let elementIconoEliminar = ( verBotonEliminar ) === 'true'
						? `<i class="fas fa-trash-alt fa-xs text-danger eliminarArchivo" style="cursor: pointer; position: absolute; top: 12px; right: 8px;" servicioId="${servicioId}" archivoId="${archivo.id}" folio="${archivo.titulo}"></i>`
						: '';

					let elementArchivo = `
						<div class="card">
							<div class="card-header px-0 py-2" id="heading-${index+1}">
								<h2 class="mb-0">
									<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-${index+1}" aria-expanded="false" aria-controls="collapse-${index+1}">
          								${archivo.titulo}
									</button>${elementIconoEliminar}
								</h2>
							</div>

							<div id="collapse-${index+1}" class="collapse ${(index == 0) ? "show" : ""}" aria-labelledby="heading-${index+1}" data-parent="#accordionArchivos">
								<div class="card-body p-0">
									<embed src="${archivo.ruta}#toolbar=1&navpanes=0" type="application/pdf" width="100%" height="600px" />
								</div>
							</div>
  						</div>`;

					$("#modalVerArchivos div#accordionArchivos").append(elementArchivo);
				});

		    }

		})

	})

	// Confirmar la eliminación de los Archivos
	$("#modalVerArchivos div.accordion").on("click", "i.eliminarArchivo", function (e) {

		let btnEliminar = this;
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarlo!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if ( result.isConfirmed ) eliminarArchivo(btnEliminar);
	    })

	});

	// Envio del formulario para Eliminar el archivo
	function eliminarArchivo(btnEliminar = null){

		if ( btnEliminar == null ) return;

		let token = $('input[name="_token"]').val();
		let archivoId = $(btnEliminar).attr("archivoId");
		let servicioId = $(btnEliminar).attr("servicioId");

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarArchivo");
		datos.append("archivoId", archivoId);
		datos.append("servicioId", servicioId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/ServicioAjax.php",
		    method: "POST",
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

		    	// console.log(respuesta)
		    	// Si la respuesta es positiva pudo eliminar el archivo
		    	if (respuesta.respuesta) {

		    		$(btnEliminar).parent().parent().parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().parent().parent().remove();

		    		let btnVerArchivos = document.getElementById("verArchivos");
		    		let elementSpan = btnVerArchivos.querySelector('span');
		    		let cantidadImagenes = elementSpan.innerHTML;
		    		btnVerArchivos.querySelector('span').innerHTML = --cantidadImagenes;

		    		if ( cantidadImagenes == 0 ) {
		    			btnVerArchivos.removeChild(elementSpan);
		    			$(btnVerArchivos).prop('disabled', true);
		    		}

		    	} else {

		    		$(btnEliminar).parent().parent().parent().before('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

});
