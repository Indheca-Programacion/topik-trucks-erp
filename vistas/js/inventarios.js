$(function(){

	let tableList = document.getElementById('tablaInventarios');
	let tablaRequisicionDetalles = document.getElementById('tablaRequisicionDetalles');
	let dataTableDetalles = null;
	let datatTableSalidasDetalles = null;
	let parametrosTableList = { responsive: true };

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) {
		fetch( rutaAjax+'app/Ajax/InventarioAjax.php', {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {


			$('#tablaInventarios').DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registroInventarios,
				columns: data.datos.columnasInventarios,
				buttons: [
					{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
				layout: {
					topStart: 'buttons'
				},
				language: LENGUAJE_DT,
				aaSorting: [],

			})

			$('#tablaSalidasListado').DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registroSalidas,
				columns: data.datos.columnaSalidas,
				buttons: [
					{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
				layout: {
					topStart: 'buttons'
				},
				language: LENGUAJE_DT,
				aaSorting: [],

			})


			$('#tablaInventarioGeneral').DataTable({
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registroPartidas,
				columns: data.datos.columnasPartidas,
				buttons: [
					{ extend: 'copy', text:'Copiar', className: 'btn-info' },
					{ extend: 'csv', className: 'btn-info' },
					{ extend: 'excel', className: 'btn-info' },
					{ extend: 'pdf', className: 'btn-info' },
					{ extend: 'print', text:'Imprimir', className: 'btn-info' },
					{ extend: 'colvis', text:'Columnas visibles', className: 'btn-info' }
				],
				

				layout: {
					topStart: 'buttons'
				},
				language: LENGUAJE_DT,
				aaSorting: [],

			})
			
		});
	}
	// FUNCION LLENAR DATOS
	if ( tablaRequisicionDetalles != null &&  $('#inventarioId').val() == 0 ) {
		let requisicionId = $('#requisicionId').val();

		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?requisicionId='+requisicionId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			if (data.datos.registros.length == 0) {
				dataTableDetalles = $('#tablaRequisicionDetalles').DataTable({
					info: false,
					paging: false,
					pageLength: 100,
					searching: false,
					autoWidth: false,
					responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
					data: data.datos.registros,
					columns: data.datos.columnas,
					language: LENGUAJE_DT,
					aaSorting: [],
				})
			}else {

				// AGREGAR CHECKS PARA TODOS
				$('#tablaRequisicionDetalles thead tr')
				.prepend('<th><input type="checkbox" id="selectAll"></th>');

				// Evento para seleccionar/desseleccionar todos los checkboxes
				$('#selectAll').on('click', function () {
					$('.row-checkbox').prop('checked', this.checked);
				});
				
				dataTableDetalles = $('#tablaRequisicionDetalles').DataTable({
					info: false,
					paging: false,
					pageLength: 100,
					searching: false,
					select: true, // Habilita la selección de filas
					autoWidth: false,
					responsive: (parametrosTableList.responsive === undefined) ? true : parametrosTableList.responsive,
					data: data.datos.registros,
					columns: data.datos.columnas,
					columnDefs: [
						{
							targets: 0, // Columna donde agregamos el checkbox
							orderable: false,
							className: 'dt-body-center',
							render: function (data, type, row) {

							if (row.cantidad_disponible === 0) {
								return '<i class="fa fa-check"></i>';
							} else {
								return '<input type="checkbox" class="row-checkbox">';
							}
							}
						},
						{
							targets: 2, // Columna donde quieres agregar el input
							render: function (td, cellData, rowData, row, col) {
								let cantidad = rowData.cantidad < rowData.cantidad_disponible ? rowData.cantidad : rowData.cantidad_disponible;

								return '<input class="form-control form-control-sm cantidad" ' + 
       							(rowData.cantidad_disponible === 0 ? 'disabled' : '') + ' min="1" max="'+rowData.cantidad_disponible+'" type="number" value="' + cantidad + '">';
							}
						},
						{ targets: [0, 1, 2, 3, 4, 5], orderable: false }
					],
					select: {
						style: 'multi',
						selector: 'td:first-child',
						selectable: function (rowData) {
							return rowData.cantidad_disponible !== 0;
						}
					},
					language: LENGUAJE_DT,
					aaSorting: [],
				});
			}
		}); 

	} else if( tablaRequisicionDetalles != null ) {
		let inventarioId = $('#inventarioId').val();

		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?inventarioId='+inventarioId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			dataTableDetalles = $('#tablaRequisicionDetalles').DataTable({
				info: false,
				paging: false,
				pageLength: 100,
				searching: false,
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				columnDefs: [
					{ targets: [0,1,2,3,4,5], orderable: false },
				],
				language: LENGUAJE_DT,
				aaSorting: []
			})

			$('#tablaSalidas').DataTable({
				info: false,
				paging: true,
				pageLength: 10,
				searching: false,
				autoWidth: false,
				responsive: ( parametrosTableList.responsive === undefined ) ? true : parametrosTableList.responsive,
				data: data.salidas.registros,
				columns: data.salidas.columnas,
				columnDefs: [
					{ targets: [0,1,2,3,4], orderable: false },
				],
				language: LENGUAJE_DT,
				aaSorting: []
			})


		}); // .then( data => {

		}

	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Inventario (Folio: '+folio+') ?',
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

	$('#btnFiltrar').on('click', function (e) {
		$(tableList).DataTable().destroy();
		tableList.querySelector('tbody').innerHTML = '';

		let almacenId = $('#filtroAlmacen').val();
		let descripcion = $('#filtroDescripcion').val();

		fActualizarListado(`${rutaAjax}app/Ajax/InventarioAjax.php?almacenId=${almacenId}&descripcion=${descripcion}`, '#tablaInventarios', parametrosTableList);
	});

	// Envio del formulario para Crear o Editar registros
	function enviar(){
		btnEnviar.disabled = true;
		mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Enviando Datos ... por favor espere!</span>";

		padre = btnEnviar.parentNode;
		padre.removeChild(btnEnviar);

		var data = dataTableDetalles.data().toArray();

		var dataURL = signaturePad.toDataURL();

		let dataSend = new FormData(formulario)
		
		dataSend.append("detalles",data);
		dataSend.append("firma",dataURL);
		
		formulario.submit(dataSend);
	}

	let formulario = document.getElementById("formSend");
	let mensaje = document.getElementById("msgSend");
	let btnEnviar = document.getElementById("btnSend");
	let tipo = null

	if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

	// *************************************************
	// SELECT2 Y DATE TIME PICKERS
	// *************************************************

	// Activar el elemento Select2
	$('.select2').select2({
		tags: false,
		width: '100%'
		// ,theme: 'bootstrap4'
	}); // $('.select2').select2({

	$('.select2Add').select2({
		tags: true
		// ,theme: 'bootstrap4'
	}); // $('.select2').select2({

    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    }); // $('.input-group.date').datetimepicker({

	$('#fechaEntregaDTP').datetimepicker();

	// *************************************************
	// Salidas
	// *************************************************

	//AUTORIZAR SALIDA
	$('#tablaSalidas').on("click", "button.autorizar", function (e) {
		e.preventDefault();
		var salidaId = $(this).attr('salidaId'); // Encuentra el formulario más cercano

		let dataSend = new FormData()
		dataSend.append("accion","autorizarSalida");
		dataSend.append("_token",token);
		dataSend.append("salidaId",salidaId);

		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Salida Autorizada', 'Error', respuesta.errorMessage);
				return;
			}
			crearToast('bg-success', 'Salida Autorizada ' ,'', respuesta.mensaje);
			window.location.reload();

		})
	});

	// MODAL PARA FIRMAR SALIDA
	$('#tablaSalidas').on("click", "button.firmar", function (e) {
		e.preventDefault();
		let salidaId = $(this).attr('salidaId'); // Encuentra el formulario más cercano
		$("#salidaId").val(salidaId); // Asignarlo al input

		$('#modalFirmarSalida').modal('show');

	});

	$('#modalFirmarSalida').on('hidden.bs.modal', function () {
		$("#salidaId").val(""); 
	});

	// BOTON PARA FIRMAR SALIDA
	$('#modalFirmarSalida .btnFirmarSalida').on('click',function(){
		let salidaId = $("#salidaId").val(); 
		let token = $('#token').val();
		let recibe = $('#recibe').val();

		if(signaturePad.isEmpty()){
			crearToast("bg-danger","error",'',"Se debe ingresar la firma")
			return
		}

		if (recibe == '' || recibe == 0) {
			crearToast("bg-danger","error",'',"Se debe ingresar el nombre de la persona que recibe")
			return;
		}

		let dataSend = new FormData()
		dataSend.append("accion","firmarSalida");
		dataSend.append("token",token);

		console.log(token)
		dataSend.append("firma",signaturePad.toDataURL());
		dataSend.append("usuarioRecibioId",recibe);
		dataSend.append("salidaId",salidaId);

		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Crear Salida', 'Error', respuesta.errorMessage);
				return;
			}

			crearToast('bg-success', 'Crear Salida', 'OK', respuesta.respuestaMessage);
			window.location.reload();
		})
		

	});

	// MODAL CREAR SALIDA
	$('#modalCrearSalida').on('shown.bs.modal', function () {
		let inventarioId = $('#inventarioId').val();
		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?inventarioId='+inventarioId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			data.datos.columnas.pop(); 	

			datatTableSalidasDetalles = $('#tablaSalidasDetalles').DataTable({
				info: false,
				paging: false,
				pageLength: 100,
				searching: false,
				select: true, // Habilita la selección de filas
				autoWidth: false,
				responsive: (parametrosTableList.responsive === undefined) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				columnDefs: [
					{
						targets: 0, // Columna donde agregamos el checkbox
						orderable: false,
						className: 'dt-body-center',
						render: function (data, type, row) {

						if (row.cantidadDisponible === 0) {
							return '<i class="fa fa-check"></i>';
						} else {
							return '<input type="checkbox" class="row-checkbox">';
						}
						}
					},
					{
						targets: 2, // Columna donde quieres agregar el input
						render: function (td, cellData, rowData, row, col) {
							let cantidad = rowData.cantidad < rowData.cantidadDisponible ? rowData.cantidad : rowData.cantidadDisponible;

							return '<input class="form-control form-control-sm cantidad" ' + 
							   (rowData.cantidadDisponible === 0 ? 'disabled' : '') + ' min="1" max="'+rowData.cantidadDisponible+'" type="number" value="' + cantidad + '">';
						}
					},
					{ targets: [0, 1, 2, 3, 4, 5], orderable: false }
				],
				select: {
					style: 'multi',
					selector: 'td:first-child',
					selectable: function (rowData) {
						return rowData.cantidadDisponible !== 0;
					}
				},
				language: LENGUAJE_DT,
				aaSorting: [],
			});
		}); // .then( data => {
	});

	$('#modalCrearSalida').on('hidden.bs.modal', function () {
		$('#tablaSalidasDetalles').DataTable().destroy();
	});

	// BOTON PARA CREAR SALIDA
	$('#modalCrearSalida .btnGuardarSalida').on('click',function(){
		let token = $('#token').val();
		let almacen = $('#almacenId').val();
		let entradaId = $('#entradaId').val();

		let data = [];
	
		$('#tablaSalidasDetalles tbody tr').each(function() {
			let row = $(this);

			if (row.find('.row-checkbox').prop('checked')) { 
				let rowData = dataTableDetalles.row(row.index()).data();
				let cantidad = row.find('.cantidad').val() || rowData.cantidad;
				rowData.cantidad = cantidad;
				data.push(rowData);

			}
		});

		if (data.length === 0) {
			crearToast("bg-danger","error",'',"Se debe seleccionar al menos un registro")
			return;
		}

		let dataSend = new FormData()
		dataSend.append("accion","crearSalida");
		dataSend.append("_token",token);
		dataSend.append("almacenId",almacen);
		dataSend.append("entradaId",entradaId);
		dataSend.append("detalles",JSON.stringify(data));

		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Crear Salida', 'Error', respuesta.errorMessage);
				return;
			}

			crearToast('bg-success', 'Crear Salida', 'OK', respuesta.respuestaMessage);
			window.location.reload();
		})
		

	});

	// *************************************************
	// RESGUARDOS
	// *************************************************

	$('#tablaSalidas').on("click", "button.resguardo", function (e) {
		e.preventDefault();
		var salidaId = $(this).attr('salidaId'); // Encuentra el formulario más cercano

		fetch( rutaAjax+'app/Ajax/InventarioAjax.php?salidaId='+salidaId, {
			method: 'GET', // *GET, POST, PUT, DELETE, etc.
			cache: 'no-cache', // *default, no-cache, reload, force-cache, only-if-cached
			headers: {
				'Content-Type': 'application/json'
			}
		} )
		.then( response => response.json() )
		.catch( error => console.log('Error:', error) )
		.then( data => {

			data.datos.columnas.pop(); 	
			
			datatTableSalidasDetalles = $('#tablaSalidasResguardo').DataTable({
				info: false,
				paging: false,
				pageLength: 100,
				searching: false,
				select: true, // Habilita la selección de filas
				autoWidth: false,
				responsive: (parametrosTableList.responsive === undefined) ? true : parametrosTableList.responsive,
				data: data.datos.registros,
				columns: data.datos.columnas,
				columnDefs: [
					{
						targets: 0, // Columna donde agregamos el checkbox
						orderable: false,
						className: 'dt-body-center',
						render: function (data, type, row) {

						if (row.cantidad === 0) {
							return '<i class="fa fa-check"></i>';
						} else {
							return '<input type="checkbox" class="row-checkbox">';
						}
						}
					},
					{
						targets: 2, // Columna donde quieres agregar el input
						render: function (td, cellData, rowData, row, col) {
							let cantidad = rowData.cantidad;

							return '<input class="form-control form-control-sm cantidad" ' + 
							   (rowData.cantidad === 0 ? 'disabled' : '') + ' min="1" max="'+rowData.cantidad+'" type="number" value="' + cantidad + '">';
						}
					},
					{ targets: [0, 1, 2, 3, 4, 5], orderable: false }
				],
				select: {
					style: 'multi',
					selector: 'td:first-child',
					selectable: function (rowData) {
						return rowData.cantidadDisponible !== 0;
					}
				},
				language: LENGUAJE_DT,
				aaSorting: [],
			});
		});
		
		$("#salidaId").val(salidaId); // Asignarlo al input
		// Abre el modal
		$('#modalCrearResguardo').modal('show');
	});

	$('#modalCrearResguardo').on('hidden.bs.modal', function () {
		
		$("#salidaId").val(""); // Asignarlo al input
		$('#tablaSalidasResguardo').DataTable().destroy();
	});

	// CREAR RESGUARDO
	$('#modalCrearResguardo .btnGuardarResguardo').on('click',function(){

		let recibe = $('#recibeResguardo').val();
		let token = $('#token').val();
		let observaciones = $('#observacionesResguardo').val();
		let fecha = $('#fecha').val();
		let salidaId = $('#salidaId').val();


		if(signaturePadResguardo.isEmpty()){
			crearToast("bg-danger","error",'',"Se debe ingresar la firma")
			return
		}

		let data = [];

		$('#tablaSalidasResguardo tbody tr').each(function() {
			let row = $(this);

			if (row.find('.row-checkbox').prop('checked')) {
				// Recupera el rowData de DataTables con el elemento DOM
				let rowData = datatTableSalidasDetalles.row(row).data();

				// Toma la cantidad del input, o el valor original
				let cantidad = row.find('.cantidad').val() || rowData.cantidad;
				rowData.cantidad = cantidad;

				data.push(rowData);
			}
		});

		if (data.length === 0) {
			crearToast("bg-danger","error",'',"Se debe seleccionar al menos un registro")
			return;
		}

		if (recibe == '' || recibe == 0) {
			crearToast("bg-danger","error",'',"Se debe ingresar el nombre de la persona que recibe")
			return;
		}

		let dataSend = new FormData()
		dataSend.append("accion","crearSalidaResguardo");
		dataSend.append("_token",token);
		dataSend.append("observaciones",observaciones);
		dataSend.append("fecha",fecha);
		dataSend.append("firma",signaturePadResguardo.toDataURL());
		dataSend.append("usuarioRecibioId",recibe);
		dataSend.append("salidaId",salidaId);
		dataSend.append("detalles",JSON.stringify(data));
		
		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
		})
		.done(function(respuesta) {
			if ( respuesta.error ) {
				crearToast('bg-danger', 'Crear Salida', 'Error', respuesta.errorMessage);
				return;
			}

			crearToast('bg-success', 'Crear Salida', 'OK', respuesta.respuestaMessage);
			window.location.href= respuesta.ruta;
		})
		

	});

	// *************************************************
	// AGREGAR PARTIDA
	// *************************************************

	$('#btnAgregarPartida').on('click',function(){

		let descripcion = $('#descripcion').val();
		let cantidad = $('#cantidad').val();
		let costo_unitario = $('#costo_unitario').val();
		let unidad = $('#unidad').val();
		let numeroParte = $('#numeroParte').val();

		if(descripcion == '' || cantidad == '' || unidad == ''){
			crearToast("bg-danger","error",'',"Se deben llenar todos los campos")
			return
		}

		let cantidadDatos = dataTableDetalles.data().toArray().length;

		let data = {
			"id":'<input type="checkbox" class="row-checkbox" disabled checked >  <button type="button" class="btn btn-danger btn-sm eliminarPartida"><i class="fas fa-trash-alt"></i></button>',
			"consecutivo": cantidadDatos+1,
			"concepto": descripcion,
			"cantidad": cantidad,
			"costo_unitario": costo_unitario,
			"unidad": unidad,
			"numeroParte": numeroParte
		}
		
		dataTableDetalles.row.add(data).draw();

		$('#descripcion').val('');
		$('#cantidad').val(1);
		$('#unidad').val('');
		$('#costo_unitario').val(0);
		$('#numeroParte').val('NA');
	});

	$('#tablaRequisicionDetalles').on('click', 'button.eliminarPartida', function () {
		let rowIndex = dataTableDetalles.row($(this).closest('tr')).index();

		let data = dataTableDetalles.data().toArray();
		data.splice(rowIndex, 1);
		data.forEach((element, index) => {
			element.consecutivo = index + 1;
		});

		dataTableDetalles.clear().rows.add(data).draw();
	});

	// *************************************************
	// Crear Entrada
	// *************************************************

	$('#btnGuardar').on('click',function(){

		let elementErrorValidacion = document.querySelector('.error-validacion');
		elementErrorValidacion.querySelector('ul').innerHTML = '';
		$(elementErrorValidacion).addClass("d-none");
		
		if(signaturePad.isEmpty()){
			crearToast("bg-danger","error",'',"Se debe ingresar la firma")
			return
		}
				
		let data = [];
	
		$('#tablaRequisicionDetalles tbody tr').each(function() {
			let row = $(this);

			
			if (row.find('.row-checkbox').prop('checked')) { 

				let rowData = dataTableDetalles.row(row.index()).data();

				let cantidad = row.find('.cantidad').val() || rowData.cantidad;
	
				rowData.cantidad = cantidad;
	
				data.push(rowData);
			}
		});

		if (data.length === 0) {
			crearToast("bg-danger","error",'',"Se debe seleccionar al menos un registro")
			return;
		}

		// obtiene la imagen de la firma
		let dataURL = signaturePad.toDataURL();
		let elementFirma = document.getElementById("firma");

		elementFirma.value = dataURL;

		let dataSend = new FormData(formulario)

		let requisicionId = $('#requisicionId').val();
		dataSend.append("requisicionId",requisicionId);

		dataSend.append("accion","guardar");
		dataSend.append("detalles",JSON.stringify(data));

		$.ajax({
			url: rutaAjax+'app/Ajax/InventarioAjax.php',
			method: 'POST',
			data: dataSend,
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json'
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

			crearToast('bg-success', 'Crear Inventario', 'OK', respuesta.respuestaMessage);
			window.location.href= respuesta.ruta;

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
	});

	// *************************************************
	// Imagenes
	// *************************************************
	
	let inventario_detalle=null;
	$(document).on('click',".btn-subirArchivo",function(){
		inventario_detalle = this.getAttribute('id')
		document.getElementById('archivoSubir').click();

	})

	$("#archivoSubir").change(function() {
		let archivos = this.files;
		let token = document.getElementById('token');
		if ( archivos.length == 0) return;

		let error = false;

		for (let i = 0; i < archivos.length; i++) {

			let archivo = archivos[i];
			
			/*==========================================
			VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
			==========================================*/
			
			if ( archivo["type"] != "image/jpeg" && archivo["type"] != "image/png" && archivo["type"] != "image/jpg" ) {

				error = true;

				// $("#comprobanteArchivos").val("");
				// $("div.subir-comprobantes span.lista-archivos").html('');

				Swal.fire({
					title: 'Error en el tipo de archivo',
					text: '¡El archivo "'+archivo["name"]+'" debe ser jpeg, png o jpg!',
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
		formData.append("inventario_detalle",inventario_detalle)
		formData.append("_token",token.value)
		// Enviamos la solicitud AJAX
		fetch(rutaAjax+"app/Ajax/InventarioAjax.php", {
			method: 'POST',
			body: formData
		})
		.then(response => response.text())
		.then(data => {
			crearToast('bg-success', 'Insertar Documentos', 'OK', data.respuestaMessage);
		})
		.catch(error => {
		console.error('Error:', error);
		});

   	}) // $("#archivos").change(function(){

	let modalVerImagenes = document.getElementById('modalVerImagenes');

	$(document).on('click', '.verImagenes',function(e){
		let partida = this.getAttribute('partida');
		$("#modalVerImagenesLabel span").html(partida);
		$("#modalVerImagenes div.imagenes").html('');

		let token = $('input[name="_token"]').val();
		let detalleId = $(this).attr("partida");

		let datos = new FormData();
		datos.append("accion", "verImagenes");
		datos.append("_token", token);
		datos.append("partida", detalleId);

		$.ajax({
		    url: rutaAjax+'app/Ajax/InventarioAjax.php',
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
								<img src="${imagen.ruta.slice(5)}" class="card-img-top" alt="${imagen.titulo}">
							</div>
						</div>`;

					$("#modalVerImagenes div.imagenes").append(elementImagen);
				});

		    }

		})

	})

	// *************************************************
	// Canvas para firmar
	// *************************************************

	const canvas = document.querySelector("#canvas");
	const signaturePad = new SignaturePad(canvas);

	const canvasResguardo = document.querySelector("#canvasResguardo");
	const signaturePadResguardo = new SignaturePad(canvasResguardo);


	$('#btnLimpiarResguardo').on('click',function(){
		signaturePadResguardo.clear();
	})


	$('#btnLimpiar').on('click',function(){
		signaturePad.clear();
	})

});
