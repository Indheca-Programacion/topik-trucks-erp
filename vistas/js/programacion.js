let tableList = document.getElementById('tablaProgramacion');
let parametrosTableList = { responsive: false };

let dataTableProgramacion = null;

let elementModalAgregarSeguimiento = document.querySelector('#modalAgregarSeguimiento');
let tableAgregarSeguimiento = document.getElementById('tablaAgregarSeguimiento');

let elementModalCrearServicio = document.querySelector('#modalCrearServicio');

// Realiza la petición para actualizar la programación
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
		// console.log(data)

		if ( data.error ) {
			$(document).Toasts('create', {
				class: 'bg-info',
				title: 'Información',
				subtitle: 'Info',
				body: data.respuestaMessage
			})

			return;
		}

		$("#btnAgregarSeguimiento").prop('disabled', false);
		$("#btnImprimir").prop('disabled', false);

		// Actualizar los catálogos en #modalAgregarSeguimiento
		let elementSelectMaquinaria = document.getElementById('modalAgregarSeguimiento_maquinariaId');
		data.catalogos.maquinarias.forEach( (item, index) => {
			let registro = elementSelectMaquinaria.querySelector(`option[value="${item.id}"]`);
			if ( registro === null ) {
				let newOption = `<option value="${item.id}">
									${item.numeroEconomico} [ ${item.serie} ]
								</option>`;

				$(elementSelectMaquinaria).append(newOption);
			}
		});

		// Actualizar los catálogos en #modalCrearServicio
		let elementSelectServicioCentro = document.getElementById('modalCrearServicio_servicioCentroId');
		data.catalogos.servicioCentros.forEach( (item, index) => {
			let registro = elementSelectServicioCentro.querySelector(`option[value="${item.id}"]`);
			if ( registro === null ) {
				let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;

				$(elementSelectServicioCentro).append(newOption);
			}
		});

		let elementSelectServicioEstatus = document.getElementById('modalCrearServicio_servicioEstatusId');
		data.catalogos.servicioStatus.forEach( (item, index) => {
			let registro = elementSelectServicioEstatus.querySelector(`option[value="${item.id}"]`);
			if ( registro === null ) {
				let selected = item.descripcion.toLowerCase().includes('activa') ? ' selected' : '';
				let newOption = `<option value="${item.id}"${selected}>
									${item.descripcion}
								</option>`;

				$(elementSelectServicioEstatus).append(newOption);
			}
		});

		let elementSelectSolicitudTipo = document.getElementById('modalCrearServicio_solicitudTipoId');
		data.catalogos.solicitudTipos.forEach( (item, index) => {
			let registro = elementSelectSolicitudTipo.querySelector(`option[value="${item.id}"]`);
			if ( registro === null ) {
				let selected = item.descripcion.toLowerCase().includes('programado') ? ' selected' : '';
				let newOption = `<option value="${item.id}"${selected}>
									${item.descripcion}
								</option>`;

				$(elementSelectSolicitudTipo).append(newOption);
			}
		});

		let elementSelectMantenimientoTipo = document.getElementById('modalCrearServicio_mantenimientoTipoId');
		data.catalogos.mantenimientoTipos.forEach((item, index) => {
			let registro = elementSelectMantenimientoTipo.querySelector(`option[value="${item.id}"]`);
			if (registro === null) {
				let selected = item.descripcion.toLowerCase().includes('preventivo') ? ' selected' : '';
				let newOption = `<option value="${item.id}"${selected}>
									${item.descripcion}
								</option>`;
				$(elementSelectMantenimientoTipo).append(newOption);
			}
		});

		let elementSelectUbicacion = document.getElementById('modalCrearServicio_ubicacion');
		data.catalogos.ubicaciones.forEach((item, index) => {
			let registro = elementSelectUbicacion.querySelector(`option[value="${item.id}"]`);
			if (registro === null) {
				let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;
				$(elementSelectUbicacion).append(newOption);
			}
		});

		let elementSelectObra = document.getElementById('modalCrearServicio_obra');
		data.catalogos.obras.forEach((item, index) => {
			let registro = elementSelectObra.querySelector(`option[value="${item.id}"]`);
			if (registro === null) {
				let newOption = `<option value="${item.id}">
									${item.descripcion}
								</option>`;
				$(elementSelectObra).append(newOption);
			}
		});

		let elementSelectEmpresa = document.getElementById('modalCrearServicio_empresa');
		data.catalogos.empresas.forEach( (item, index) => {
			let registro = elementSelectEmpresa.querySelector(`option[value="${item.id}"]`);
			if ( registro === null ) {
				let newOption = `<option value="${item.id}">
									${item.nombreCorto}
								</option>`;
				$(elementSelectEmpresa).append(newOption);
			}
		});

		let arrayColumnsTextRight = [];

		// Agregar las columnas de los Servicios (Encabezado)
		data.datos.encabezado.forEach( (item, index) => {
			$("#tablaProgramacion thead tr[encabezado-1]").append(`<th scope="col" rowspan="1" colspan="4" class="text-center bg-info" style="min-width: 112px;">${item.data}</th>`);
			// arrayColumnsTextRight.push(i + 1);
			// arrayColumnsOrderable.push(i + 1);
			let numColumna = (index + 1) * 4;
			$("#tablaProgramacion thead tr[encabezado-2]").append(`<th scope="col" rowspan="1" class="text-centers bg-info" style="min-width: 80px;">Serv Ant</th>`);
			arrayColumnsTextRight.push(numColumna);
			$("#tablaProgramacion thead tr[encabezado-2]").append(`<th scope="col" rowspan="1" class="text-centers bg-info" style="min-width: 80px;">Prox Serv</th>`);
			arrayColumnsTextRight.push(numColumna + 1);
			$("#tablaProgramacion thead tr[encabezado-2]").append(`<th scope="col" rowspan="1" class="text-centers bg-info" style="min-width: 80px;">Hrs Act</th>`);
			arrayColumnsTextRight.push(numColumna + 2);
			$("#tablaProgramacion thead tr[encabezado-2]").append(`<th scope="col" rowspan="1" class="text-centers bg-info" style="min-width: 80px;">Hrs Pend</th>`);
			arrayColumnsTextRight.push(numColumna + 3);
		});

		tableList.classList.remove("d-none");

		dataTableProgramacion = $(idTabla).DataTable({

			autoWidth: false,
			responsive: ( parametros.responsive === undefined ) ? true : parametros.responsive,
			info: false,
			paging: false,
			searching: false,
			// pageLength: 25,
			scrollX: true,
			data: data.datos.registros,
			columns: data.datos.columnas,

			columnDefs: [
				// { targets: [0], visible: false, searchable: false },
				// { targets: [1], className: 'col-fixed-left' },
				{ targets: arrayColumnsTextRight, className: 'text-right' }
				// { targets: arrayColumnsTextCenter, className: 'text-center' },
				// { targets: arrayColumnsOrderable, orderable: false }
			],

	        createdRow: function (row, data, index) {
	        	// row.setAttribute('equipo', data.equipo);
	        	data.warningActuales.forEach( (item, index2) => {
	        		$('td', row).eq(item).addClass("text-danger");
	        	});
	        	data.warningPendientes.forEach( (item, index2) => {
	        		// $('td', row).eq(item).css("background-color", "yellow");
	        		$('td', row).eq(item).addClass("bg-warning");
	        	});
	        },

			buttons: [
				// { extend: 'copy', text:'Copiar', className: 'btn-info' },
				// { extend: 'csv', className: 'btn-info' },
				{ extend: 'excel', className: 'btn-info' },
				// { extend: 'pdf', className: 'btn-info' },
				// { extend: 'print', text:'Imprimir', className: 'btn-info' },
				// { extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
			],

			language: LENGUAJE_DT,
			aaSorting: [],

		// }).buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)'); // $(idTabla).DataTable({
		}); // $(idTabla).DataTable({
		dataTableProgramacion.buttons().container().appendTo(idTabla+'_wrapper .row:eq(0)');
	}); // .then( data => {

} // function fActualizarListado( rutaAjax, idTabla, parametros = {} ) {

