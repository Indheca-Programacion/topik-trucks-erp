if (document.getElementById('demo-upload') !== null) Dropzone.autoDiscover = false;

$(function(){

	let tableList = document.getElementById('tablaTraslados');
	let tableGastos = document.getElementById('tblGastos');
	let parametrosTableList = { responsive: true };
	let dataTableGasto = null;
	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/TrasladoAjax.php', '#tablaTraslados',parametrosTableList);
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
		        	if ( data.estatus == 'Por Atender' ) {
						// $('td', row).eq(3).css("color", '');
						$('td', row).eq(3).css("background-color", '#f0ab51');
					} else {
						// $('td', row).eq(3).css("color", '#fff');
						$('td', row).eq(3).css("background-color", '#0dcaf0');
					}
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
	if ( tableGastos != null ) obtenerDetalles();

	// Confirmar la eliminación del Ubicacion
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Ubicación (Descripción: '+folio+') ?',
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

	// Select2
	$('.select2').select2({
		theme: 'bootstrap4',
		width: '100%'
	});
	$('#fecha').datetimepicker({
		timepicker: false,
		format: 'DD/MMMM/YYYY'
	});

	/* =================================== */
	/* =========== Eventos Gastos =========== */
	/* =================================== */

	// Tipo de gasto

	$('#gasto').on('change', function(){
		let gastos = $(this).val();
		
		if (gastos == 1) {
			$('#section-deducible').removeClass('d-none');
			$('#section-no-deducible').addClass('d-none');
		} else {
			$('#section-deducible').addClass('d-none');
			$('#section-no-deducible').removeClass('d-none');
		}
		
	});

	// Agregar Gasto

	$('#btnAddGasto').on('click', function(){
		let traslado = $('#traslado').val();
		let gastoId = $('#gasto').val();
		let proveedor = $('#proveedor').val();
		let total = $('#total').val();
		let folio = $('#folio').val();
		let descripcion = $('#descripcion').val();

		if (gastoId == 0 || total == '' || descripcion == '') {
			crearToast('bg-danger', 'Error', '', 'Todos los campos son obligatorios');
			return;
		}

		if ( gastoId == 1) {
			
			if (arrPDF.length == 0) {
				crearToast('bg-danger', 'Error', '', 'Es necesario subir el archivo PDF');
				return;
			}

			if (arrXML.length == 0) {
				crearToast('bg-danger', 'Error', '', 'Es necesario subir el archivo XML');
				return;
			}
		}

		let datos = new FormData();
		datos.append('_token', $('input[name=_token]').val());
		datos.append('traslado', traslado);
		datos.append('gasto', gastoId);
		datos.append('proveedor', proveedor);
		datos.append('folio', folio);
		datos.append('total', total);
		datos.append('descripcion', descripcion);
		datos.append('accion', 'addGasto');

		arrPDF.forEach(file => {
			datos.append('archivos[]', file);
		});

		arrXML.forEach(file => {
			datos.append('archivos[]', file);
		});

		arrSoporte.forEach(file => {
			datos.append('archivos[]', file);
		});

		let btn = $(this);
		btn.prop('disabled', true);
		$.ajax({
			url: rutaAjax+'app/Ajax/TrasladoAjax.php',
			type: 'POST',
			dataType: 'json',
			data: datos,
			processData: false,
			contentType: false,
		}).done(function(data){
			if (!data.error) {
				crearToast('bg-success', 'Éxito', '', 'Gasto agregado correctamente');
				$('#total').val('');
				$('#folio').val('');
				$('#descripcion').val('');
				$('#proveedor').val('');
				arrPDF = [];
				arrXML = [];
				arrSoporte = [];
				myDropzone.removeAllFiles(true);
				myDropzoneXML.removeAllFiles(true);
				myDropzoneSoporte.removeAllFiles(true);
				dataTableGasto.destroy();
				obtenerDetalles();
			} else {
				crearToast('bg-danger', 'Error', '', 'Error al agregar el gasto');
			}
		}).fail(function(){
			crearToast('bg-danger', 'Error', '', 'Error al agregar el gasto');
		}).always(function(){
			btn.prop('disabled', false);
		});
	});

	// Eliminar Gasto
	$(tableGastos).on('click', 'button.eliminar', function(){
		let folio  = $(this).attr('folio');
		Swal.fire({
			title: '¿Estás Seguro de querer eliminar este detalle (Descripción: '+folio+') ?',
			text: "No podrá recuperar esta información!",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#d33',
			cancelButtonColor: '#3085d6',
			confirmButtonText: 'Sí, quiero eliminarla!',
			cancelButtonText:  'No!'
	    }).then((result) => {
			if (result.isConfirmed) {
				let id = $(this).attr('id');
				let datos = new FormData();
				datos.append('_token', $('input[name=_token]').val());
				datos.append('id', id);
				datos.append('accion', 'deleteGasto');
		
				$.ajax({
					url: rutaAjax+'app/Ajax/TrasladoAjax.php',
					type: 'POST',
					dataType: 'json',
					data: datos,
					processData: false,
					contentType: false,
				}).done(function(data){
					if (!data.error) {
						crearToast('bg-success', 'Éxito', '', 'Gasto eliminado correctamente');
						dataTableGasto.destroy();
						obtenerDetalles();
					} else {
						crearToast('bg-danger', 'Error', '', 'Error al eliminar el gasto');
					}
				}).fail(function(){
					crearToast('bg-danger', 'Error', '', 'Error al eliminar el gasto');
				});
			}
	    })

	});

	// Ver Archivos
	$(tableGastos).on('click', 'button.btn-mostrar-modal', function(){
		let id = $(this).attr('folio');
		let datos = new FormData();
		$("#modalVerArchivosLabel span").html(id);
		$("#modalVerArchivos div#accordionArchivos").html('');
		datos.append('_token', $('input[name=_token]').val());
		datos.append('id', id);
		datos.append('accion', 'getArchivos');

		$.ajax({
			url: rutaAjax+'app/Ajax/TrasladoAjax.php',
			type: 'POST',
			dataType: 'json',
			data: datos,
			processData: false,
			contentType: false,
		}).done(function(data){
			if (!data.error) {

				data.datos.forEach( (archivo, index) => {
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
		}).fail(function(){
			crearToast('bg-danger', 'Error', '', 'Error al obtener los archivos');
		});
	});

	// Obtener detalles
	function obtenerDetalles(){
		let traslado = $('#traslado').val();
		let datos = new FormData();
		datos.append('_token', $('input[name=_token]').val());
		datos.append('traslado', traslado);
		datos.append('accion', 'getDetalles');

		$.ajax({
			url: rutaAjax+'app/Ajax/TrasladoAjax.php',
			type: 'POST',
			dataType: 'json',
			data: datos,
			processData: false,
			contentType: false,
		}).done(function(data){
			if (!data.error) {
				dataTableGasto = $(tableGastos).DataTable({
					autoWidth: false,
					info: false,
					paging: false,
					searching: false,
					responsive:  true ,
					data: data.datos.registros,
					columns: data.datos.columnas,
					destroy: true
				});
				
			}
		}).fail(function(){
			crearToast('bg-danger', 'Error', '', 'Error al obtener los detalles');
		});
	}

	/* =================================== */
	/* =========== Descargar Todo =========== */
	/* =================================== */

	$('#btnDescargarTodo').on('click', function(){
		let folio = $('#folio').val();
		let traslado = $('#traslado').val();
		let datos = new FormData();
		datos.append('_token', $('input[name=_token]').val());
		datos.append('traslado', traslado);
		datos.append('accion', 'descargarTodo');

		$.ajax({
			url: rutaAjax+'app/Ajax/TrasladoAjax.php',
			type: 'POST',
			data: datos,
			processData: false,
			contentType: false,xhrFields: {
				responseType: 'blob' // Importante: Especificar el tipo de respuesta como 'blob'
			},
			success: function(data) {
				// Crear un enlace invisible para la descarga
				var link = document.createElement('a');
				link.href = window.URL.createObjectURL(data);
				link.download = folio; // Nombre del archivo para la descarga
				link.style.display = 'none';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);
			},
			error: function(error) {
				console.error('Error en la petición AJAX:', error);
			}
		});
	});

	// ===================== Dropzone =====================

	let arrPDF = [];
	let arrXML = [];
	let arrSoporte = [];
	let myDropzone = null;
	let myDropzoneXML = null;
	let myDropzoneSoporte = null;


	if (document.getElementById('demo-upload') !== null) {

		myDropzone = new Dropzone('#demo-upload', {
			url: rutaAjax,
			acceptedFiles: "application/pdf",
			addRemoveLinks: true,
			dictRemoveFile:'Eliminar Archivo',
			dictUpload: "Subiendo",
			dictCancelUpload: "Cancelar",
			dictInvalidFileType: "No se permite este tipo de archivo",
			maxFiles: 1,
			maxfilesexceeded: function(file) {
				crearToast('bg-danger', 'Error','', 'Solo se permite subir un archivo');
				this.removeFile(file); // Eliminar el archivo que excede el límite
			},
			maxFilesize: 4
		});

		myDropzoneXML = new Dropzone('#xml-upload', {
			url: rutaAjax,
			acceptedFiles: "text/xml",
			addRemoveLinks: true,
			dictRemoveFile:'Eliminar Archivo',
			dictUpload: "Subiendo",
			dictCancelUpload: "Cancelar",
  			dictFileTooBig: "El archivo es demasiado grande ({{filesize}} {{maxFilesize}})",
  			dictInvalidFileType: "No se permite este tipo de archivo",
			maxFiles: 1,
			maxfilesexceeded: function(file) {
				crearToast('bg-danger', 'Error','', 'Solo se permite subir un archivo');
				this.removeFile(file); // Eliminar el archivo que excede el límite
			},
			maxFilesize: 4
		});

		myDropzoneSoporte = new Dropzone('#soporte-upload', {
			url: rutaAjax,
			acceptedFiles: "application/pdf",
			addRemoveLinks: true,
			dictRemoveFile:'Eliminar Archivo',
			dictUpload: "Subiendo",
			dictCancelUpload: "Cancelar",
  			dictFileTooBig: "El archivo es demasiado grande ({{filesize}} {{maxFilesize}})",
  			dictInvalidFileType: "No se permite este tipo de archivo",
			maxFiles: 1,
			maxfilesexceeded: function(file) {
				crearToast('bg-danger', 'Error','', 'Solo se permite subir un archivo');
				this.removeFile(file); // Eliminar el archivo que excede el límite
			},
			maxFilesize: 4
		});
	
		myDropzone.on('addedfile', file => {
			arrPDF.push(file);
		})

		myDropzone.on('removedfile', file => {
			let i = arrPDF.indexOf(file);
			arrPDF.splice(i, 1);
		})

		myDropzoneSoporte.on('addedfile', file => {
			arrSoporte.push(file);
		})

		myDropzoneXML.on('addedfile', file => {
			arrXML.push(file);
		})		
		
		myDropzoneXML.on('removedfile', file => {
			let i = arrXML.indexOf(file);
			arrXML.splice(i, 1);
		})

		myDropzoneSoporte.on('removedfile', file => {
			let i = arrSoporte.indexOf(file);
			arrSoporte.splice(i, 1);
		})
		
	}

	function fDataTable(idTabla){

		$(idTabla).DataTable({

	      // "autoWidth": false,
	      // "lengthChange": false,
	      // "responsive": false,
	      responsive: false,
	      paging: false,
		  searching: false,
	      // data: data.datos.registros,
	      // columns: data.datos.columnas,

	      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"],

	      "language": {

	        "sProcessing":     "Procesando...",
	        "sLengthMenu":     "Mostrar _MENU_ registros",
	        "sZeroRecords":    "No se encontraron resultados",
	        "sEmptyTable":     "Ningún dato disponible en esta tabla",
	        "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_",
	        "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0",
	        "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
	        "sInfoPostFix":    "",
	        "sSearch":         "Buscar:",
	        "sUrl":            "",
	        "sInfoThousands":  ",",
	        "sLoadingRecords": "Cargando...",
	        "oPaginate": {
	        "sFirst":    "Primero",
	        "sLast":     "Último",
	        "sNext":     "Siguiente",
	        "sPrevious": "Anterior"
	        },
	        "oAria": {
	          "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
	          "sSortDescending": ": Activar para ordenar la columna de manera descendente"
	        }

	      },
	      
	      'aaSorting': [],

	    // }).buttons().container().appendTo(idTabla+'_wrapper .col-md-6:eq(0)'); // $(idTabla).DataTable({
	    }).buttons().container().appendTo('#requisiciones .card-tools'); // $(idTabla).DataTable({

	    $(idTabla).parent().addClass( "table-responsive" );

  		let colButtons = document.querySelector(idTabla+'_wrapper').firstChild.firstChild;
  		$( colButtons ).removeClass('col-md-6');

	}

});