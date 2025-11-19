$(function(){

	let tableList = document.getElementById('tablaRequisiciones');
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
		        	if ( data.colorTexto != '' ) $('td', row).eq(3).css("color", data.colorTexto);
		        	if ( data.colorFondo != '' ) $('td', row).eq(3).css("background-color", data.colorFondo);
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
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/RequisicionTrasladoAjax.php', '#tablaRequisiciones', parametrosTableList);

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
		let ubicacionId = $('#filtroUbicacionId').val();
		let maquinariaId = $('#filtroMaquinariaId').val();
		let servicioEstatusId = $('#filtroServicioEstatusId').val();
		let fechaInicial = $('#filtroFechaInicial').val();
		let fechaFinal = $('#filtroFechaFinal').val();
		let concepto = $('#filtroConcepto').val();


		if ( fechaInicial == '' ) fechaInicial = 0;
		if ( fechaFinal == '' ) fechaFinal = 0;

		// fAjaxDataTable(`${rutaAjax}app/Ajax/RequisicionAjax.php?empresaId=${empresaId}&ubicacionId=${ubicacionId}&maquinariaId=${maquinariaId}&servicioEstatusId=${servicioEstatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}`, '#tablaRequisiciones');
		fActualizarListado(`${rutaAjax}app/Ajax/RequisicionAjax.php?empresaId=${empresaId}&ubicacionId=${ubicacionId}&maquinariaId=${maquinariaId}&servicioEstatusId=${servicioEstatusId}&fechaInicial=${fechaInicial}&fechaFinal=${fechaFinal}&concepto=${concepto}`, '#tablaRequisiciones', parametrosTableList);
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

	// Habilitar observaciones al cambiar de estatus
	$("#servicioEstatusId").change(function(){
		let actualServicioEstatusId = $('#actualServicioEstatusId').val();
		if ( actualServicioEstatusId === '' ) return;

		let observacion = document.getElementById('observacion');
		if ( observacion === null ) return;

		if ( actualServicioEstatusId == this.value ) {
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

	// Confirmar la eliminación de los Archivos
	$("div.subir-comprobantes").on("click", "i.eliminarArchivo", function (e) {

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
	// Agregar Partida
	function agregarPartida(){
		let elementCantidad = document.getElementById("cantidad");
		let elementUnidad = document.getElementById("unidad");
		let elementNumeroParte = document.getElementById("numeroParte");
		let elementConcepto = document.getElementById("concepto");
		let elementFotos = document.getElementById("fotos");

		let cantidad = elementCantidad.value;
		let unidad = elementUnidad.value.trim();
		let numeroParte = elementNumeroParte.value.trim();
		let concepto = elementConcepto.value.trim();

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

		elementCantidad.classList.remove("is-invalid");
		elementPadre = elementCantidad.parentElement;
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
		// let ultimaPartida = tableRequisicionDetalles.lastElementChild;
		// let partida = ( ultimaPartida === null ) ? 1 : parseInt(ultimaPartida.getAttribute('partida')) + 1;

		let registrosNuevos = tableRequisicionDetalles.querySelectorAll('tr[nuevo]');
		let partida = registrosNuevos.length + 1;

		// let elementRow = `<tr nuevo partida="${partida}">
		// 					<td partida class="text-right"><span>${registros.length + 1}</span><input type="hidden" name="detalles[partida][]" value="${partida}"></td>
		// 					<td class="text-right">${cantidad}<input type="hidden" name="detalles[cantidad][]" value="${cantidad}"></td>
		// 					<td>${unidad}<input type="hidden" name="detalles[unidad][]" value="${unidad}"></td>
		// 					<td>${numeroParte}<input type="hidden" name="detalles[numeroParte][]" value="${numeroParte}"></td>
		// 					<td>${concepto}<input type="hidden" name="detalles[concepto][]" value="${concepto}"></td>
		// 				</tr>`;
		let elementRow = `<tr nuevo partida="${partida}">
							<td partida class="text-right"><span>${registros.length + 1}</span><input type="hidden" name="detalles[partida][]" value="${partida}">
							<button type='button' class='btn btn-xs btn-danger ml-1 eliminar'>
								<i class='far fa-times-circle'></i>
							</button></td>
							<td class="text-right">${cantidad}<input type="hidden" name="detalles[cantidad][]" value="${cantidad}"></td>
							<td>${unidad}</td>
							<td numeroParte>${numeroParte}</td>
							<td>${concepto}</td>
						</tr>`;

		$(tableRequisicionDetalles).append(elementRow);

		let rowNuevoRegistro = tableRequisicionDetalles.querySelector(`tr[partida="${partida}"]`);
		let columnaConcepto = rowNuevoRegistro.querySelector('td:last-child');

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

		elementCantidad.value = '0.00';
		elementUnidad.value = '';
		elementNumeroParte.value = '';
		elementConcepto.value = '';
	}

	let btnAgregarPartida = document.getElementById("btnAgregarPartida");
	if ( btnAgregarPartida != null ) btnAgregarPartida.addEventListener("click", agregarPartida);
	$('#tablaRequisicionDetalles').on("click", "button.eliminar", function (event) {
		this.parentElement.parentElement.remove();
		let tableRequisicionDetalles = document.getElementById('tablaRequisicionDetalles');

		// Renumerar las partidas
		let table = tableRequisicionDetalles.querySelector('tbody');
		let registros = table.querySelectorAll('tr');
		registros.forEach( (registro, index) => {
			registro.setAttribute('partida', index + 1);
			registro.querySelector('td[partida] span').innerHTML = index + 1;
		});

	});
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

    $("#btnDescargarTodo").click(function(event) {
		event.preventDefault();

		let requisicionId = $('#requisicionId').val();
		window.open(`${rutaAjax}app/Ajax/RequisicionAjax.php?requisicionId=${requisicionId}`, '_blank');
	})

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
    let elementServicioEstatusId = $('#servicioEstatusId.select2.is-invalid');
    if ( elementServicioEstatusId.length == 1) {
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-servicioEstatusId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
	}

});