// LLamar a la funcion fAjaxDataTable() para llenar el Listado

$('#collapseFiltros').on('show.bs.collapse', function (event) {
	let btnVerFiltros = document.getElementById('btnVerFiltros');
	btnVerFiltros.querySelector('i').classList.remove("fa-eye");
	btnVerFiltros.querySelector('i').classList.add("fa-eye-slash");
})

$('#collapseFiltros').on('hide.bs.collapse', function (event) {
	let btnVerFiltros = document.getElementById('btnVerFiltros');
	btnVerFiltros.querySelector('i').classList.remove("fa-eye-slash");
	btnVerFiltros.querySelector('i').classList.add("fa-eye");
})

// Limpia la tabla y crea el header
function cleanTableProgramacion() {
	$(tableList).DataTable().destroy();
	dataTableProgramacion = null;
	$("#tablaProgramacion thead tr[encabezado-1]").html('');
	$("#tablaProgramacion thead tr[encabezado-1]").append('<th scope="col" colspan="4" class="text-center bg-info">Especificaciones</th>');
	$("#tablaProgramacion thead tr[encabezado-2]").html('');
	$("#tablaProgramacion thead tr[encabezado-2]").append('<th scope="col">Equipo</th>');
	$("#tablaProgramacion thead tr[encabezado-2]").append('<th scope="col">Empresa</th>');
	$("#tablaProgramacion thead tr[encabezado-2]").append('<th scope="col" style="min-width: 160px;">Ubicación</th>');
	$("#tablaProgramacion thead tr[encabezado-2]").append('<th scope="col">Estado</th>');
	tableList.querySelector('tbody').innerHTML = '';
}

