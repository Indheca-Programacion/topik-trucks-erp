$(function(){

	let tableList = document.getElementById('tablaRequisiciones');
	let tablaExistencias = null;
	let tablaProveedores = null;
	let parametrosTableList = { responsive: true };
	const TIEMPO_DESCARGA = 350;

	// Realiza la petición para actualizar el listado de requisiciones
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
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }],

				language: LENGUAJE_DT,
				aaSorting: [],

			}).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); // $(idTabla).DataTable({
		}); // .then( data => {

	} // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	// if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/RequisicionAjax.php', '#tablaRequisiciones');
	$(document).ready(function() {
		if ($('#filtroCentroServicioId').val() != '') {
			setTimeout(function() {
				$('#btnFiltrar').trigger('click');
			}, 100); // Espera breve para asegurar que todo esté listo
		}else{

			if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/RequisicionAjax.php', '#tablaRequisiciones', parametrosTableList);
		}
	});

	$('#tablaCodigosSat').DataTable({
		autoWidth: false,
		responsive: true,
	});

	$('#tablaCodigosSat tbody').on('click', 'tr.seleccionable', function () {
		var data = $('#tablaCodigosSat').DataTable().row(this).data();
		// Asignar los valores a los campos correspondientes
		$('#codigoId').val(data[0]); // Asumiendo que el código está en la primera columna
		$('#codigoDescripcion').val(data[1]); // Asumiendo que la descripción está en la segunda columna
		// Cerrar el modal
		$('#modalVerCodigosSat').modal('hide');
	});

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
		let centroServicioId = $('#filtroCentroServicioId').val();
		let estatusId = $('#filtroServicioEstatusId').val();
		let ubicacionId = $('#filtroUbicacionId').val();
		let maquinariaId = $('#filtroMaquinariaId').val();
		let fechaInicial = $('#filtroFechaInicial').val();
		let fechaFinal = $('#filtroFechaFinal').val();
		let concepto = $('#filtroConcepto').val();
		let ordenCompra = $('#filtroOrdenCompra').val();

		if ( fechaInicial == '' ) fechaInicial = 0;
		if ( fechaFinal == '' ) fechaFinal = 0;

		fActualizarListado(`${rutaAjax}app/Ajax/RequisicionAjax.php?empresaId=${empresaId}&centroServicioId=${centroServicioId}&ubicacionId=${ubicacionId}&servicioEstatusId=${estatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}&maquinariaId=${maquinariaId}&concepto=${concepto}&ordenCompra=${ordenCompra}`, '#tablaRequisiciones', parametrosTableList);
	});

	// Confirmar la eliminación de la Requisición
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Requisición (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarla!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				form.submit();
			}
	    })

	});

	$("#btnUpdate").on("click",function (e) {
		let btnAgregar = $(this);
		let inputs = document.querySelectorAll('.campoConDecimal');
		let datos = new FormData();
		
		inputs.forEach(function(input) {
			let partidaIdInput = document.getElementById('partidaId' + input.id.replace('costo', ''));
			let partidaIdValor = partidaIdInput.value;
			let costo = input.value;
			datos.append(partidaIdValor, costo);
		});
		
		datos.append("accion", "actualizar");
		$.ajax({
			url: rutaAjax+'app/Ajax/RequisicionAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
			}
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Actualizar Costos', 'Error', respuesta.errorMessage);
				return;
			}
			$('#modalAddCostosTotales').modal('hide');
			crearToast('bg-success', 'Actualizar Costos', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			console.log(error)
			// let elementList = document.createElement('li'); // prepare a new li DOM element
			// let newContent = document.createTextNode(error.errorMessage);
			// elementList.appendChild(newContent); //añade texto al div creado.
			// // elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
			// $(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
	});

	// Habilitar observaciones al cambiar de estatus
	$("#servicioEstatusId").change(function(){
		let actualEstatusId = $('#actualServicioEstatusId').val();
		if ( actualEstatusId === '' ) return;

		let observacion = document.getElementById('observacion');
		if ( observacion === null ) return;

		if ( actualEstatusId == this.value ) {
			// let observacion = document.getElementById('observacion');
			$(observacion).prop('disabled', true);
			observacion.parentElement.parentElement.parentElement.classList.add("d-none");
		} else {
			// let observacion = document.getElementById('observacion');
			if ( $(observacion).prop('disabled') ) {
				$(observacion).prop('disabled', false);
				observacion.parentElement.parentElement.parentElement.classList.remove("d-none");
			}
		}
	}) // $("#servicioEstatusId").change(function(){

	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		formulario.submit();
	}
	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	// Envio del formulario para Finalizar el registro
	function finalizar(){
		btnFinalizar.disabled = true;

		padre = btnFinalizar.parentNode;
		padre.removeChild(btnFinalizar);

		var input = document.createElement('input'); // prepare a new input DOM element
		input.setAttribute('name', 'servicioEstatusId'); // set the param name
		input.setAttribute('value', 3); // set the value
		input.setAttribute('type', 'hidden') // set the type, like "hidden" or other

		formulario.appendChild(input); // append the input to the form
		enviar();
	}
	let btnFinalizar = document.getElementById("btnFinalizar");
	if ( btnFinalizar != null ) btnFinalizar.addEventListener("click", finalizar);

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

	let modalVerImagenes = document.querySelector('#modalVerImagenes');
	/*==============================================================
	Visualizar las imágenes	
	==============================================================*/
	$("#tablaRequisicionDetalles td i.verImagenes").click(function(e){

		let partida = this.getAttribute('partida');
		$("#modalVerImagenesLabel span").html(partida);
		$("#modalVerImagenes div.imagenes").html('');

		let token = $('input[name="_token"]').val();
		let detalleId = $(this).attr("detalleId");

		let datos = new FormData();
		datos.append("accion", "verImagenes");
		datos.append("_token", token);
		datos.append("detalleId", detalleId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/RequisicionAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta){

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerImagenes.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				respuesta.imagenes.forEach( (imagen, index) => {
					let elementImagen = `
						<div class="col mb-4">
							<div class="card">
								<img src="${imagen.ruta}" class="card-img-top" alt="${imagen.titulo}">
							</div>
						</div>`;

					$("#modalVerImagenes div.imagenes").append(elementImagen);
				});

		    }

		})

	})

	// Confirmar la eliminación de los Archivos
	$("div.subir-ordenes, div.subir-comprobantes, div.subir-facturas, div.subir-cotizaciones, div.subir-vales,div.subir-resguardos, div.subir-soportes").on("click", "i.eliminarArchivo", function (e) {

		let btnEliminar = this;
	    // let archivoId = $(this).attr("archivoId");
	    let folio = $(this).attr("folio");

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Archivo (Folio: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarlo!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				eliminarArchivo(btnEliminar);
			}
	    })

	});

	// Envio del formulario para Cancelar el registro
	function eliminarArchivo(btnEliminar = null){

		if ( btnEliminar == null ) return;		

		let archivoId = $(btnEliminar).attr("archivoId");

		// $(btnEliminar).prop('disabled', true);

		let token = $('input[name="_token"]').val();
		let requisicionId = $('input#requisicionId').val();

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarArchivo");
		datos.append("archivoId", archivoId);
		datos.append("requisicionId", requisicionId);

		$.ajax({
		    url: rutaAjax+"app/Ajax/RequisicionAjax.php",
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

		    		$(btnEliminar).parent().after('<div class="alert alert-success alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

		    		$(btnEliminar).parent().remove();

		    	} else {

		    		$(btnEliminar).parent().after('<div class="alert alert-warning alert-dismissable my-2"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    		// $(btnEliminar).prop('disabled', false);

			    }

	    		setTimeout(function(){ 
	    			$(".alert").remove();
	    		}, 5000);

		    }

		})

	}

	/*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
	$('.verArchivo').on('click', function () {
		var archivoRuta = $(this).attr('archivoRuta');
		$('#pdfViewer').attr('src', archivoRuta);

		// Mostrar el modal
		$('#pdfModal').modal('show');
	});
	



	// Al cerrar el modal, limpia el src del iframe
	$('#pdfModal').on('hidden.bs.modal', function () {
		$('#pdfViewer').attr('src', '');
	});


	/*==============================================================
	Abrir el input al presionar el botón Cargar Comprobantes de Pago
	==============================================================*/
	$("#btnSubirComprobantes").click(function(){
		document.getElementById('comprobanteArchivos').click();
	})

	/*========================================================
 	Validar tipo y tamaño de los archivos Comprobantes de Pago
 	========================================================*/
 	$("#comprobanteArchivos").change(function() {

 		// $("div.subir-comprobantes span.lista-archivos").html('');
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

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			}
			// else {

				// $("div.subir-comprobantes span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

			// }

 		}

 		if ( error ) {
 			$("#comprobanteArchivos").val("");

 			return;
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-comprobantes span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

 		}

		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'comprobanteArchivos[]';
		$("div.subir-comprobantes").append(cloneElementArchivos);

	}) // $("#comprobanteArchivos").change(function(){

	/*===========================================================
	Abrir el input al presionar el botón Cargar Órdenes de Compra
	===========================================================*/
	$("#btnSubirOrdenes").click(function(){
		document.getElementById('ordenesArchivos').click();
	})

	/*=====================================================
 	Validar tipo y tamaño de los archivos Órdenes de Compra
 	=====================================================*/
 	$("#ordenesArchivos").change(function(){

 		// $("div.subir-ordenes span.lista-archivos").html('');
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

				// $("#ordenesArchivos").val("");
				// $("div.subir-ordenes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				// $("#ordenesArchivos").val("");
				// $("div.subir-ordenes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			}
			// else {

				// $("div.subir-ordenes span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

			// }

 		}

 		if ( error ) {
 			$("#ordenesArchivos").val("");

 			return;
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-ordenes span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

 		}

 		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'ordenesArchivos[]';
		$("div.subir-ordenes").append(cloneElementArchivos);

	}) // $("#comprobanteArchivos").change(function(){

 	/*==================================================
	Abrir el input al presionar el botón Cargar Facturas
	==================================================*/
	$("#btnSubirFacturas").click(function(){
		document.getElementById('facturaArchivos').click();
	})

	/*============================================
 	Validar tipo y tamaño de los archivos Facturas
 	============================================*/
 	$("#facturaArchivos").change(function(){

 		// $("div.subir-facturas span.lista-archivos").html('');
 		// let btnCargarFacturas = document.querySelector("#btnSend.cargar-facturas");
		// if ( btnCargarFacturas !== null ) {
	 		// $(btnEnviar).addClass('d-none');
	 		// $(btnEnviar).prop('disabled', true);
		// }

 		let archivos = this.files;
 		if ( archivos.length == 0) return;

 		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];

			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "application/pdf" && archivo["type"] != "text/xml" ) {

				error = true;

				// $("#facturaArchivos").val("");
				// $("div.subir-facturas span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF o XML!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				// $("#facturaArchivos").val("");
				// $("div.subir-facturas span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			}
			// else {

				// $("div.subir-facturas span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

			// }

 		}

 		if ( error ) {
 			$("#facturaArchivos").val("");

 			return;
 		}

 		let btnCargarFacturas = document.querySelector("#btnSend.cargar-facturas");
 		// if ( btnCargarFacturas !== null && archivos.length && !error ) {
 		if ( btnCargarFacturas !== null ) {
 			$(btnEnviar).removeClass('d-none');
			$(btnEnviar).prop('disabled', false);
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-facturas span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

 		}

 		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'facturaArchivos[]';
		$("div.subir-facturas").append(cloneElementArchivos);

	}) // $("#facturaArchivos").change(function(){

 	/*======================================================
	Abrir el input al presionar el botón Cargar Cotizaciones
	======================================================*/
	$("#btnSubirCotizaciones").click(function(){
		document.getElementById('cotizacionArchivos').click();
	})

	/*================================================
 	Validar tipo y tamaño de los archivos Cotizaciones
 	================================================*/
 	$("#cotizacionArchivos").change(function(){

 		// $("div.subir-cotizaciones span.lista-archivos").html('');
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

				// $("#cotizacionArchivos").val("");
				// $("div.subir-cotizaciones span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

				return false;

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				// $("#cotizacionArchivos").val("");
				// $("div.subir-cotizaciones span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

				return false;

			}
			// else {

				// $("div.subir-cotizaciones span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

			// }

 		}

 		if ( error ) {
 			$("#cotizacionArchivos").val("");

 			return;
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-cotizaciones span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

 		}

 		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'cotizacionArchivos[]';
		$("div.subir-cotizaciones").append(cloneElementArchivos);

	}) // $("#cotizacionArchivos").change(function(){

	/*===============================================
	Abrir el input al presionar el botón Cargar Vales
	===============================================*/
	$("#btnSubirVales").click(function(){
		document.getElementById('valeArchivos').click();
	})

	/*===============================================
	Abrir el input al presionar el botón Cargar Soportes
	===============================================*/
	$("#btnSubirSoportes").click(function(){
		document.getElementById('soporteArchivos').click();
	})

	$("#soporteArchivos").change(function(){

 		let archivos = this.files;
 		if ( archivos.length == 0) return;

 		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];

			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( !archivo["type"].match(/^(application\/pdf|image\/.*)$/) ) {

				error = true;

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF o una imagen (JPG/PNG)!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

				return false;

			}
 		}

 		if ( error ) {
 			$("#soporteArchivos").val("");

 			return;
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-soportes span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

 		}

 		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'soporteArchivos[]';
		$("div.subir-soportes").append(cloneElementArchivos);

	}) // $("#cotizacionArchivos").change(function(){

	/*===============================================
	Abrir el input al presionar el botón Cargar Resguardos
	===============================================*/
	$("#btnSubirResguardos").click(function(){
		document.getElementById('resguardoArchivos').click();
	})
	/*=====================================================
 	Validar tipo y tamaño de los archivos Resguardo
 	=====================================================*/
 	$("#resguardoArchivos").change(function(){

		// $("div.subir-ordenes span.lista-archivos").html('');
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

			   // $("#ordenesArchivos").val("");
			   // $("div.subir-ordenes span.lista-archivos").html('');

			   Swal.fire({
				 title: 'Error en el tipo de archivo',
				 text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				 icon: 'error',
				 confirmButtonText: '¡Cerrar!'
			   })

		   } else if ( archivo["size"] > 4000000 ) {

			   error = true;

			   // $("#ordenesArchivos").val("");
			   // $("div.subir-ordenes span.lista-archivos").html('');

			   Swal.fire({
				 title: 'Error en el tamaño del archivo',
				 text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				 icon: 'error',
				 confirmButtonText: '¡Cerrar!'
			   })

		   }
		   // else {

			   // $("div.subir-ordenes span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

		   // }

		}

		if ( error ) {
			$("#resguardoArchivos").val("");

			return;
		}

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];

			$("div.subir-resguardos span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

		}

		let cloneElementArchivos = this.cloneNode(true);
	   cloneElementArchivos.removeAttribute('id');
	   cloneElementArchivos.name = 'resguardoArchivos[]';
	   $("div.subir-resguardos").append(cloneElementArchivos);

   }) 
	/*====================================================
 	Validar tipo y tamaño de los archivos Vales de Almacén
 	====================================================*/
 	$("#valeArchivos").change(function() {

 		let archivos = this.files;
 		if ( archivos.length == 0) return;

 		let error = false;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];

			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( !archivo["type"].match(/^(application\/pdf|image\/.*)$/) ) {

				error = true;

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

				// return false;
				break;

			} else if ( archivo["size"] > 4000000 ) {

				error = true;

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 4MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

				// return false;
				break;

			}

 		}

 		if ( error ) {
 			$("#valeArchivos").val("");

 			return;
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-vales span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

 		}

 		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'valeArchivos[]';
		$("div.subir-vales").append(cloneElementArchivos);

 	}) // $("#valeArchivos").change(function() {

	/*==============================================
	Abrir el input al presionar el botón Subir Fotos
	==============================================*/
	$("#btnSubirFotos").click(function(){
		document.getElementById('fotos').click();
	})

	/*=====================================================
 	Validar tipo y tamaño de los archivos Órdenes de Compra
 	=====================================================*/
 	$("#fotos").change(function(){

 		// $("div.subir-fotos span.lista-fotos").html('');
 		$("div.subir-fotos span.previsualizar").html('');
 		let archivos = this.files;

 		for (let i = 0; i < archivos.length; i++) {

		    let archivo = archivos[i];

			/*================================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA JPG O PNG
			================================================*/
			
			if ( archivo["type"] != "image/jpeg" && archivo["type"] != "image/png" ) {

				$("#fotos").val("");
				// $("div.subir-fotos span.lista-fotos").html('');
				$("div.subir-fotos span.previsualizar").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser JPG o PNG!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else if ( archivo["size"] > 1000000 ) {

				$("#fotos").val("");
				// $("div.subir-fotos span.lista-fotos").html('');
				$("div.subir-fotos span.previsualizar").html('');

				Swal.fire({
				  title: 'Error en el tamaño del archivo',
				  text: '¡El archivo "'+archivo["name"]+'" no debe pesar más de 1MB!',
				  icon: 'error',
				  confirmButtonText: '¡Cerrar!'
				})

			} else {

				// $("div.subir-fotos span.lista-fotos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

				let datosImagen = new FileReader;
				datosImagen.readAsDataURL(archivo);

				$(datosImagen).on("load", function(event){
					let rutaImagen = event.target.result;

					let elementPicture = `<picture>
											<img src="${rutaImagen}" class="img-fluid img-thumbnail" style="width: 100%">
										</picture>
										<p class="font-italic text-info mb-0">${archivo["name"]}</p>`;
					$("div.subir-fotos span.previsualizar").append(elementPicture);
				})

			}

 		}

	}) // $("#fotos").change(function(){

	// Agregar Partida
	function agregarPartida(){
		let elementCantidad = document.getElementById("cantidad");
		let elementCosto = document.getElementById("costo");
		let elementUnidad = document.getElementById("unidad");
		let elementNumeroParte = document.getElementById("numeroParte");
		let elementConcepto = document.getElementById("concepto");
		let elementCodigo = document.getElementById("codigoId");
		let elementFotos = document.getElementById("fotos");

		let cantidad = elementCantidad.value;
		let costo = elementCosto.value;
		let unidad = elementUnidad.value.trim();
		let numeroParte = elementNumeroParte.value.trim();
		let concepto = elementConcepto.value.trim();
		let codigo = elementCodigo.value;

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

		elementCantidad.classList.remove("is-invalid");
		elementPadre = elementCantidad.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementCosto.classList.remove("is-invalid");
		elementPadre = elementCosto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementUnidad.classList.remove("is-invalid");
		elementPadre = elementUnidad.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementNumeroParte.classList.remove("is-invalid");
		elementPadre = elementNumeroParte.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementConcepto.classList.remove("is-invalid");
		elementPadre = elementConcepto.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;
		// if ( parseFloat(cantidad) == 0 ) {
		if ( parseFloat(cantidad) < 0.01 ) {
			elementCantidad.classList.add("is-invalid");
			elementPadre = elementCantidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		// newContent = document.createTextNode("La cantidad es obligatoria.");
	  		newContent = document.createTextNode("El valor del campo Cantidad no puede ser menor a 0.01.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( cantidad.length > 10 ) {
			elementCantidad.classList.add("is-invalid");
			elementPadre = elementCantidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El campo Cantidad debe ser máximo de 8 dígitos.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		// if ( parseFloat(costo) == 0 ) {
		if ( parseFloat(costo) < 0.01 ) {
			elementCosto.classList.add("is-invalid");
			elementPadre = elementCosto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El campo Costo Unitario no puede ser menor a 0.01.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

		} else if ( costo.length > 15 ) {
			elementCosto.classList.add("is-invalid");
			elementPadre = elementCosto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El campo Costo Unitario debe ser máximo de 13 dígitos.");
		 	newDiv.appendChild(newContent);
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( unidad == '' ) {
			elementUnidad.classList.add("is-invalid");
			elementPadre = elementUnidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La unidad es obligatoria.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( unidad.length > 80 ) {
			elementUnidad.classList.add("is-invalid");
			elementPadre = elementUnidad.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La unidad debe ser máximo de 80 caracteres.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( numeroParte == '' ) {
			elementNumeroParte.classList.add("is-invalid");
			elementPadre = elementNumeroParte.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El número de parte es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( numeroParte.length > 100 ) {
			elementNumeroParte.classList.add("is-invalid");
			elementPadre = elementNumeroParte.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("La número de parte debe ser máximo de 100 caracteres.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( concepto == '' ) {
			elementConcepto.classList.add("is-invalid");
			elementPadre = elementConcepto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El concepto es obligatorio.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( concepto.length > 255 ) {
			elementConcepto.classList.add("is-invalid");
			elementPadre = elementConcepto.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El concepto debe ser máximo de 255 caracteres.");
		 	newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);

			errores = true;
		}

		if ( errores ) return;

		let tableRequisicionDetalles = document.querySelector('#tablaRequisicionDetalles tbody');
		let registros = tableRequisicionDetalles.querySelectorAll('tr');

		let registrosNuevos = tableRequisicionDetalles.querySelectorAll('tr[nuevo]');
		let partida = registrosNuevos.length + 1;

		let elementRow = `<tr nuevo partida="${partida}">
							<td partida class="text-right"><span>${registros.length + 1}</span><input type="hidden" name="detalles[partida][]" value="${partida}"></td>
							<td class="text-right">${cantidad}<input type="hidden" name="detalles[cantidad][]" value="${cantidad}"></td>
							<td class="text-right">${costo}<input type="hidden" name="detalles[costo][]" value="${costo}"></td>
							<td>${unidad}</td>
							<td>${codigo}</td>
							<td numeroParte>${numeroParte}</td>
							<td>${concepto}</td>
						</tr>`;

		$(tableRequisicionDetalles).append(elementRow);

		let rowNuevoRegistro = tableRequisicionDetalles.querySelector(`tr[partida="${partida}"]`);
		let columnaConcepto = rowNuevoRegistro.querySelector('td:last-child');

		let cloneElementcodigoId = elementCodigo.cloneNode(true);
		cloneElementcodigoId.removeAttribute('id');
		cloneElementcodigoId.type = 'hidden';
		cloneElementcodigoId.name = 'detalles[codigoId][]';
		$(columnaConcepto).append(cloneElementcodigoId);

		let cloneElementUnidad = elementUnidad.cloneNode(true);
		cloneElementUnidad.removeAttribute('id');
		cloneElementUnidad.type = 'hidden';
		cloneElementUnidad.name = 'detalles[unidad][]';
		$(columnaConcepto).append(cloneElementUnidad);

		let cloneElementNumeroParte = elementNumeroParte.cloneNode(true);
		cloneElementNumeroParte.removeAttribute('id');
		cloneElementNumeroParte.type = 'hidden';
		cloneElementNumeroParte.name = 'detalles[numeroParte][]';
		$(columnaConcepto).append(cloneElementNumeroParte);

		let cloneElementConcepto = elementConcepto.cloneNode(true);
		cloneElementConcepto.removeAttribute('id');
		cloneElementConcepto.name = 'detalles[concepto][]';
		cloneElementConcepto.classList.add('d-none');
		$(columnaConcepto).append(cloneElementConcepto);

		let cloneElementFotos = elementFotos.cloneNode(true);
		cloneElementFotos.removeAttribute('id');
		cloneElementFotos.name = 'detalle_imagenes['+partida+'][]';
		$(columnaConcepto).append(cloneElementFotos);
		$("#fotos").val("");
		$("div.subir-fotos span.previsualizar").html('');

		elementCantidad.value = '0.00';
		elementUnidad.value = '';
		elementNumeroParte.value = '';
		elementConcepto.value = '';
		elementCodigo.value = '';
		$('#codigoDescripcion').val('');
	}

	let btnAgregarPartida = document.getElementById("btnAgregarPartida");
	if ( btnAgregarPartida != null ) btnAgregarPartida.addEventListener("click", agregarPartida);

	// Eliminar la partida (editando)
	$('#tablaRequisicionDetalles').on("click", "i.eliminarPartida", function (e) {

		let detalleId = $(this).attr("detalleId");
		let elementInput = `<input type="hidden" name="partidasEliminadas[]" value="${detalleId}">`;
		$('#tablaRequisicionDetalles').parent().parent().append(elementInput);

		this.parentElement.parentElement.remove();

		// Renumerar las partidas
		let tableRequisicionDetalles = document.querySelector('#tablaRequisicionDetalles tbody');
		let registros = tableRequisicionDetalles.querySelectorAll('tr');		
		registros.forEach( (registro, index) => {
			registro.querySelector('td[partida] span').innerHTML = index + 1;
		});

	});

	// Descargar Comprobantes de Pago
	$("#btnDescargarComprobantes").click(function(event) {

		event.preventDefault();

		let btnDescargarComprobantes = this;
		let requisicionId = $('#requisicionId').val();
		
		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/comprobantes`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarComprobantes.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarComprobantes.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarComprobantes.disabled = false;
		});

	})

	// Descargar Órdenes de Compra
	$("#btnDescargarOrdenes").click(function(event) {

		event.preventDefault();

		let btnDescargarOrdenes = this;
		let requisicionId = $('#requisicionId').val();

		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/ordenes`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarOrdenes.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarOrdenes.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarOrdenes.disabled = false;
		});

	})

	// Descargar Facturas
	$("#btnDescargarFacturas").click(function(event) {

		event.preventDefault();

		let btnDescargarFacturas = this;
		let requisicionId = $('#requisicionId').val();

		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/facturas`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarFacturas.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarFacturas.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarFacturas.disabled = false;
		});

	})

	// Descargar Cotizaciones
	$("#btnDescargarCotizaciones").click(function(event) {

		event.preventDefault();

		let btnDescargarCotizaciones = this;
		let requisicionId = $('#requisicionId').val();

		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/cotizaciones`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarCotizaciones.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarCotizaciones.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarCotizaciones.disabled = false;
		});

	})

		// Descargar Cotizaciones
	$("#btnDescargarSoportes").click(function(event) {

		event.preventDefault();

		let btnDescargarSoportes = this;
		let requisicionId = $('#requisicionId').val();

		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/soportes`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarSoportes.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarCotizaciones.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarSoportes.disabled = false;
		});

	})

	// Descargar Vales de Almacén
	$("#btnDescargarVales").click(function(event) {

		event.preventDefault();

		let btnDescargarVales = this;
		let requisicionId = $('#requisicionId').val();

		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/vales`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarVales.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarVales.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarVales.disabled = false;
		});

	})

	// Descargar Resguardos
	$("#btnDescargarResguardos").click(function(event) {

		event.preventDefault();

		let btnDescargarResguardos = this;
		let requisicionId = $('#requisicionId').val();
		$.ajax({
			url: `${rutaAjax}requisiciones/${requisicionId}/download/resguardos`,
			method: 'GET',
			dataType: "json",
			beforeSend: () => {
				btnDescargarResguardos.disabled = true;
			}
		})
		.done(function(data) {
			// console.log(data);
			data.archivos.forEach( (archivo, index) => {
				let link = document.createElement('a');
				// link.innerHTML = 'download file';

				link.addEventListener('click', function(event) {
					link.href = rutaAjax+archivo.ruta;
					link.download = archivo.archivo;
				}, false);

				// btnDescargarVales.parentElement.appendChild(link);
				// link.click();
				setTimeout(() => {
					link.click();
				}, TIEMPO_DESCARGA * (index+1));
			});
		})
		.fail(function(error) {
			console.log(error);
			console.log(error.responseJSON);
		})
		.always(function() {
			btnDescargarResguardos.disabled = false;
		});

	})

	$("#btnDescargarTodo").click(function(event) {
		event.preventDefault();
		
		let requisicionId = $('#requisicionId').val();
		window.open(`${rutaAjax}app/Ajax/RequisicionAjax.php?requisicionId=${requisicionId}`, '_blank');

	})

	//==================================================
	// Existencias
	//==================================================

	$('#modalComprobarExistencias').on('shown.bs.modal',function(){
		
		let requisicionId = $('#requisicionId').val();
		
		if ( !$.fn.DataTable.isDataTable('#tablaExistencias') ) {
			
		$.ajax({
		    url: `${rutaAjax}app/Ajax/RequisicionAjax.php?accion=existencias&requisicionId=${requisicionId}`,
		    method: 'GET',
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(data){
				tablaExistencias = $('#tablaExistencias').DataTable({

					autoWidth: false,
					responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
					info: false,
					
					data: data.datos.registros,
					columns: data.datos.columnas,
					columnDefs: [
						{
							render: DataTable.render.select(),
							targets: 0
						},
						{
							targets: 2, // Columna donde quieres agregar el input
							render: function (td, cellData, rowData, row, col) {
								
								return '<input class="form-control form-control-sm cantidad" min="1" max="'+rowData.cantidad_disponible+'" type="number" value="' + rowData.cantidad + '">';
							}
						},
						{ targets: [0,1,2,3,4], orderable: false }
					],
					select: {
						style: 'multi',
						selector: 'td:first-child'
					},
					language: LENGUAJE_DT,
					aaSorting: [],
	
				})

		    }

		})
		
		}
	});

	$('#tablaExistencias').on('change', '.cantidad', function() {

		var rowIndex = $(this).closest('tr').index();
		let newValue = $(this).val()
		
		if (isNaN(newValue) || newValue == "") newValue = 1

		tablaExistencias.cell(rowIndex, 2).data(newValue).draw();
	});

	$('#tablaExistencias').on('change', '.dt-select-checkbox', function() {
		var table = $('#tablaExistencias').DataTable();
		var selectedRows = table.rows({ selected: true }).data();
		var descriptions = {};
	
		// Iterar solo sobre las filas seleccionadas
		selectedRows.each(function(index, rowData) {
			var descripcion = rowData.descripcion;
	
			// Verificar si la descripción ya existe en el objeto
			if (descriptions[descripcion]) {
				// Deshabilitar el checkbox y mostrar un mensaje (opcional)
				table.row(index).deselect();
				$(table.row(index).node()).find('.dt-select-checkbox').prop('disabled', true);
			} else {
				// Agregar la descripción al objeto
				descriptions[descripcion] = true;
			}
		});
	});

	$('#btnCrearEntrada').click(function(event) {

		if ( tablaExistencias.rows('.selected').data().length == 0 ) {
			crearToast('bg-danger','Error','', 'No se ha seleccionado ningún registro');
			return;
			
		}

		$.ajax({
			url: `${rutaAjax}app/Ajax/InventarioAjax.php`,
			method: 'POST',
			data: {
				'_token': $('input[name=_token]').val(),
				'requisicionId': $('#requisicionId').val(),
				'observaciones': 'Traslado de Material a Obra ',
				'accion': 'crearEntrada',
				'existencias': tablaExistencias.rows('.selected').data().toArray()
			},
			dataType: "json",
			success:function(data){
				
				crearToast('bg-success','','', 'Entrada creada correctamente');
				window.location.reload();
				
			}
		})
	});

	//==================================================
	// Porveedores
	//==================================================

	$('#modalSeleccionarProveedor').on('shown.bs.modal',function(){
		
		if ( !$.fn.DataTable.isDataTable('#tablaProveedores') ) {
			let columnas = [
				{ data: 'consecutivo' },
				{ data: 'proveedor' },
				{ data: 'direccion' },
				{ data: 'correo' },
				{ data: 'estrellas' },
				{ data: 'telefono' }
			];
			$.ajax({
				url: `${rutaAjax}app/Ajax/ProveedorAjax.php?accion=listar`,
				method: 'GET',
				cache: false,
				contentType: false,
				processData: false,
				dataType: "json",
				success:function(data){
					
					tablaProveedores = $('#tablaProveedores').DataTable({

						autoWidth: false,
						responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
						info: false,
						
						data: data.datos.registros,
						columns: data.datos.columnas,
						columnDefs: [
							// { targets: [0,1,2,3,4], orderable: false }
						],
						createdRow: (row, data, index) => {
							row.classList.add('seleccionable');
						}

					})
				}
			})
		}
	})

	$('#tablaProveedores').on('click', 'tbody tr.seleccionable', function () {
		let data = tablaProveedores.row(this).data();

		$('#proveedor').val(data["proveedor"]);
		$('#telefono').val(data["telefono"]);
		$('#email').val(data["email"]);

		$('#proveedor').val(data["proveedor"]);
		$('#proveedorId').val(data["id"]);

		document.getElementById('calificacion').innerHTML = `<strong>${data.estrellas}/5</strong>`;
	
		$('#modalSeleccionarProveedor').modal('hide');
	});

	$('#btnSolicitarFacturas').on('click', function() {
		let boton = this;
		Swal.fire({
            title: 'Ingrese la orden de compra',
            input: 'number',
            inputAttributes: {
				required: true, // Hace que el campo sea obligatorio
                autocapitalize: 'off'
            },
            showCancelButton: true,
            confirmButtonText: 'Enviar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                const valor = result.value;
				boton.disabled = true;
				$('.loader').removeClass('d-none');
                // Realizar la petición AJAX
                $.ajax({
                    url: `${rutaAjax}app/Ajax/RequisicionAjax.php`, // Reemplaza con la URL de tu endpoint
                    type: 'POST',
                    data: {
						'accion': 'solicitarFacturas',
						'_token': $('input[name=_token]').val(),
						'requisicionId': $('#requisicionId').val(),
						'ordenCompra': valor,
						'proveedorId': $('#proveedorId').val()
					},
                    success: function(response) {
                        // Si la respuesta es exitosa
                        Swal.fire('¡Éxito!', 'El valor ha sido procesado correctamente.', 'success');
						boton.disabled = false;
						$('.loader').addClass('d-none');
                    },
                    error: function(error) {
                        // Si ocurre un error
                        Swal.fire('Error', 'Ha ocurrido un error al enviar el correo.', 'error');
						boton.disabled = false;
						$('.loader').addClass('d-none');
                    }
                });
            }
        });
		
	});

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
	$(".input-group.date2").datetimepicker({
    	format: "DD/MMMM/YYYY HH:mm",
    	defaultDate: moment().add(4, "days"),
    	stepping: 15, // Incremento de 15 minutos para seleccionar la hora
  	});

    let elementServicioEstatusId = $('#servicioEstatusId.select2.is-invalid');
    if ( elementServicioEstatusId.length == 1) {
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

	  //==============================================================
  // Subir Cotizacion
  //==============================================================
  $(".btn-upload-document").click(function () {
    let dataId = $(this).data("id"); // Get the data-id from the button
    $("#fileCotizacion").data("id", dataId); // Set the data-id on the input element
    document.getElementById("fileCotizacion").click();
  });

  /*================================================
	Validar tipo y tamaño de los archivos Cotizacion
	================================================*/
  $("#fileCotizacion").change(function () {
    let dataId = $(this).data("id"); // Retrieve the data-id from the input element

    let archivos = this.files;
    if (archivos.length === 0) return;

    let error = false;
    const tiposPermitidos = ["application/pdf"];

    for (let i = 0; i < archivos.length; i++) {
      let archivo = archivos[i];

      // Validamos tipo de archivo
      if (!tiposPermitidos.includes(archivo["type"])) {
        error = true;

        Swal.fire({
          title: "Error en el tipo de archivo",
          text: '¡El archivo "' + archivo["name"] + '" debe ser PDF!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });

        return false;
      }

      // Validamos tamaño
      if (archivo["size"] > 4000000) {
        error = true;

        Swal.fire({
          title: "Error en el tamaño del archivo",
          text:
            '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });

        return false;
      }
    }

    if (error) {
      $("#fileCotizacion").val("");
      return;
    }

    let formData = new FormData();
    for (let i = 0; i < archivos.length; i++) {
      formData.append("cotizacionArchivos[]", archivos[i]);
    }
    formData.append("_token", $('input[name="_token"]').val());
    formData.append("accion", "subirCotizacion");
    formData.append("requisicionId", $("#requisicionId").val());
    formData.append("proveedorId", dataId);

    $.ajax({
      url: `${rutaAjax}app/Ajax/CotizacionesAjax.php`,
      method: "POST",
      data: formData,
      cache: false,
      contentType: false,
      processData: false,
      dataType: "json",
      beforeSend: function () {
        Swal.fire({
          title: "Subiendo archivos...",
          text: "Por favor, espere.",
          icon: "info",
          showConfirmButton: false,
          allowOutsideClick: false,
        });
      },
      success: function (response) {
        if (response.error) {
          Swal.fire({
            title: "Error",
            text: response.errorMessage,
            icon: "error",
            confirmButtonText: "¡Cerrar!",
          });
        } else {
          Swal.fire({
            title: "¡Éxito!",
            text: "Los archivos se han subido correctamente.",
            icon: "success",
            confirmButtonText: "¡Cerrar!",
          }).then(() => {
            location.reload();
          });
        }
      },
      error: function (error) {
        Swal.fire({
          title: "Error",
          text: "Ocurrió un error al subir los archivos.",
          icon: "error",
          confirmButtonText: "¡Cerrar!",
        });
      },
    });
  });

  $('#proveedorId').on('change', function (e) {
	if ( $(this).val() !== '' ) {
		$.ajax({
			url: `${rutaAjax}app/Ajax/ProveedorAjax.php?accion=obtenerVendedores&proveedorId=${$(this).val()}`,
			method: 'GET',
			dataType: "json",
			success:function(data){
				let elementoVendedor = document.getElementById('vendedorId');
				// Vaciar el select antes de agregar nuevas opciones
				elementoVendedor.innerHTML = '';
				// Agregar la opción por defecto
				elementoVendedor.innerHTML += '<option value="">Seleccione un vendedor</option>';
				data.vendedores.forEach(function(vendedor) {
					elementoVendedor.innerHTML += '<option value="' + vendedor.id + '">' + vendedor.nombreCompleto + '</option>';
				});
			}
		});
	}
  });
  
});
