$(function(){

	let tableList = document.getElementById('tablaGeneradores');
	let dataTableMaquinarias = null;
	let dataTableResumen = null;
	let dataTablObservaciones = null;	
	
	let tableGenerador = document.getElementById('tablaMaquinarias');
	let tableResumen = document.getElementById('tablaResumen');
	let tableObservaciones = document.getElementById('tablaIncidencias');
	let elementmodalAñadirEquipo = document.querySelector('#modalAñadirEquipo');
	let elementmodalAgregarIncidencia = document.querySelector('#modalAgregarIncidencia');
	let elementmodalAgregarObservacion = document.querySelector('#modalAgregarObservacion');
	let dataTableSeleccionarMaquina = $('#tablaSeleccionarMaquina').DataTable();
	let parametrosTableList = { responsive: false };
	// LLamar a la funcion fAjaxDataTable() para llenar el Listado  
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/GeneradorAjax.php', '#tablaGeneradores');
	let firstDayOfMonth
	let lastDayOfMonth
	let rangoMes = obtenerRangoFechas( new Date($('#mes').val()+ '-01T00:00:00'));
	// Confirmar la eliminación de observacion
	$(tableObservaciones).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Observacion (Descripción: '+folio+') ?',
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
	// Confirmar la eliminación de generador detalles
	$(tableGenerador).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar esta Observacion (Descripción: '+folio+') ?',
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

	if (tableGenerador != null) {
		// Obtiene los datos de los detalles de generador
		fActualizarGeneradores();
	}

	function fActualizarGeneradores(){
		let mes = $('#mes').val();
		let dias = obtenerDiasEnMes(mes)
		let selectedYear = parseInt(mes.split('-')[0]);
		let selectedMonth = parseInt(mes.split('-')[1]);

		firstDayOfMonth = new Date(selectedYear, selectedMonth - 1, 1);
		lastDayOfMonth = new Date(selectedYear, selectedMonth, 0);
		fecha = mes;
		let generadorId = $('#generadorId').val();
		fetch( `${rutaAjax}app/Ajax/GeneradorAjax.php?generador=${generadorId}`, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {
			let selectElement = $('#modalAgregarObservacion_numero');
			$.each(data.maquinaria.registros, function(index, obj) {
				selectElement.append($('<option>').val(obj.generadorId).text(obj.numeroEconomico));
			});
			dataTableMaquinarias = $(tableGenerador).DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				// pageLength: 25,
				info: false,
				// ordering: false,
				paging: false,
				pageLength: 100,
				lengthMenu: [
					[10, 15, 25, 50, 100],
					[10, 15, 25, 50, 100]
				],
				searching: false,
				// scrollX: true,
				data: data.maquinaria.registros,
				columns: data.maquinaria.columnas,
				createdRow: (row, data, index) => {	
					for (let index = 1; index < dias+1; index++) {
						addCell(row, '');
						if (data.fallas.includes(index)) {
							$('td:eq(' + (index+5) + ')', row).css("background-color", "#FF0000");;
						}
						if (data.laborados.includes(index)) {
							$('td:eq(' + (index+5) + ')', row).css("background-color", "#00913f");;
						}
						if (data.paros.includes(index)) {
							$('td:eq(' + (index+5) + ')', row).css("background-color", "#FFD300");;
						}
						if (data.clima.includes(index)) {
							$('td:eq(' + (index+5) + ')', row).css("background-color", "#572364 ");;
						}
						if (data.diaParcial.includes(index)) {
							$('td:eq(' + (index+5) + ')', row).css("background-color", "#FF8000 ");;
						}
					}
				},
				columnDefs: [
					{ targets: [0], visible: false, searchable: false },
					// { targets: [1], className: 'col-fixed-left dt-control' },
					// { targets: arrayColumnsTextRight, className: 'text-right' },
					// // { targets: arrayColumnsTextCenter, className: 'text-center' },
					{ targets: [0,1,2,3,4,5,6], orderable: false },
					{ targets: 1, render:
						function (data, type, row) {
							return `${data} ${row.acciones}`
					}
					}
				],

	
				buttons: [{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' }
					// { extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
	
				language: LENGUAJE_DT,
				aaSorting: [],
	
			});

			dataTableResumen = $(tableResumen).DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				paging: false,
				info: false,
				pageLength: 100,
				lengthMenu: [
					[10, 15, 25, 50, 100],
					[10, 15, 25, 50, 100]
				],
				searching: false,
				// scrollX: true,
				data: data.resumen.registros,
				columns: data.resumen.columnas,
				columnDefs: [
					//{ targets: [0], visible: false, searchable: false },
					// { targets: [1], className: 'col-fixed-left dt-control' },
					// { targets: arrayColumnsTextRight, className: 'text-right' },
					// { targets: arrayColumnsTextCenter, className: 'text-center' },
					{ targets: [0,1,2,3,4,5,6], orderable: false },
				],
	
				language: LENGUAJE_DT,
				aaSorting: [],
			});

			dataTablObservaciones = $(tableObservaciones).DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				// pageLength: 25,
				info: false,
				// ordering: false,
				paging: false,
				pageLength: 100,
				lengthMenu: [
					[10, 15, 25, 50, 100],
					[10, 15, 25, 50, 100]
				],
				searching: false,
				data: data.observaciones.registros,
				columns: data.observaciones.columnas,
				columnDefs: [
					//{ targets: [0], visible: false, searchable: false },
					// { targets: [1], className: 'col-fixed-left dt-control' },
					// { targets: arrayColumnsTextRight, className: 'text-right' },
					// // { targets: arrayColumnsTextCenter, className: 'text-center' },
					{ targets: [0,1,2,3], orderable: false },
				],
				language: LENGUAJE_DT,
				aaSorting: [],
			});
		})	
	}

    function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		formulario.submit();
	}

	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Estatus (Descripción: '+folio+') ?',
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

	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

    $('.select2').select2({
		tags: false,
		width: '100%'
    })
	$(".select2modalAñadirEquipo").select2({
		dropdownParent: $('#modalAñadirEquipo'),
		language: 'es',
		tags: false,
		width: '100%'
		// theme: 'bootstrap4'
	});
	$(".input-group.date").datetimepicker({
		timepicker: false,
        format: 'DD/MMMM/YYYY',
		minDate: rangoMes.primerDia,
        maxDate: rangoMes.ultimoDia
	});
	$(".input-group.date2").datetimepicker({
		timepicker: false,
        format: 'DD/MMMM/YYYY',
		minDate: rangoMes.primerDia,
        maxDate: rangoMes.ultimoDia
	});
	$('#start_date').datetimepicker({
		timepicker: false,
        format: 'DD/MMMM/YYYY',
		minDate: rangoMes.primerDia,
        maxDate: rangoMes.ultimoDia
	});
	$('#end_date').datetimepicker({
		timepicker: false,
        format: 'DD/MMMM/YYYY',
		minDate: rangoMes.primerDia,
        maxDate: rangoMes.ultimoDia
	});

	// Agregar Observaciones
	$('#modalAgregarIncidencia_incidencia').on('change',function(e){
		if (parseFloat(this.value) < 2) {
			$('#modalAgregarIncidencia_observacion').addClass("d-none")
		}else{
			$('#modalAgregarIncidencia_observacion').removeClass("d-none")
		}
	});
	// Buscar insumo
	$('#modalAñadirEquipo button#btnBuscarNumero').on('click', function (e) {

		let elementErrorValidacion = elementmodalAñadirEquipo.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");

		let tableList = document.getElementById('tablaSeleccionarMaquina');
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';

		let generadorId = document.getElementById("generadorId");

		fetch( `${rutaAjax}app/Ajax/GeneradorAjax.php?generadorId=${generadorId.value}`, {
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
			
			dataTableSeleccionarMaquina = $(tableList).DataTable({

				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				// info: false,
				// paging: false,
				// searching: false,
				data: data.datos.registros,
				columns: data.datos.columnas,

				columnDefs: [
					// { targets: [0], visible: false, searchable: false },
					// { targets: [1], className: 'col-fixed-left' },
					// { targets: arrayColumnsTextRight, className: 'text-right' },
					// { targets: arrayColumnsTextCenter, className: 'text-center' },
					// { targets: arrayColumnsOrderable, orderable: false }
				],

				createdRow: (row, data, index) => {
					row.classList.add('seleccionable');
				},

				language: LENGUAJE_DT,
				aaSorting: [],

			}); 

			$('#modalBuscarMaquina').modal('show');

		}); // .then( data => {

	});
	// Selecciona uno de las maquinarias
	dataTableSeleccionarMaquina.on('click', 'tbody tr.seleccionable', function () {
		let data = dataTableSeleccionarMaquina.row(this).data();
		const numeroId = document.getElementById("modalAñadirEquipo_numeroId")
		const equipo = document.getElementById("modalAñadirEquipo_Equipo")
		const marca = document.getElementById("modalAñadirEquipo_Marca")
		const serie = document.getElementById("modalAñadirEquipo_Serie")
		const maquinaria = document.getElementById("modalAñadirEquipo_maquinariaId")

		numeroId.value = data.numeroEconomico
		marca.value = data.marca
		equipo.value = data.tipoMaquinaria
		serie.value = data.serie
		maquinaria.value = data.maquinariaId

		$('#modalBuscarMaquina').modal('hide');
	});
	// Añade equipo en el generador
	$('#modalAñadirEquipo button.btnAgregar').on('click',function (e){
		let elementErrorValidacion = elementmodalAñadirEquipo.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");
		let generadorId = $('#generadorId').val()
		let token = $('#token').val()
		let ubicacionId = $('#ubicacionId').val()
		let obraId = $('#obraId').val()
		let btnAgregar = this;
		let datos = new FormData(document.getElementById("formAñadirEquipo"));
		datos.append("fk_generador",generadorId)
		datos.append("ubicacionId",ubicacionId)
		datos.append("obraId",obraId)
		datos.append("_token",token)
		datos.append("accion", "agregar");
		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
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
				
				return;

			}

			$(elementmodalAñadirEquipo).modal('hide');
			crearToast('bg-success', 'Añadir Equipo', 'OK', respuesta.respuestaMessage);

			location.reload();

		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
			// console.log(error);
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
	});

	// Añade incidencias
	$('#modalAgregarIncidencia button.btnAgregarIncidencia').on('click',function(e){
		let elementFechaInicio = document.getElementById('modalAgregarIncidencia_fechaInicio')
		let elementFechaFin =  document.getElementById('modalAgregarIncidencia_fechaFin')
		let elementIncidencia =  document.getElementById('modalAgregarIncidencia_incidencia')
		let elementMaquinaria =  document.getElementById('modalAgregarIncidencia_numero')
		let elementObservacion =  document.getElementById('modalAgregarIncidencia_observacion_input')
		let elementObservacionDiv =  document.getElementById('modalAgregarIncidencia_observacion')

		let fechaInicio = $('#modalAgregarIncidencia_fechaInicio').val()
		let fechaFin = $('#modalAgregarIncidencia_fechaFin').val()
		let valorincidencia = $('#modalAgregarIncidencia_incidencia').val()
		let maquinariaId = $('#modalAgregarIncidencia_numero').val()

		let elementErrorValidacion = elementmodalAgregarIncidencia.querySelector('.error-validacion')
		let btnAgregar = $(this)

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

		elementFechaInicio.classList.remove("is-invalid");
		elementPadre = elementFechaInicio.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementFechaFin.classList.remove("is-invalid");
		elementPadre = elementFechaFin.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementIncidencia.classList.remove("is-invalid");
		elementPadre = elementIncidencia.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementMaquinaria.classList.remove("is-invalid");
		elementPadre = elementMaquinaria.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);
		
		elementObservacion.classList.remove("is-invalid");
		elementPadre = elementObservacion.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;

		if ( fechaInicio == '' ) {
			elementFechaInicio.classList.add("is-invalid");
			elementPadre = elementFechaInicio.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una fecha.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( fechaFin == '' ) {
			elementFechaFin.classList.add("is-invalid");
			elementPadre = elementFechaFin.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una fecha.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( valorincidencia == '0' ) {
			elementIncidencia.classList.add("is-invalid");
			elementPadre = elementIncidencia.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una incidencia.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( maquinariaId.length === 0) {
			elementMaquinaria.classList.add("is-invalid");
			elementPadre = elementMaquinaria.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una maquinaria.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( !elementObservacionDiv.classList.contains('d-none') ){
			if ( elementObservacion.value.length == 0) {
				elementObservacion.classList.add("is-invalid");
				elementPadre = elementObservacion.parentElement;
				newDiv = document.createElement("div");
				newDiv.classList.add("invalid-feedback");
				  // newContent = document.createTextNode("La cantidad es obligatoria.");
				newContent = document.createTextNode("Debe ingresar una observacion.");
				newDiv.appendChild(newContent); //añade texto al div creado.
				elementPadre.appendChild(newDiv);
		
				errores = true;
			}
		}
		if ( errores ) return;

		let startDate = fFecha(fechaInicio);
		let endDate = fFecha(fechaFin);

		let arrayDias = getDaysArray(startDate, endDate)
		let tabla = $('#tablaMaquinarias').DataTable();

		let datos = tabla.rows().data().toArray();

		let datosMaquinas = [];
		maquinariaId.forEach(element => {
			const maquina = datos.find(maquina => maquina.maquinariaId == element);
			// Busca por dias que esten añadido dentro de las incidencias 
			arrayDias.forEach(element => {

				if (maquina.fallas.includes(element)) {
					let index = maquina.fallas.indexOf(element);
					if (index > -1) {
						maquina.fallas.splice(index, 1);
					}
				}
				if (maquina.clima.includes(element)) {
					let index = maquina.clima.indexOf(element);
					if (index > -1) {
						maquina.clima.splice(index, 1);
					}
				}
				if (maquina.laborados.includes(element)) {
					let index = maquina.laborados.indexOf(element);
					if (index > -1) {
						maquina.laborados.splice(index, 1);
					}
				}
				if (maquina.paros.includes(element)) {
					let index = maquina.paros.indexOf(element);
					if (index > -1) {
						maquina.paros.splice(index, 1);
					}
				}
				if (maquina.diaParcial.includes(element)) {
					let index = maquina.diaParcial.indexOf(element);
					if (index > -1) {
						maquina.diaParcial.splice(index, 1);
					}
				}
			});

			let incidencia = ''
			// Se Obtiene el valor de la incidencia para buscarla en el array asociativo
			switch (valorincidencia) {
				case "1":
					incidencia = 'laborados'
					break;
				case "2":
					incidencia = 'fallas'
				break;
				case "3":
					incidencia = 'paros'
				break;
				case "4":
					incidencia = 'diaParcial'
				break;
				case "7":
					incidencia = 'clima'
				break;
				default:
					break;
			}
	
			if(incidencia != '') {
				maquina[incidencia]= maquina[incidencia].concat(arrayDias)
			}

			// TODO: HACER UN PUSH PERSONALIZADO PARA FACILITAR LA INSERCION DE LOS DATOS
			datosMaquinas.push(
					{
						detalleId : maquina.generadorId,
						clima : maquina.clima,
						fallas : maquina.fallas,
						laborados : maquina.laborados,
						paros : maquina.paros,
						fallas : maquina.fallas,
						diaParcial : maquina.diaParcial,
					}
				)
		});

		let datosPost = new FormData()
		let token = $('#token').val()
		datosPost.append("accion","updateIncidencia")
		datosPost.append("datos",JSON.stringify(datosMaquinas))
		if(elementObservacion.value.length > 0){
			datosPost.append("observaciones",elementObservacion.value)
			datosPost.append("desde",elementFechaInicio.value)
			datosPost.append("hasta",elementFechaFin.value)
		} 
		datosPost.append("_token",token)

		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datosPost,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
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
				
				return;

			}

			crearToast('bg-success', 'Añadir Incidencia', 'OK', respuesta.respuestaMessage);
			$('#modalAgregarIncidencia').modal('hide');
			tabla.destroy();
			// fActualizarGeneradores();

			location.reload();

		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
			// console.log(error);
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
		
	});
	
	// Añade Observaciones
	$('#modalAgregarObservacion button.btnAgregarObservacion').on('click',function(e){
		let elementFechaInicio = document.getElementById('modalAgregarObservacion_fecha_inicio')
		let elementFechaFin =  document.getElementById('modalAgregarObservacion_fecha_fin')
		let elementNumero =  document.getElementById('modalAgregarObservacion_numero')
		let elementObservacion =  document.getElementById('modalAgregarObservacion_observacion')

		let fechaInicio = $('#modalAgregarObservacion_fecha_inicio').val()
		let fechaFin = $('#modalAgregarObservacion_fecha_fin').val()
		let generadorId = $('#modalAgregarObservacion_numero').val()
		let observacion = $('#modalAgregarObservacion_observacion').val()

		let elementErrorValidacion = elementmodalAgregarObservacion.querySelector('.error-validacion')
		let btnAgregar = $(this)

		let elementPadre = null;
		let newDiv = null;
		let newContent = null;

		elementFechaInicio.classList.remove("is-invalid");
		elementPadre = elementFechaInicio.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementFechaFin.classList.remove("is-invalid");
		elementPadre = elementFechaFin.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementNumero.classList.remove("is-invalid");
		elementPadre = elementNumero.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		elementObservacion.classList.remove("is-invalid");
		elementPadre = elementObservacion.parentElement;
		newDiv = elementPadre.querySelector('div.invalid-feedback');
		if ( newDiv != null ) elementPadre.removeChild(newDiv);

		let errores = false;

		if ( fechaInicio == '' ) {
			elementFechaInicio.classList.add("is-invalid");
			elementPadre = elementFechaInicio.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una fecha.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( fechaFin == '' ) {
			elementFechaFin.classList.add("is-invalid");
			elementPadre = elementFechaFin.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una fecha.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( generadorId == '' ) {
			elementNumero.classList.add("is-invalid");
			elementPadre = elementNumero.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe seleccionar una maquinaria.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		}
		if ( observacion == '') {
			elementObservacion.classList.add("is-invalid");
			elementPadre = elementObservacion.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("Debe escribir una observacion.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
	
			errores = true;
		} else if ( observacion.length > 100 ) {
			elementObservacion.classList.add("is-invalid");
			elementPadre = elementObservacion.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
			  // newContent = document.createTextNode("La cantidad es obligatoria.");
			newContent = document.createTextNode("La observacion supera los 100 caracteres.");
			newDiv.appendChild(newContent); //añade texto al div creado.
			elementPadre.appendChild(newDiv);
		}
		if ( errores ) return;

		let datosPost = new FormData(document.getElementById('formSendObservacion'))
		let token = $('#token').val()
		datosPost.append("accion","agregarObservacion")
		datosPost.append("_token",token)
		
		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datosPost,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
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
				
				return;

			}

			crearToast('bg-success', 'Agregar Observacion', 'OK', respuesta.respuestaMessage);
			$('#modalAgregarObservacion').modal('hide');

			location.reload();

		})
		.fail(function(error) {
			// console.log("*** Error ***");
			// console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
			// console.log(error);
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
		
	})
	// Añade Firmar autorizado
	$('#btnAutorizarEstimacion').on('click',function(e){

		let btnAgregar = this;

		let token = $('#token').val()
		let datos = new FormData();
		let generadorId = $('#generadorId').val()

		datos.append("generadorId",generadorId)
		datos.append("_token",token)
		datos.append("accion", "autorizarEstimacion");
		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
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
				
				return;

			}

			crearToast('bg-success', 'Añadir Equipo', 'OK', respuesta.respuestaMessage);

			location.reload();

		})
		.fail(function(error) {
			crearToast('bg-danger', 'Añadir Equipo', 'OK', error.respuestaMessage);
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
	})
	// Añade Firmar autorizado
	$('#btnFirmar').on('click',function(e){

		let btnAgregar = this;

		let token = $('#token').val()
		let datos = new FormData();
		let generadorId = $('#generadorId').val()

		datos.append("generadorId",generadorId)
		datos.append("_token",token)
		datos.append("accion", "firmar");
		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {

				swal.fire({
					title: 'Error',
					text: respuesta.errorMessage,
					icon: 'error',
					confirmButtonText: 'OK'
				});

			}else{
				swal.fire({
					title: 'Generador firmado',
					text: respuesta.respuestaMessage,
					icon: 'success',
					confirmButtonText: 'OK'
				}).then(() => {
					location.reload();
				});
			}

			crearToast('bg-success', 'Añadir Equipo', 'OK', respuesta.respuestaMessage);

			// location.reload();

		})
		.fail(function(error) {
			crearToast('bg-danger', 'Añadir Equipo', 'OK', error.respuestaMessage);
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
	})
	// Añade Firmar autorizado a la estimacion de supervisor
	$('#btnAutorizarEstimacionesSupervisor').on('click',function(e){

		let btnAgregar = this;
		let datos = new FormData();
		let generadorId = $('#generadorId').val()
		let token = $('#token').val()

		datos.append("generadorId",generadorId)
		datos.append("_token",token)
		datos.append("accion", "firmarSupervisorEstimacion");
		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {
			if (respuesta.error) {
				
				crearToast('bg-danger', 'Autorizar Estimación', 'OK', respuesta.errorMessage);

				return;
			}

			crearToast('bg-success', 'Autorizar Estimación', 'OK', respuesta.respuestaMessage);

			location.reload();	
		})
		.fail(function(error) {
			crearToast('bg-danger', 'Autorizar Estimación', 'OK', error.respuestaMessage);

		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
	})

	// Añade Firmar autorizado al generador de supervisor
	$('#btnAutorizarSupervisor').on('click',function(e){
		
		let btnAgregar = this;
		let datos = new FormData();
		let generadorId = $('#generadorId').val()
		let token = $('#token').val()

		datos.append("generadorId",generadorId)
		datos.append("_token",token)
		datos.append("accion", "firmarSupervisor");
		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: datos,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
			beforeSend: () => {
				$(btnAgregar).prop('disabled', true);
				// loading();
			}
		})
		.done(function(respuesta) {
			if (respuesta.error) {
				
				crearToast('bg-danger', 'Autorizar Estimación', 'OK', respuesta.errorMessage);

				return;
			}

			crearToast('bg-success', 'Autorizar Estimación', 'OK', respuesta.respuestaMessage);

			location.reload();
		})
		.fail(function(error) {
			crearToast('bg-danger', 'Autorizar Estimación', 'OK', error.respuestaMessage);

		})
		.always(function() {
			// stopLoading();
			$(btnAgregar).prop('disabled', false);
		});
	})

	//ESTIMACIONES

	$("#tablaEstimaciones").on("blur", "input[name='detalles[costo][]']", function (e) {    

		const dateString = $('#mes').val();
		const ultimoDia = moment(dateString+'-01').endOf('month');
		const diasMes = parseInt(ultimoDia.format('DD'));

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let costo = $(this).val();
		costo = costo.replace(/,/g, "");
		costo = parseFloat(costo);
	
		let dias = $(this).parent().parent().find("span.dias");
		dias = parseFloat(dias.text());

		let pu = $(this).parent().parent().find("span.pu");
		let valorPu = (costo/30)*dias;  
		pu.html(number_format( valorPu, 2 ));

		let operacion = $(this).parent().parent().find("input[name='detalles[operacion][]']").val();
		operacion = operacion.replace(/,/g, "");
		operacion = parseFloat(operacion);

		let comb = $(this).parent().parent().find("input[name='detalles[comb][]']").val();
		comb = comb.replace(/,/g, "");
		comb = parseFloat(comb);

		let mantto = $(this).parent().parent().find("input[name='detalles[mantto][]']").val();
		mantto = mantto.replace(/,/g, "");
		mantto = parseFloat(mantto);

		let flete = $(this).parent().parent().find("input[name='detalles[flete][]']").val();
		flete = flete.replace(/,/g, "");
		flete = parseFloat(flete);

		let ajuste = $(this).parent().parent().find("input[name='detalles[ajuste][]']").val();
		ajuste = ajuste.replace(/,/g, "");
		ajuste = parseFloat(ajuste);

		const importeValor = valorPu + operacion + comb + mantto +  flete + ajuste

		let importe = $(this).parent().parent().find("span.importe");
		importe.html(number_format(importeValor,2));

		let datos = {
			"generador_datelle" : detalleId,
			"costo" : costo,
			"pu" : valorPu,
			"operacion" : operacion,
			"comb" : comb,
			"mantto" : mantto,
			"flete" : flete,
			"ajuste" : ajuste
		}

		actualizarDatos(datos);
	
	});

	$("#tablaEstimaciones").on("blur", "input[name='detalles[operacion][]']", function (e) {    

		const dateString = $('#mes').val();
		const ultimoDia = moment(dateString+'-01').endOf('month');
		const diasMes = parseInt(ultimoDia.format('DD'));

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let costo = $(this).parent().parent().find("input[name='detalles[costo][]']").val();
		costo = costo.replace(/,/g, "");
		costo = parseFloat(costo);
	
		let dias = $(this).parent().parent().find("span.dias");
		dias = parseFloat(dias.text());

		let pu = $(this).parent().parent().find("span.pu");
		let valorPu = (costo/30)*dias;  
		pu.html(number_format( valorPu, 2 ));

		let operacion = $(this).val();
		operacion = operacion.replace(/,/g, "");
		operacion = parseFloat(operacion);

		let comb = $(this).parent().parent().find("input[name='detalles[comb][]']").val();
		comb = comb.replace(/,/g, "");
		comb = parseFloat(comb);

		let mantto = $(this).parent().parent().find("input[name='detalles[mantto][]']").val();
		mantto = mantto.replace(/,/g, "");
		mantto = parseFloat(mantto);

		let flete = $(this).parent().parent().find("input[name='detalles[flete][]']").val();
		flete = flete.replace(/,/g, "");
		flete = parseFloat(flete);

		let ajuste = $(this).parent().parent().find("input[name='detalles[ajuste][]']").val();
		ajuste = ajuste.replace(/,/g, "");
		ajuste = parseFloat(ajuste);

		const importeValor = valorPu + operacion + comb + mantto +  flete + ajuste

		let importe = $(this).parent().parent().find("span.importe");
		importe.html(number_format(importeValor,2));

		let datos = {
			"generador_datelle" : detalleId,
			"costo" : costo,
			"pu" : valorPu,
			"operacion" : operacion,
			"comb" : comb,
			"mantto" : mantto,
			"flete" : flete,
			"ajuste" : ajuste
		}

		actualizarDatos(datos);
	
	});

	$("#tablaEstimaciones").on("blur", "input[name='detalles[comb][]']", function (e) {    

		const dateString = $('#mes').val();
		const ultimoDia = moment(dateString+'-01').endOf('month');
		const diasMes = parseInt(ultimoDia.format('DD'));

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let costo = $(this).parent().parent().find("input[name='detalles[costo][]']").val();
		costo = costo.replace(/,/g, "");
		costo = parseFloat(costo);
	
		let dias = $(this).parent().parent().find("span.dias");
		dias = parseFloat(dias.text());

		let pu = $(this).parent().parent().find("span.pu");
		let valorPu = (costo/30)*dias;  
		pu.html(number_format( valorPu, 2 ));

		let operacion = $(this).parent().parent().find("input[name='detalles[operacion][]']").val();
		operacion = operacion.replace(/,/g, "");
		operacion = parseFloat(operacion);

		let comb = $(this).val();
		comb = comb.replace(/,/g, "");
		comb = parseFloat(comb);

		let mantto = $(this).parent().parent().find("input[name='detalles[mantto][]']").val();
		mantto = mantto.replace(/,/g, "");
		mantto = parseFloat(mantto);

		let flete = $(this).parent().parent().find("input[name='detalles[flete][]']").val();
		flete = flete.replace(/,/g, "");
		flete = parseFloat(flete);

		let ajuste = $(this).parent().parent().find("input[name='detalles[ajuste][]']").val();
		ajuste = ajuste.replace(/,/g, "");
		ajuste = parseFloat(ajuste);

		const importeValor = valorPu + operacion + comb + mantto +  flete + ajuste

		let importe = $(this).parent().parent().find("span.importe");
		importe.html(number_format(importeValor,2));

		let datos = {
			"generador_datelle" : detalleId,
			"costo" : costo,
			"pu" : valorPu,
			"operacion" : operacion,
			"comb" : comb,
			"mantto" : mantto,
			"flete" : flete,
			"ajuste" : ajuste
		}

		actualizarDatos(datos);
	
	});

	$("#tablaEstimaciones").on("blur", "input[name='detalles[mantto][]']", function (e) {    

		const dateString = $('#mes').val();
		const ultimoDia = moment(dateString+'-01').endOf('month');
		const diasMes = parseInt(ultimoDia.format('DD'));

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let costo = $(this).parent().parent().find("input[name='detalles[costo][]']").val();
		costo = costo.replace(/,/g, "");
		costo = parseFloat(costo);
	
		let dias = $(this).parent().parent().find("span.dias");
		dias = parseFloat(dias.text());

		let pu = $(this).parent().parent().find("span.pu");
		let valorPu = (costo/30)*dias;  
		pu.html(number_format( valorPu, 2 ));

		let operacion = $(this).parent().parent().find("input[name='detalles[operacion][]']").val();
		operacion = operacion.replace(/,/g, "");
		operacion = parseFloat(operacion);

		let comb = $(this).parent().parent().find("input[name='detalles[comb][]']").val();
		comb = comb.replace(/,/g, "");
		comb = parseFloat(comb);

		let mantto = $(this).val();
		mantto = mantto.replace(/,/g, "");
		mantto = parseFloat(mantto);

		let flete = $(this).parent().parent().find("input[name='detalles[flete][]']").val();
		flete = flete.replace(/,/g, "");
		flete = parseFloat(flete);

		let ajuste = $(this).parent().parent().find("input[name='detalles[ajuste][]']").val();
		ajuste = ajuste.replace(/,/g, "");
		ajuste = parseFloat(ajuste);

		const importeValor = valorPu + operacion + comb + mantto +  flete + ajuste

		let importe = $(this).parent().parent().find("span.importe");
		importe.html(number_format(importeValor,2));

		let datos = {
			"generador_datelle" : detalleId,
			"costo" : costo,
			"pu" : valorPu,
			"operacion" : operacion,
			"comb" : comb,
			"mantto" : mantto,
			"flete" : flete,
			"ajuste" : ajuste
		}

		actualizarDatos(datos);
	
	});

	$("#tablaEstimaciones").on("blur", "input[name='detalles[flete][]']", function (e) {    

		const dateString = $('#mes').val();
		const ultimoDia = moment(dateString+'-01').endOf('month');
		const diasMes = parseInt(ultimoDia.format('DD'));

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let costo = $(this).parent().parent().find("input[name='detalles[costo][]']").val();
		costo = costo.replace(/,/g, "");
		costo = parseFloat(costo);
	
		let dias = $(this).parent().parent().find("span.dias");
		dias = parseFloat(dias.text());

		let pu = $(this).parent().parent().find("span.pu");
		let valorPu = (costo/30)*dias;  
		pu.html(number_format( valorPu, 2 ));

		let operacion = $(this).parent().parent().find("input[name='detalles[operacion][]']").val();
		operacion = operacion.replace(/,/g, "");
		operacion = parseFloat(operacion);

		let comb = $(this).parent().parent().find("input[name='detalles[comb][]']").val();
		comb = comb.replace(/,/g, "");
		comb = parseFloat(comb);

		let mantto = $(this).parent().parent().find("input[name='detalles[mantto][]']").val();
		mantto = mantto.replace(/,/g, "");
		mantto = parseFloat(mantto);

		let flete = $(this).val();
		flete = flete.replace(/,/g, "");
		flete = parseFloat(flete);

		let ajuste = $(this).parent().parent().find("input[name='detalles[ajuste][]']").val();
		ajuste = ajuste.replace(/,/g, "");
		ajuste = parseFloat(ajuste);

		const importeValor = valorPu + operacion + comb + mantto +  flete + ajuste

		let importe = $(this).parent().parent().find("span.importe");
		importe.html(number_format(importeValor,2));

		let datos = {
			"generador_datelle" : detalleId,
			"costo" : costo,
			"pu" : valorPu,
			"operacion" : operacion,
			"comb" : comb,
			"mantto" : mantto,
			"flete" : flete,
			"ajuste" : ajuste
		}

		actualizarDatos(datos);
	
	});

	$("#tablaEstimaciones").on("blur", "input[name='detalles[ajuste][]']", function (e) {    

		const dateString = $('#mes').val();
		const ultimoDia = moment(dateString+'-01').endOf('month');
		const diasMes = parseInt(ultimoDia.format('DD'));

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let costo = $(this).parent().parent().find("input[name='detalles[costo][]']").val();
		costo = costo.replace(/,/g, "");
		costo = parseFloat(costo);
	
		let dias = $(this).parent().parent().find("span.dias");
		dias = parseFloat(dias.text());

		let pu = $(this).parent().parent().find("span.pu");
		let valorPu = (costo/30)*dias;  
		pu.html(number_format( valorPu, 2 ));

		let operacion = $(this).parent().parent().find("input[name='detalles[operacion][]']").val();
		operacion = operacion.replace(/,/g, "");
		operacion = parseFloat(operacion);

		let comb = $(this).parent().parent().find("input[name='detalles[comb][]']").val();
		comb = comb.replace(/,/g, "");
		comb = parseFloat(comb);

		let mantto = $(this).parent().parent().find("input[name='detalles[mantto][]']").val();
		mantto = mantto.replace(/,/g, "");
		mantto = parseFloat(mantto);

		let flete = $(this).parent().parent().find("input[name='detalles[flete][]']").val();
		flete = flete.replace(/,/g, "");
		flete = parseFloat(flete);

		let ajuste = $(this).val();
		ajuste = ajuste.replace(/,/g, "");
		ajuste = parseFloat(ajuste);

		const importeValor = valorPu + operacion + comb + mantto +  flete + ajuste

		let importe = $(this).parent().parent().find("span.importe");
		importe.html(number_format(importeValor,2));

		let datos = {
			"generador_datelle" : detalleId,
			"costo" : costo,
			"pu" : valorPu,
			"operacion" : operacion,
			"comb" : comb,
			"mantto" : mantto,
			"flete" : flete,
			"ajuste" : ajuste
		}

		actualizarDatos(datos);
	
	});

	//DESEMPENO

	$("#tablaDesempeno").on("blur", "input[name='detalles[hmr][]']", function (e) {    

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let hmr = $(this).val();
		hmr = hmr.replace(/,/g, "");
		hmr = parseFloat(hmr);
	
		let hod = $(this).parent().parent().find("span.hod");
		hod = parseFloat(hod.text());

		let rr = $(this).parent().parent().find("input[name='detalles[rr][]']").val();
		rr = rr.replace(/,/g, "");
		rr = parseFloat(rr);

		let lcc = $(this).parent().parent().find("input[name='detalles[lcc][]']").val();
		lcc = lcc.replace(/,/g, "");
		lcc = parseFloat(lcc);

		let observaciones = $(this).parent().parent().find("input[name='detalles[observaciones][]']").val();

		const rendimiento = hod != 0 ? (lcc/hod)*100 : 0;

		const aprovechamiento = hod != 0 ? ((hmr-rr)/hod)*100 : 0;

		let rendimientohtml = $(this).parent().parent().find("span.rendimiento");
		rendimientohtml.html(number_format(rendimiento,2)+' %');
		
		let aprovechamientohtml = $(this).parent().parent().find("span.aprovechamiento");
		aprovechamientohtml.html(number_format(aprovechamiento,2)+' %');

		let datos = {
			"generador_datelle" : detalleId,
			"hmr" : hmr,
			"rr" : rr,
			"lcc" : lcc,
			"observacion" : observaciones
		}

		actualizarDesempeno(datos);
	
	});

	$("#tablaDesempeno").on("blur", "input[name='detalles[rr][]']", function (e) {    

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let rr = $(this).val();
		rr = rr.replace(/,/g, "");
		rr = parseFloat(rr);
	
		let hod = $(this).parent().parent().find("span.hod");
		hod = parseFloat(hod.text());

		let hmr = $(this).parent().parent().find("input[name='detalles[hmr][]']").val();
		hmr = rr.replace(/,/g, "");
		hmr = parseFloat(hmr);

		let lcc = $(this).parent().parent().find("input[name='detalles[lcc][]']").val();
		lcc = lcc.replace(/,/g, "");
		lcc = parseFloat(lcc);

		let observaciones = $(this).parent().parent().find("input[name='detalles[observaciones][]']").val();

		const rendimiento = hod != 0 ? (lcc/hod)*100 : 0;

		const aprovechamiento = hod != 0 ? ((hmr-rr)/hod)*100 : 0;

		let rendimientohtml = $(this).parent().parent().find("span.rendimiento");
		rendimientohtml.html(number_format(rendimiento,2)+' %');
		
		let aprovechamientohtml = $(this).parent().parent().find("span.aprovechamiento");
		aprovechamientohtml.html(number_format(aprovechamiento,2)+' %');

		let datos = {
			"generador_datelle" : detalleId,
			"hmr" : hmr,
			"rr" : rr,
			"lcc" : lcc,
			"observacion" : observaciones
		}

		actualizarDesempeno(datos);
	
	});

	$("#tablaDesempeno").on("blur", "input[name='detalles[lcc][]']", function (e) {    

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let lcc = $(this).val();
		lcc = lcc.replace(/,/g, "");
		lcc = parseFloat(lcc);
	
		let hod = $(this).parent().parent().find("span.hod");
		hod = parseFloat(hod.text());

		let rr = $(this).parent().parent().find("input[name='detalles[rr][]']").val();
		rr = rr.replace(/,/g, "");
		rr = parseFloat(rr);

		let hmr = $(this).parent().parent().find("input[name='detalles[hmr][]']").val();
		hmr = hmr.replace(/,/g, "");
		hmr = parseFloat(hmr);

		let observaciones = $(this).parent().parent().find("input[name='detalles[observaciones][]']").val();

		const rendimiento = hod != 0 ? (lcc/hod)*100 : 0;

		const aprovechamiento = hod != 0 ? ((hmr-rr)/hod)*100 : 0;

		let rendimientohtml = $(this).parent().parent().find("span.rendimiento");
		rendimientohtml.html(number_format(rendimiento,2)+' %');
		
		let aprovechamientohtml = $(this).parent().parent().find("span.aprovechamiento");
		aprovechamientohtml.html(number_format(aprovechamiento,2)+' %');

		let datos = {
			"generador_datelle" : detalleId,
			"hmr" : hmr,
			"rr" : rr,
			"lcc" : lcc,
			"observacion" : observaciones
		}

		actualizarDesempeno(datos);
	
	});

	$("#tablaDesempeno").on("blur", "input[name='detalles[observaciones][]']", function (e) {    

		let detalleId = $(this).parent().parent().find("td.partida").attr("detalle_id");

		let observaciones = $(this).val();
	
		let hod = $(this).parent().parent().find("span.hod");
		hod = parseFloat(hod.text());

		let rr = $(this).parent().parent().find("input[name='detalles[rr][]']").val();
		rr = rr.replace(/,/g, "");
		rr = parseFloat(rr);

		let hmr = $(this).parent().parent().find("input[name='detalles[hmr][]']").val();
		hmr = hmr.replace(/,/g, "");
		hmr = parseFloat(hmr);
		
		let lcc = $(this).parent().parent().find("input[name='detalles[lcc][]']").val();
		lcc = lcc.replace(/,/g, "");
		lcc = parseFloat(lcc);

		let datos = {
			"generador_datelle" : detalleId,
			"hmr" : hmr,
			"rr" : rr,
			"lcc" : lcc,
			"observacion" : observaciones
		}

		actualizarDesempeno(datos);
	
	});


	// Funciones
	//Actualizar datos
	function actualizarDatos(datos)
	{
		let dataSend = new FormData();
		dataSend.append("accion","actualizar")
		dataSend.append("ajuste",datos.ajuste)
		dataSend.append("comb",datos.comb)
		dataSend.append("costo",datos.costo)
		dataSend.append("flete",datos.flete)
		dataSend.append("generador_detalle_id",datos.generador_datelle)
		dataSend.append("mantto",datos.mantto)
		dataSend.append("operacion",datos.operacion)
		dataSend.append("pu",datos.pu)

		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
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
				
				return;

			}

		})
		.fail(function(error) {
			// console.log("*** Error ***");
			console.log(error);
			// console.log(error.responseText);
			// console.log(error.responseJSON);
			// console.log(error.responseJSON.message);
			// console.log(error);
			let elementList = document.createElement('li'); // prepare a new li DOM element
			let newContent = document.createTextNode(error.errorMessage);
			elementList.appendChild(newContent); //añade texto al div creado.
			// elementErrorValidacion.querySelector('ul').appendChild("Ocurrió un error al intentar guardar el insumo, de favor actualice o vuelva a cargar la página e intente de nuevo");
			$(elementErrorValidacion).removeClass("d-none");
		})
	}

	function actualizarDesempeno(datos)
	{
		let dataSend = new FormData();
		dataSend.append("accion","actualizarDesempeno")
		dataSend.append("hmr",datos.hmr)
		dataSend.append("rr",datos.rr)
		dataSend.append("lcc",datos.lcc)
		dataSend.append("generador_detalle",datos.generador_datelle)
		dataSend.append("observaciones",datos.observacion)

		$.ajax({
			url: rutaAjax+'app/Ajax/GeneradorAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
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
				
				return;

			}

		})
	}
	// Obtiene array de dias
	function getDaysArray(startDate, endDate) {
		const daysArray = [];
		let dia = new Date(startDate)
		while (dia <= endDate) {
			daysArray.push(dia.getUTCDate());
			dia.setDate(dia.getUTCDate() + 1);
		}

		return daysArray;
	}
	// Obtener dia en un mes
	function obtenerDiasEnMes(fecha) {
		fecha =new Date(fecha+ '-01T00:00:00');
		// Obtenemos el mes y el año de la fecha proporcionada
		const mes = fecha.getMonth();
		const año = fecha.getFullYear();
		
		// Creamos una nueva fecha para el siguiente mes
		const fechaSiguiente = new Date(año, mes + 1, 1);
		
		// Restamos un día a la fecha siguiente para obtener el último día del mes actual
		const ultimoDiaMes = new Date(fechaSiguiente - 1).getDate();
		return ultimoDiaMes;
	}
	// Añade una celda
	function addCell(tr, content, colSpan = 1, clase = null) {
		let td = document.createElement('td');
	
		td.colSpan = colSpan;
		td.textContent = content;
		if ( clase !== null ) td.classList.add(clase);
	
		tr.appendChild(td);
	}
	// 
	function obtenerRangoFechas(fecha) {
		const primerDiaDelMes = new Date(fecha.getFullYear(), fecha.getMonth(), 1);
		const ultimoDiaDelMes = new Date(fecha.getFullYear(), fecha.getMonth() + 1, 0);
	  
		return {
		  primerDia: primerDiaDelMes,
		  ultimoDia: ultimoDiaDelMes
		};
	}
})