$('#btnFiltrar').on('click', function (e) {

	$("#btnAgregarSeguimiento").prop('disabled', true);
	$("#btnImprimir").prop('disabled', true);

	tableList.classList.add("d-none");

	cleanTableProgramacion();

	let empresaId = $('#filtroEmpresaId').val();
	let obraId = $('#filtroObraId').val();

	fActualizarListado(`${rutaAjax}app/Ajax/ProgramacionAjax.php?empresaId=${empresaId}&obraId=${obraId}`, '#tablaProgramacion', parametrosTableList);
});

// Activar el elemento Select2
$('.select2').select2({
	tags: false,
	width: '100%'
	// theme: 'bootstrap4'
});
$('.select2Add').select2({
	tags: true
	// ,theme: 'bootstrap4'
});
$(".select2ModalAgregarSeguimiento").select2({
	dropdownParent: $('#modalAgregarSeguimiento'),
	language: 'es',
	tags: false,
	width: '100%'
	// theme: 'bootstrap4'
});
$(".select2ModalCrearServicio").select2({
	dropdownParent: $('#modalCrearServicio'),
	language: 'es',
	tags: false,
	width: '100%'
	// theme: 'bootstrap4'
});
//Date picker
$('.input-group.date').datetimepicker({
    format: 'DD/MMMM/YYYY'
});

let minDate = moment().subtract(12, 'months')
let maxDate = moment().add(1, 'hours');

$('#modalCrearServicio_fechaSolicitudDTP').datetimepicker('minDate', minDate);
$('#modalCrearServicio_fechaSolicitudDTP').datetimepicker('maxDate', maxDate);

minDate = $('#modalCrearServicio_fechaSolicitudDTP').datetimepicker('viewDate');

$('#modalCrearServicio_fechaProgramacionDTP').datetimepicker('minDate', minDate);

