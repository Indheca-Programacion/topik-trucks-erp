$(function(){

    let tableList = document.getElementById('tablaGastos');
	let tableGastos = document.getElementById('tablaDetallesGastos');
	let campoMaquinariaId = document.getElementById('maquinariaId');
	let elementSectionPartida= document.querySelector('#addPartidas');
	let elementSectionGasto= document.querySelector('#gasto-section');
	let parametrosTableList = { responsive: true };

	if (tableGastos != null){
		let gastoId = $('#gastoId').val()
		$.ajax({
			url: `${rutaAjax}app/Ajax/GastosAjax.php?gasto=${gastoId}`,
			method: 'GET',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
		}).done(function(respuesta) {
			$('#tablaDetallesGastos').DataTable({
				autoWidth: false,
				info: false,
				paging: false,
				searching: false,
				responsive:  true ,
				data: respuesta.datos.registros,
				columns:respuesta.datos.columnas,
				columnDefs: [
					{targets: '_all', 
					orderable: false }
				],
				language: LENGUAJE_DT,
				"createdRow": function( row, data, dataIndex){
                if( data.cancelada==1){
                    $(row).addClass('bg-danger');
                }
            }
			});
		}).fail(function(error){
			console.log(error)
		});
	}

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/GastosAjax.php', '#tablaGastos', parametrosTableList);

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

			$(idTabla).DataTable({

				autoWidth: false,
				responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,

		        columnDefs: [
					{
						targets: 3, // Suponiendo que la columna "Estado" es la tercera (índice 2)
                		render: function ( data, type, row ) {
							if (data === 'ABIERTO') {
								badgeClass = 'badge badge-success';
							} else if (data === 'EN PROCESO') {
								badgeClass = 'badge badge-primary';
							} else if (data === 'PROCESADO') {
								badgeClass = 'badge badge-info';
							} else if (data === 'PAGADO') {
								badgeClass = 'badge badge-danger';
							} else {
								badgeClass = 'badge badge-secondary';
							}
							return '<span class="' + badgeClass + '">' + data + '</span>';
						}
					}
				],

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
	}

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
		let obraId = $('#filtroObraId').val();
		let usuarioId = $('#filtroUsuarioId').val();
		let tipogastoId = $('#filtroTipoGasto').val();

		fActualizarListado(`${rutaAjax}app/Ajax/GastosAjax.php?empresaId=${empresaId}&obraId=${obraId}&usuarioId=${usuarioId}&tipogastoId=${tipogastoId}`, '#tablaGastos');
	});

	// Confirmar la eliminación del Gastos
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Gastos (Descripción: '+folio+') ?',
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
	
	$(tableGastos).on("click", "button.observacion", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");

	    Swal.fire({
			title: 'Partida Cancelada',
			text: folio,
			icon: 'error',
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			cancelButtonText:  'Cerrar'
	    })

	});

	$(tableGastos).on("click", "button.cancelarPartida", async function (e) {
		var folio = $(this).attr("folio");
		var detalleId = $(this).attr("detalle");
		let token = $('input[name="_token"]').val();
		let gastoId = $('#gastoId').val()

		const observaciones = await Swal.fire({
			input: "textarea",
			inputLabel: "Observaciones",
			inputPlaceholder: "Ingrese las observaciones",
			inputAttributes: {
				"aria-label": "Type your message here"
			},
			title: '¿Estás Seguro de querer cancelar esta partida (Descripción: '+folio+') ?',
			icon: 'warning',
			showCancelButton: true,
			cancelButtonColor: '#d33',
			confirmButtonText: 'Confirmar',
			cancelButtonText:  'Cancelar',
			inputValidator: (value) => {
				if (!value) {
				  return "Se tiene que escribir una observacion";
				}
			  }
		});

		if (!observaciones.isConfirmed) {
			return;
		}

		let datos = new FormData();
		datos.append("accion", "cancelarPartida");
		datos.append("_token", token);
		datos.append("gastoDetalleId", detalleId);
		datos.append("gastoId", gastoId);
		datos.append("observacion", observaciones.value);
		$.ajax({
		    url: rutaAjax+'app/Ajax/GastosAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				location.reload();

		    }
		})
		
	});

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

	//select 2 y datetimepickers
	$('.select2').select2({
		language: 'es',
		tags: false,
		width: '100%'
		// theme: 'bootstrap4'
	});

	$('#datetimepicker5').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});
	$('#datetimepicker1').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});
	$('#datetimepicker2').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});
	$('#datetimepicker3').datetimepicker({
		format: 'DD/MMMM/YYYY'
	});

	/*========================================================
	ARCHIVOS
	========================================================*/

	let gastoDetalleId= null;

	$("#btnSubirArchivos").click(function(){
		document.getElementById('archivo').click();
	})

	$(document).on('click',"#btn-subirArchivo",function(){
		gastoDetalleId = this.getAttribute('folio')
		document.getElementById('archivoSubir').click();
	})

	$("#archivoSubir").change(function() {

		let archivos = this.files;
		console.log(archivos)
		let token = document.getElementById('_token');
		if ( archivos.length == 0) return;

		let error = false;

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];
			
			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "application/pdf" && archivo["type"] != "text/xml" ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
					title: 'Error en el tipo de archivo',
					text: '¡El archivo "'+archivo["name"]+'" debe ser PDF o XML!',
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
			$("#archivo").val("");

			return;
		}

		let formData = new FormData();

		// Iteramos sobre todos los archivos seleccionados
		for (let i = 0; i < this.files.length; i++) {
			formData.append('archivos[]', this.files[i]); // El nombre del archivo en el servidor será archivos[]
		}

		formData.append("accion","subir-archivo")
		formData.append("gastoDetalleId",gastoDetalleId)
		formData.append("_token",token.value)
		// Enviamos la solicitud AJAX
		fetch(rutaAjax+"app/Ajax/GastosAjax.php", {
			method: 'POST',
			body: formData
		})
		.then(response => response.text())
		.then(data => {
			crearToast('bg-success', 'Insertar Documentos', 'OK', data.respuestaMessage);
			location.reload();
		})
		.catch(error => {
		console.error('Error:', error);
		});

   	}) // $("#archivos").change(function(){
	
	$("#archivo").change(function() {

 		// $("div.subir-comprobantes span.lista-archivos").html('');
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

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
				  title: 'Error en el tipo de archivo',
				  text: '¡El archivo "'+archivo["name"]+'" debe ser PDF o XML!',
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

			if (archivo["type"] == "text/xml") {
				// Leer el archivo XML y obtener sus valores
				const reader = new FileReader();
				reader.onload = function(e) {
					const contenidoXML = e.target.result;
					const valoresXML = obtenerValoresXML(contenidoXML);
					// Puedes usar valoresXML aquí, por ejemplo:
					let fechaFactura = new Date(valoresXML.fecha);
					$("#datetimepicker3").val(fFechaLarga(fechaFactura));
					$("#proveedor").val(valoresXML.proveedor);
					$("#factura").val(valoresXML.folio);
					$("#costo").val(valoresXML.costoTotal);

					if (valoresXML.items.length < 1) {
						$("#cantidad").val(valoresXML.items[0].cantidad);
						$("#unidad").val(valoresXML.items[0].unidad);
						$("#observaciones").val(valoresXML.items[0].descripcion);
					}else{
						let items = valoresXML.items;
						
						// Limpiar el select antes de agregar opciones
						$('#selectItemFactura').empty();
						items.forEach((item, idx) => {
							let texto = `${item.descripcion} | Cantidad: ${item.cantidad} | Unidad: ${item.unidad} | Importe: ${item.importe}`;
							$('#selectItemFactura').append(`<option value="${idx}">${texto}</option>`);
						});

						$('#modalSeleccionarItem').modal('show');

						$('#btnSeleccionarItemFactura').on('click', function() {
							let idx = $('#selectItemFactura').val();
							let item = items[idx];
							$("#cantidad").val(item.cantidad);
							$("#unidad").val(item.unidad);
							$("#observaciones").val(item.descripcion);
							$('#modalSeleccionarItem').modal('hide');
						});
					}
				};
				reader.readAsText(archivo);
			}

 		}

		const tipoGasto = $('#tipoGasto').val();
		let archivos_contador = 0;

		if (tipoGasto == 2) {
			const archivosInputs = document.querySelectorAll('input[name="archivos[]"]');
			archivosInputs.forEach(input => {
				archivos_contador++
			});
	
			if(archivos_contador > 1){
				error = true;
	
					Swal.fire({
					  title: 'Error cantidad de archivos',
					  text: 'Se alcanzo el maximo de archivos',
					  icon: 'error',
					  confirmButtonText: '¡Cerrar!'
					})
			}
			
		}

 		if ( error ) {
 			$("#archivo").val("");

 			return;
 		}

 		for (let i = 0; i < archivos.length; i++) {

 			let archivo = archivos[i];

 			$("div.subir-archivos span.lista-archivos").append('<p class="font-italic text-info mb-0">'+archivo["name"]+'</p>');

 		}

		let cloneElementArchivos = this.cloneNode(true);
		cloneElementArchivos.removeAttribute('id');
		cloneElementArchivos.name = 'archivos[]';
		$("div.subir-archivos").append(cloneElementArchivos);

	}) // $("#archivos").change(function(){

	let modalVerArchivos = document.querySelector('#modalVerArchivos');
	
	$(document).on('click','.btn-mostrar-modal',function() {
		let folio = this.getAttribute('folio').toUpperCase();
		$("#modalVerArchivosLabel span").html(folio);
		$("#modalVerArchivos div#accordionArchivos").html('');

		let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');
		$(elementErrorValidacion).addClass("d-none");

		let token = $('input[name="_token"]').val();
		let gastoDetalleId = $(this).attr("folio");

		let datos = new FormData();
		datos.append("accion", "verArchivos");
		datos.append("_token", token);
		datos.append("gastoDetalleId", gastoDetalleId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/GastosAjax.php',
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
					let elementIconoEliminar =  `<i class="fas fa-trash-alt fa-xs text-danger eliminarArchivo" style="cursor: pointer; position: absolute; top: 12px; right: 8px;" gastoDetalleId="${archivo.gastoDetalleId}" archivoId="${archivo.id}" folio="${archivo.titulo}"></i>`;

					let elementArchivo = `
						<div class="card">
							<div class="card-header px-0 py-2" id="heading-${index+1}">
								<h2 class="mb-0">
									<button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse" data-target="#collapse-${index+1}" aria-expanded="false" aria-controls="collapse-${index+1}">
          								${archivo.titulo}
									</button>
									<a class="btn btn-link btn-block text-left collapsed" href="${archivo.ruta}" download="${archivo.titulo}"><i class="fas fa-download"></i></a>
									${elementIconoEliminar}
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
		let gastoDetalleId = $(btnEliminar).attr("gastoDetalleId");

		let datos = new FormData();
		datos.append("_token", token);
		datos.append("accion", "eliminarArchivo");
		datos.append("archivoId", archivoId);
		datos.append("gastoDetalleId", gastoDetalleId);
		$.ajax({
		    url: rutaAjax+"app/Ajax/GastosAjax.php",
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

	/*========================================================
	PARTIDAS
	========================================================*/

	$('#btnAddPartida').on('click',function (e){

		let elementErrorValidacion = elementSectionPartida.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");

		let btnGuardar = this;
		let gastoId = $('#gastoId').val()

		const archivosInputs = document.querySelectorAll('input[name="archivos[]"]');

		// Iterar sobre cada input y verificar si tiene un archivo seleccionado
		let archivos = false;
		let archivoPDF = false;
		let archivoXML = false;
		archivosInputs.forEach(input => {
			const extension = input.files[0].type
			if (input.files.length > 0) {
				if (extension === 'application/pdf') {
					archivoPDF = true;
				  } else if (extension === 'text/xml') {
					archivoXML = true;
				  }
				archivos = true;
				; // Detener la iteración si se encontró al menos uno
			}
		});
		const tipoGasto = $('#tipoGasto').val();

		// if(!archivoPDF || (!archivoXML && tipoGasto == 1)){
		// 	let texto =""
		// 	if (!archivoPDF && !archivoXML) {
		// 		texto= "Necesita su soporte para agregar."
		// 	}else if ( !archivoPDF ) {
		// 		texto= "Necesita su soporte PDF para agregar."
		// 	}else if( !archivoXML ) {
		// 		texto= "Necesita su soporte XML para agregar."
		// 	}
		// 	let elementList = document.createElement('li'); // prepare a new li DOM element
		// 	let newContent = document.createTextNode(texto);
		// 	elementList.appendChild(newContent); //añade texto al div creado.
		// 	elementErrorValidacion.querySelector('ul').appendChild(elementList);
		// 	$(elementErrorValidacion).removeClass("d-none");
		// 	return;
		// } 

		let token = document.getElementById('_token');

		let datosPost = new FormData(document.getElementById("formAddPartida"));
		datosPost.append("_token",token.value)
		datosPost.append("gastoId",gastoId)
		datosPost.append("accion","agregarPartida")
		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datosPost,
			cache: false,
			contentType: false,
			 processData: false,
			 dataType: 'json',
			beforeSend: () => {
				$(btnGuardar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {

			if ( respuesta.error ) {

				if ( respuesta.errors ) {
	
					// console.log(Object.keys(respuesta.errors))
					let errors = Object.values(respuesta.errors);
	
					// respuesta.errors.forEach( (item, index) => {
					errors.forEach( (item) => {
						let elementList = document.createElement('li'); // prepare a new li DOM element
						let newContent = document.createTextNode(item);
						elementList.appendChild(newContent); //añade texto al div creado.
						elementErrorValidacion.querySelector('ul').appendChild(elementList);
					});
	
				} else {
	
					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(respuesta.errorMessage);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);
	
				}
	
				$(elementErrorValidacion).removeClass("d-none");
	
				$(btnGuardar).prop('disabled', false);
	
				return;
	
			}
			crearToast('bg-success', 'Crear Indirecto', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
	
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el indirecto, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnGuardar).prop('disabled', false);
		});

	})

	$(tableGastos).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Detalle (Descripción: '+folio+') ?',
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

	$('#btnCancelarPartida').on('click',function() {
		let elementErrorValidacion = elementSectionGasto.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");

		let btnGuardar = this
		let token = document.getElementById('_token');
		let gastoId = $('#gastoId').val();

		let datos = new FormData();
		datos.append("accion", "crearRequisicion");
		datos.append("_token", token.value);
		datos.append("gastoId", gastoId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/RequisicionGastoAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				location.reload()
		    }
		}).done(function(respuesta) {

			if ( respuesta.error ) {

				if ( respuesta.errors ) {
	
					// console.log(Object.keys(respuesta.errors))
					let errors = Object.values(respuesta.errors);
	
					// respuesta.errors.forEach( (item, index) => {
					errors.forEach( (item) => {
						let elementList = document.createElement('li'); // prepare a new li DOM element
						let newContent = document.createTextNode(item);
						elementList.appendChild(newContent); //añade texto al div creado.
						elementErrorValidacion.querySelector('ul').appendChild(elementList);
					});
	
				} else {
	
					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(respuesta.errorMessage);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);
	
				}
	
				$(elementErrorValidacion).removeClass("d-none");
	
				$(btnGuardar).prop('disabled', false);
	
				return;
	
			}

			crearToast('bg-success', 'Crear Requisicion', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
	
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar crear la Requisicion, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnGuardar).prop('disabled', false);
		});
	});

	/*========================================================
	GASTOS
	========================================================*/

	$('#btnCerrarGasto').on('click',function (e){
		let elementErrorValidacion = elementSectionGasto.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");
		
		let btnGuardar = this;
		let gastoId = $('#gastoId').val()

		let token = document.getElementById('_token');

		let datosPost = new FormData();
		datosPost.append("_token",token.value)
		datosPost.append("gastoId",gastoId)
		datosPost.append("accion","cerrarGasto")
		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: datosPost,
			cache: false,
			contentType: false,
			 processData: false,
			 dataType: 'json',
			beforeSend: () => {
				$(btnGuardar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {

			if ( respuesta.error ) {

				if ( respuesta.errors ) {
	
					// console.log(Object.keys(respuesta.errors))
					let errors = Object.values(respuesta.errors);
	
					// respuesta.errors.forEach( (item, index) => {
					errors.forEach( (item) => {
						let elementList = document.createElement('li'); // prepare a new li DOM element
						let newContent = document.createTextNode(item);
						elementList.appendChild(newContent); //añade texto al div creado.
						elementErrorValidacion.querySelector('ul').appendChild(elementList);
					});
	
				} else {
	
					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(respuesta.errorMessage);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);
	
				}
	
				$(elementErrorValidacion).removeClass("d-none");
	
				$(btnGuardar).prop('disabled', false);
	
				return;
	
			}

			crearToast('bg-success', 'Cerrar Gasto', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
	
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar cerrar el gasto, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnGuardar).prop('disabled', false);
		});

	})

	$("#btnDownload").click(function(event) {
		event.preventDefault();

		let gastoId = $('#gastoId').val();
		window.open(`${rutaAjax}app/Ajax/GastosAjax.php?gastoId=${gastoId}`, '_blank');
	})

	$('#btnRevisar').on('click',function (e){
		let gastoId = $('#gastoId').val()
		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: {
				accion: 'revisarGasto',
				gastoId: gastoId,
				_token: $('input[name="_token"]').val()
			},
			dataType: 'json',
			success: function(respuesta) {
				if ( respuesta.error ) {
					swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}
				swal.fire({
					title: 'Revisar Gasto',
					text: respuesta.respuestaMessage,
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}). then((result) => {
					if (result.isConfirmed) {
						location.reload();
					}
				});
			}
		})
	})

	$('#btnAutorizar').on('click',function (e){
		let gastoId = $('#gastoId').val()
		$.ajax({
			url: rutaAjax+'app/Ajax/GastosAjax.php',
			method: 'POST',
			data: {
				accion: 'autorizarGasto',
				gastoId: gastoId,
				_token: $('input[name="_token"]').val()
			},
			dataType: 'json',
			success: function(respuesta) {
				if ( respuesta.error ) {
					swal.fire({
						title: 'Error',
						text: respuesta.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}
				swal.fire({
					title: 'Autorizar Gasto',
					text: respuesta.respuestaMessage,
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}). then((result) => {
					if (result.isConfirmed) {
						location.reload();
					}
				});
			}
		})
	})

	$('#btnCrearRequisicion').on('click',function() {
		let elementErrorValidacion = elementSectionGasto.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");

		let btnGuardar = this
		let token = document.getElementById('_token');
		let gastoId = $('#gastoId').val();

		let datos = new FormData();
		datos.append("accion", "crearRequisicion");
		datos.append("_token", token.value);
		datos.append("gastoId", gastoId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/RequisicionGastoAjax.php',
		    method: 'POST',
		    data: datos,
		    cache: false,
		    contentType: false,
		    processData: false,
		    dataType: "json",
		    success:function(respuesta) {

				if ( respuesta.error ) {
					let elementErrorValidacion = modalVerArchivos.querySelector('.error-validacion');

					elementErrorValidacion.querySelector('ul li').innerHTML = respuesta.errorMessage;
					$(elementErrorValidacion).removeClass("d-none");

					return;
				}

				location.reload()
		    }
		}).done(function(respuesta) {

			if ( respuesta.error ) {

				if ( respuesta.errors ) {
	
					// console.log(Object.keys(respuesta.errors))
					let errors = Object.values(respuesta.errors);
	
					// respuesta.errors.forEach( (item, index) => {
					errors.forEach( (item) => {
						let elementList = document.createElement('li'); // prepare a new li DOM element
						let newContent = document.createTextNode(item);
						elementList.appendChild(newContent); //añade texto al div creado.
						elementErrorValidacion.querySelector('ul').appendChild(elementList);
					});
	
				} else {
	
					let elementList = document.createElement('li'); // prepare a new li DOM element
					let newContent = document.createTextNode(respuesta.errorMessage);
					elementList.appendChild(newContent); //añade texto al div creado.
					elementErrorValidacion.querySelector('ul').appendChild(elementList);
	
				}
	
				$(elementErrorValidacion).removeClass("d-none");
	
				$(btnGuardar).prop('disabled', false);
	
				return;
	
			}

			crearToast('bg-success', 'Crear Requisicion', 'OK', respuesta.respuestaMessage);
			location.reload();
		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
	
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar crear la Requisicion, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnGuardar).prop('disabled', false);
		});
	});

	$(campoMaquinariaId).on('change', function (e) {

		$("#maquinariaTipoDescripcion").val('');
		$("#ubicacionId").val('');
		$("#maquinariaUbicacionDescripcion").val('');
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
						$("#ubicacionId").val(data.datos.maquinaria['ubicacionId']).trigger('change');
						$("#maquinariaUbicacionDescripcion").val(data.datos.maquinaria['ubicaciones.descripcion']);
					// }
					$("#maquinariaMarcaDescripcion").val(data.datos.maquinaria['marcas.descripcion']);
					$("#maquinariaModeloDescripcion").val(data.datos.maquinaria['modelos.descripcion']);
					$("#maquinariaDescripcion").val(data.datos.maquinaria['descripcion']);
					$("#maquinariaSerie").val(data.datos.maquinaria['serie']);
				}
			}); // .then( data => {

		} // if ( campoMaquinariaId.value != '' )

	});

	$("#btnDescargarFacturas").on('click', function () {
		let gastoId = $('#gastoId').val();
		window.open(`${rutaAjax}app/Ajax/GastosAjax.php?accion=descargarFacturas&gastoId=${gastoId}`, '_blank');
	});

	function obtenerValoresXML(xmlString) {
		let parser = new DOMParser();
		let xmlDoc = parser.parseFromString(xmlString, "text/xml");
		let folio = "";
		let fecha = "";
		let proveedor = "";
		let factura = "";
		let costoTotal = 0;

		let comprobante = xmlDoc.getElementsByTagName("cfdi:Comprobante")[0];
		if (comprobante) {
			// Obtener el UUID del complemento TimbreFiscalDigital si existe
			let timbre = xmlDoc.getElementsByTagName("tfd:TimbreFiscalDigital")[0];
			if (timbre) {
				folio = timbre.getAttribute("UUID") || timbre.getAttribute("uuid") || "";
			} else {
					folio = comprobante.getAttribute("UUID") || comprobante.getAttribute("uuid") || "";
			}
			fecha = comprobante.getAttribute("Fecha") || comprobante.getAttribute("fecha") || "";
			factura = (comprobante.getAttribute("Serie") || comprobante.getAttribute("serie") || "") + " ";
			// Obtener el total directamente del comprobante
			let total = comprobante.getAttribute("Total") || comprobante.getAttribute("total") || "";
			costoTotal = parseFloat(total) || 0;
		}

		let emisor = xmlDoc.getElementsByTagName("cfdi:Emisor")[0];
		if (emisor) {
			proveedor = emisor.getAttribute("Nombre") || emisor.getAttribute("nombre") || "";
		}

		let conceptos = xmlDoc.getElementsByTagName("cfdi:Concepto");
		let items = [];
		for (let i = 0; i < conceptos.length; i++) {
			let concepto = conceptos[i];
			let cantidad = concepto.getAttribute("Cantidad") || concepto.getAttribute("cantidad") || "";
			let unidad = concepto.getAttribute("Unidad") || concepto.getAttribute("unidad") || "";
			let descripcion = concepto.getAttribute("Descripcion") || concepto.getAttribute("descripcion") || "";
			let valorUnitario = concepto.getAttribute("ValorUnitario") || concepto.getAttribute("valorUnitario") || "";
			let importe = concepto.getAttribute("Importe") || concepto.getAttribute("importe") || "";
			items.push({ cantidad, unidad, descripcion, valorUnitario, importe });
		}

		return { folio, fecha, proveedor, factura, items, costoTotal };
	}

	/*========================================
		Funciones para manejar los estatus de los gastos
	========================================*/

	$(document).on('click', '.btn-cambiar-estatus', function() {
		let nuevoEstatus = $(this).data('estatus');
		let gastoId = $('#gastoId').val();

		$.ajax({
			url: `${rutaAjax}app/Ajax/GastosAjax.php`,
			method: 'POST',
			data: {
				accion: 'cambiarEstatus',
				gastoId: gastoId,
				nuevoEstatus: nuevoEstatus
			},
			cache: false,
			contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
			dataType: 'json',
			success: function(data) {
				if (data.error) {
					Swal.fire({
						title: 'Error',
						text: data.errorMessage,
						icon: 'error',
						confirmButtonText: 'Cerrar'
					});
					return;
				}

				Swal.fire({
					title: 'Éxito',
					text: 'El gasto fue marcado como procesado correctamente.',
					icon: 'success',
					confirmButtonText: 'Cerrar'
				}).then(() => {
					location.reload();
				});
			}
		});
	});

});