// Selecionar Maquinaria (Número Económico)
$('#modalAgregarSeguimiento select#modalAgregarSeguimiento_maquinariaId').on('change', function (e) {

	let elementErrorValidacion = elementModalAgregarSeguimiento.querySelector('.error-validacion');
	elementErrorValidacion.querySelector('ul').innerHTML = '';
	$(elementErrorValidacion).addClass("d-none");

	tableAgregarSeguimiento.classList.add("d-none");
	tableAgregarSeguimiento.querySelector('tbody').innerHTML = '';
	$("#modalAgregarSeguimiento button.btnGuardar").prop('disabled', true);

	let maquinariaId = this.value;
	if ( maquinariaId == '' ) return;

	fetch( `${rutaAjax}app/Ajax/ProgramacionAjax.php?maquinariaId=${maquinariaId}`, {
		method: 'GET', // *GET, POST, PUT, DELETE, etc.
		cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
		headers: {
			'Content-Type': 'application/json'
		}
	} )
	.then( response => response.json() )
	.catch( error => console.log('Error:', error) )
	.then( data => {

		if ( data.error ) {
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(data.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			elementErrorValidacion.querySelector('ul').appendChild(elementList);

			$(elementErrorValidacion).removeClass("d-none");

			return;
		}

		// console.log(data)
		let elementTbody = tableAgregarSeguimiento.querySelector('tbody');
		data.datos.registros.forEach( (item, index) => {
			let newTr = `<tr>
							<td data-servicio-tipo-id='${item.servicioTipoId}'>${item.servicioTipo}</td>
							<td>${item.ultimo}</td>
							<td>${item.siguiente}</td>
						</tr>`;

			$(elementTbody).append(newTr);
		});

		tableAgregarSeguimiento.classList.remove("d-none");
		$("#modalAgregarSeguimiento button.btnGuardar").prop('disabled', false);

	}); // .then( data => {

});

// Check al Tipo de Servicio
$(tableAgregarSeguimiento).on("click", "input[name='servicioTipoId[]']", function (event) {

	let elementInputConsecutivo = this.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector("input[name='consecutivo[]']");
	let elementInputServicioAnterior = this.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector("input[name='horoOdometroUltimo[]']");
	let elementInputSiguienteServicio = this.parentElement.parentElement.parentElement.parentElement.parentElement.querySelector("input[name='cantidadSiguienteServicio[]']");

	if ( this.checked ) {
		$(elementInputConsecutivo).prop('disabled', false);
		$(elementInputServicioAnterior).prop('disabled', false);
		$(elementInputSiguienteServicio).prop('disabled', false);
	} else {
		$(elementInputConsecutivo).prop('disabled', true);
		$(elementInputServicioAnterior).prop('disabled', true);
		$(elementInputSiguienteServicio).prop('disabled', true);
	}

});

// Guardar Seguimiento
$('#modalAgregarSeguimiento button.btnGuardar').on('click', function (e) {

	let elementErrorValidacion = elementModalAgregarSeguimiento.querySelector('.error-validacion');
	elementErrorValidacion.querySelector('ul').innerHTML = '';
	$(elementErrorValidacion).addClass("d-none");

	let maquinariaId = $('#modalAgregarSeguimiento select#modalAgregarSeguimiento_maquinariaId').val();
	if ( maquinariaId == '' ) return;

	let btnGuardar = this;

	// Petición Ajax para guardar el insumo
	let token = $('input[name="_token"]').val();

	let datos = new FormData(document.getElementById("formSeguimientoSend"));
	datos.append("_token", token);
	datos.append("accion", "agregarSeguimiento");
	datos.append("maquinariaId", maquinariaId);

	$.ajax({
		url: rutaAjax+'app/Ajax/ProgramacionAjax.php',
		method: 'POST',
		data: datos,
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
					let newContent = document.createTextNode(`Registro [${respuesta.consecutivo}]: ${item}`);
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

		// console.log(respuesta)
		$(elementModalAgregarSeguimiento).modal('hide');
		crearToast('bg-success', 'Actualizar Programación', 'OK', respuesta.respuestaMessage);

		$("#modalAgregarSeguimiento_maquinariaId").val("");
		$('#modalAgregarSeguimiento_maquinariaId').trigger('change.select2'); // Notify only Select2 of changes
		$('#modalAgregarSeguimiento_maquinariaId').change();

		document.getElementById("btnFiltrar").click();

	})
	.fail(function(error) {
		// console.log("*** Error ***");
		// console.log(error);
		// console.log(error.responseText);
		// console.log(error.responseJSON);
		// console.log(error.responseJSON.message);

		let elementList = document.createElement('li'); // prepare a new li DOM element
		// let newContent = document.createTextNode(error.errorMessage);
		let newContent = document.createTextNode("Ocurrió un error al intentar actualizar la programación, de favor actualice o vuelva a cargar la página e intente de nuevo");
		elementList.appendChild(newContent); //añade texto al div creado.
		// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar actualizar la programación, de favor actualice o vuelva a cargar la página e intente de nuevo");
		elementErrorValidacion.querySelector('ul').appendChild(elementList);
		$(elementErrorValidacion).removeClass("d-none");

		$(btnGuardar).prop('disabled', false);
	})
	.always(function() {
		// stopLoading();
		// $(btnGuardar).prop('disabled', false);
	});

});

// Rellenar los campos al abrir el modal #modalCrearServicio 
$(elementModalCrearServicio).on('show.bs.modal', function (event) {
	let servicioTipoId = event.relatedTarget.getAttribute('servicioTipoId');
	let servicioTipo = event.relatedTarget.getAttribute('servicioTipo');

	let rowElement = event.relatedTarget.parentElement.parentElement;
	// let dataT = dataTableProgramacion.rows().data();
	let dataRow = dataTableProgramacion.row(rowElement).data();

	$('#modalCrearServicio_empresaId').val(dataRow.empresaId);
	$('#modalCrearServicio_empresa').val(dataRow.empresa);

	$('#modalCrearServicio_servicioTipoId').val(servicioTipoId);
	$('#modalCrearServicio_servicioTipo').val(servicioTipo);

	$('#modalCrearServicio_maquinariaId').val(dataRow.maquinariaId);
	$('#modalCrearServicio_numeroEconomico').val(dataRow.equipo);

	$('#modalCrearServicio_ubicacion').val(dataRow.ubicacionId).trigger('change');

	$('#modalCrearServicio_obra').val(dataRow.obraId).trigger('change');

	$.ajax({
		url: rutaAjax+`app/Ajax/KitMantenimientoAjax.php?maquinariaId=${dataRow.maquinariaId}`,
		method: 'GET',
		dataType: 'json',
	})
	.done(function(respuesta) {
		if ( respuesta.error ) {
			// console.log(respuesta)
			crearToast('bg-info', 'Información', 'Info', respuesta.respuestaMessage);
			return;
		}
		// console.log(respuesta)
		if ( respuesta.datos.registros.length > 0 ) {
			let elementSelectKitMantenimiento = document.getElementById('modalCrearServicio_kitMantenimiento');
			respuesta.datos.registros.forEach( (item, index) => {
				let registro = elementSelectKitMantenimiento.querySelector(`option[value="${item.id}"]`);
				if ( registro === null ) {
					let newOption = `<option value="${item.id}">
										${item.tipoMantenimiento}
									</option>`;

					$(elementSelectKitMantenimiento).append(newOption);
				}
			});
		}
	})


	$("#modalCrearServicio button.btnGuardar").prop('disabled', false);
})

// Guardar Seguimiento
$('#modalCrearServicio button.btnGuardar').on('click', function (e) {

	let elementErrorValidacion = elementModalCrearServicio.querySelector('.error-validacion');
	elementErrorValidacion.querySelector('ul').innerHTML = '';
	$(elementErrorValidacion).addClass("d-none");

	let btnGuardar = this;

	// Petición Ajax para crear el servicio
	let token = $('input[name="_token"]').val();

	let datos = new FormData(document.getElementById("formCrearServicioSend"));
	datos.append("_token", token);
	datos.append("accion", "crearServicio");

	$.ajax({
		url: rutaAjax+'app/Ajax/ProgramacionAjax.php',
		method: 'POST',
		data: datos,
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

		// console.log(respuesta)
		$(elementModalCrearServicio).modal('hide');
		crearToast('bg-success', 'Crear Servicio', 'OK', respuesta.respuestaMessage);

		$("#modalCrearServicio_servicioCentroId").val("");
		$('#modalCrearServicio_servicioCentroId').trigger('change.select2'); // Notify only Select2 of changes
		$('#modalCrearServicio_servicioCentroId').change();

		$("#modalCrearServicio_empresa").val("");
		$('#modalCrearServicio_empresa').trigger('change.select2'); // Notify only Select2 of changes
		$('#modalCrearServicio_empresa').change();

		$("#modalCrearServicio_horasProyectadas").val("");

		$("#modalCrearServicio_fechaProgramacion").val("");

		$("#modalCrearServicio_descripcion").val("");

		document.getElementById("btnFiltrar").click();

	})
	.fail(function(error) {
		// console.log("*** Error ***");
		// console.log(error);
		// console.log(error.responseText);
		// console.log(error.responseJSON);
		// console.log(error.responseJSON.message);

		let elementList = document.createElement('li'); // prepare a new li DOM element
		// let newContent = document.createTextNode(error.errorMessage);
		let newContent = document.createTextNode("Ocurrió un error al intentar crear el servicio, de favor actualice o vuelva a cargar la página e intente de nuevo");
		elementList.appendChild(newContent); //añade texto al div creado.
		// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar crear el servicio, de favor actualice o vuelva a cargar la página e intente de nuevo");
		elementErrorValidacion.querySelector('ul').appendChild(elementList);
		$(elementErrorValidacion).removeClass("d-none");

		$(btnGuardar).prop('disabled', false);
	})
	.always(function() {
		// stopLoading();
		// $(btnGuardar).prop('disabled', false);
	});

});

//
$('#btnImprimir').on('click',function(){
	let btnGuardar = this;

	let empresaId = $("#filtroEmpresaId").val();
	let obraId = $("#filtroObraId").val();
	let token = $('input[name="_token"]').val();

	let datos = new FormData();
	datos.append("accion","imprimir");
	datos.append("_token",token);
	datos.append("empresaId",empresaId);
	datos.append("obraId",obraId);
	$.ajax({
		url: rutaAjax+`app/Ajax/ProgramacionAjax.php`,
		method: 'POST',
		data: datos,
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

		// console.log(respuesta)
		crearToast('bg-success', 'OK', respuesta.respuestaMessage);

		const link = document.createElement('a');
		link.href = rutaAjax+'app/Ajax/tmp/ProgramacionMantenimiento.pdf';
		link.download = 'ReporteMantenimiento.pdf';
		link.click();

	})
	.fail(function(error) {
		// console.log("*** Error ***");
		// console.log(error);
		// console.log(error.responseText);
		// console.log(error.responseJSON);
		// console.log(error.responseJSON.message);

		let elementList = document.createElement('li'); // prepare a new li DOM element
		// let newContent = document.createTextNode(error.errorMessage);
		let newContent = document.createTextNode("Ocurrió un error al intentar crear el servicio, de favor actualice o vuelva a cargar la página e intente de nuevo");
		elementList.appendChild(newContent); //añade texto al div creado.
		// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar crear el servicio, de favor actualice o vuelva a cargar la página e intente de nuevo");
		// elementErrorValidacion.querySelector('ul').appendChild(elementList);
		// $(elementErrorValidacion).removeClass("d-none");

		$(btnGuardar).prop('disabled', false);
	})
	.always(function() {
		// stopLoading();
		$(btnGuardar).prop('disabled', false);
	});
});

$('#modalSeleccionarMaquinarias').on('show.bs.modal', function (event) {
	
	fActualizarListadoMaquinarias();

});

$('#modalSeleccionarMaquinarias').on('hidden.bs.modal', function (event) {
	// Destruir la tabla al cerrar el modal
	$('#tablaMaquinariasReporte').DataTable().destroy();
	$('#tablaMaquinariasReporte').find('tbody').empty();
	// $('#btnGenerarReporteMaquinarias').prop('disabled', true);
});

$('#modalSeleccionarMaquinarias').on('change', 'input.row-checkbox', function () {
	try {
		let table = $('#tablaMaquinariasReporte').DataTable();
		// Buscar checkboxes en todos los nodos (todas las páginas)
		let anyChecked = $(table.rows().nodes()).find('input.row-checkbox:checked').length > 0;
		$('#btnGenerarReporteMaquinarias').prop('disabled', !anyChecked);
	} catch (e) {
		// Si la tabla no está inicializada, fallback a búsqueda en DOM actual
		let anyChecked = $('#modalSeleccionarObra').find('input.row-checkbox:checked').length > 0;
		$('#btnGenerarReporteMaquinarias').prop('disabled', !anyChecked);
	}
});

$('#btnGenerarReporteMaquinarias').on('click', function () {
	let btnGenerarReporteMaquinarias = this;
	let token = $('input[name="_token"]').val();

	let tabla = $('#tablaMaquinariasReporte').DataTable();
	let maquinarias = [];
	tabla.rows().every(function (rowIdx, tableLoop, rowLoop) {
		let rowNode = this.node();
		let checkbox = $(rowNode).find('input.row-checkbox');
		if (checkbox.is(':checked')) {
			let data = this.data();
			maquinarias.push(data.id);
		}
	});

	let datos = new FormData();
	datos.append("accion", "imprimir");
	datos.append("_token", token);
	datos.append("empresaId", $('#filtroEmpresaReporteMaquinarias').val());
	datos.append("obraId", $('#filtroObraReporteMaquinarias').val());
	datos.append("maquinarias", JSON.stringify(maquinarias));
	$.ajax({
		url: rutaAjax + `app/Ajax/ProgramacionAjax.php`,
		method: 'POST',
		data: datos,
		cache: false,
		contentType: false,
		processData: false,
		dataType: 'json',
		beforeSend: () => {
			$(btnGenerarReporteMaquinarias).prop('disabled', true);
			// loading();
		}
	})
		.done(function (respuesta) {
			// Manejar la respuesta del servidor
			if (respuesta.error) {
				crearToast('bg-danger', 'Error', 'Error', respuesta.mensaje);
			} else {
				crearToast('bg-success', 'Éxito', 'Éxito', 'Reporte generado correctamente');
				const link = document.createElement('a');
				link.href = rutaAjax+'app/Ajax/tmp/ProgramacionMantenimiento.pdf';
				link.download = 'ReporteMantenimiento.pdf';
				link.click();

				const linkservicio = document.createElement('a');
				linkservicio.href = rutaAjax+'app/Ajax/tmp/ProgramacionServicios.pdf';
				linkservicio.download = 'ProgramacionServicios.pdf';
				linkservicio.click();
			}
		})
		.always(function () {
			$(btnGenerarReporteMaquinarias).prop('disabled', false);
		});
});

$('#filtroEmpresaReporteMaquinarias, #filtroObraReporteMaquinarias').on('change', function () {
	fActualizarListadoMaquinarias();

});

function fActualizarListadoMaquinarias() {
	let empresaId = $('#filtroEmpresaReporteMaquinarias').val();
	let obraId = $('#filtroObraReporteMaquinarias').val();
	$.ajax({
		url: rutaAjax + 'app/Ajax/MaquinariaAjax.php?empresaId=' + empresaId + '&obraId=' + obraId + '&ubicacionId=0&maquinariaTipoId=0',
		method: 'GET',
		dataType: 'json',
		
	}).
	done(function (data) {
		if (data.error) {
			crearToast('bg-info', 'Información', 'Info', data.respuestaMessage);
			return;
		}

		$('#tablaMaquinariasReporte').DataTable().destroy();
		$('#tablaMaquinariasReporte').find('tbody').empty();
		// $('#btnGenerarReporteMaquinarias').prop('disabled', true);

		$('#tablaMaquinariasReporte').DataTable({

			autoWidth: false,
			responsive: true,
			data: data.datos.registros,
			columns: data.datos.columnas.splice(0, 12),
			columnDefs: [
				{
					targets: 0, // Columna donde agregamos el checkbox
					orderable: false,
					className: 'dt-body-center',
					render: function (data, type, row) {
						return '<input type="checkbox" class="row-checkbox" value="' + (row.id || '') + '">';
					}
				},
			],
			// mantener select pero manejaremos checks manualmente
			select: {
				style: 'multi',
				selector: 'td:first-child',
			},
			language: LENGUAJE_DT,
			aaSorting: [],
			initComplete: function () {
				var api = this.api();

				// Insertar checkbox "select all" en el header (primera columna)
				var $firstTh = $(api.table().header()).find('th').eq(0);
				$firstTh.html('<input type="checkbox" id="select-all-maquinarias">');

				// Manejar click en el header checkbox para seleccionar/deseleccionar todo
				$(api.table().header()).on('change', '#select-all-maquinarias', function () {
					var checked = this.checked;
					$(api.rows().nodes()).find('input.row-checkbox').prop('checked', checked).trigger('change');
					$('#btnGenerarReporteMaquinarias').prop('disabled', !checked);
				});

				// Delegar cambio en checkboxes de fila para actualizar estado del header y el botón
				$(api.table().body()).on('change', 'input.row-checkbox', function () {
					var all = $(api.rows().nodes()).find('input.row-checkbox').length;
					var checked = $(api.rows().nodes()).find('input.row-checkbox:checked').length;

					$('#btnGenerarReporteMaquinarias').prop('disabled', checked === 0);

					var selectAll = $(api.table().header()).find('#select-all-maquinarias').get(0);
					if (selectAll) {
						if (checked === 0) {
							selectAll.checked = false;
							selectAll.indeterminate = false;
						} else if (checked === all) {
							selectAll.checked = true;
							selectAll.indeterminate = false;
						} else {
							selectAll.checked = false;
							selectAll.indeterminate = true;
						}
					}
				});

				// Al dibujar la tabla, sincronizar estado del header checkbox si ya hay checks (por ejemplo al recargar)
				api.on('draw', function () {
					var all = $(api.rows().nodes()).find('input.row-checkbox').length;
					var checked = $(api.rows().nodes()).find('input.row-checkbox:checked').length;
					var selectAll = $(api.table().header()).find('#select-all-maquinarias').get(0);
					if (selectAll) {
						if (checked === 0) {
							selectAll.checked = false;
							selectAll.indeterminate = false;
						} else if (checked === all) {
							selectAll.checked = true;
							selectAll.indeterminate = false;
						} else {
							selectAll.checked = false;
							selectAll.indeterminate = true;
						}
					}
					$('#btnGenerarReporteMaquinarias').prop('disabled', checked === 0);
				});
			}
		})

	}).fail(function (error) {
		console.log("*** Error ***");
		console.log(error);
		console.log(error.responseText);
		console.log(error.responseJSON);
		console.log(error.responseJSON.message);
	});
}