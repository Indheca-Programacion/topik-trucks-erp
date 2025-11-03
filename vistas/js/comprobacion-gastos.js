$(function(){

	let tableList = document.getElementById('tablaComprobacionGastos');

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
	if ( tableList != null ) fActualizarListado(rutaAjax+'app/Ajax/ComprobacionGastosAjax.php', '#tablaComprobacionGastos');

	// Confirmar la eliminación del Color
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Comprobacion de Gastos (Descripción: '+folio+') ?',
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

	$(".select2").select2({
		tags: true,
		width: "100%",
	});

	$('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });

	function agregarPartida(){
		let elementCantidad = document.getElementById("cantidad");
		let elementCostoUnitario = document.getElementById("costo_unitario");
		let elementUnidad = document.getElementById("unidad");
		let elementNumeroParte = document.getElementById("numeroParte");
		let elementConcepto = document.getElementById("concepto");
		let elementCodigo = document.getElementById("codigoId");
		let elementFotos = document.getElementById("fotos");

		let cantidad = elementCantidad.value;
		let costoUnitario = elementCostoUnitario.value;
		let unidad = elementUnidad.value.trim();
		let numeroParte = elementNumeroParte.value.trim();
		let concepto = elementConcepto.value.trim();
		
		// Validar que la suma de costos existentes + costo nuevo no supere el monto disponible
		let elementMonto = document.getElementById("monto");
		if ( elementMonto !== null ) {
			let parseNumber = v => {
				if (v === undefined || v === null) return 0;
				if (typeof v === 'number') return v;
				v = String(v).trim();
				if (v === '') return 0;
				// quitar signos de moneda, comas, espacios
				v = v.replace(/[^0-9\.\-]/g, '');
				let n = parseFloat(v);
				return isNaN(n) ? 0 : n;
			};

			// Sumar costos existentes en la tabla (multiplicar cantidad * costo por fila)
			let totalExistente = 0;
			let filas = document.querySelectorAll('#tablaRequisicionDetalles tbody tr');
			filas.forEach(row => {
				// intentar obtener inputs escondidos primero
				let inpCantidad = row.querySelector('input[name="detalles[cantidad][]"]');
				let inpCosto = row.querySelector('input[name="detalles[costo][]"]');
				let cantidadVal = inpCantidad ? parseNumber(inpCantidad.value) : null;
				let costoVal = inpCosto ? parseNumber(inpCosto.value) : null;

				// si no existen inputs, leer celdas visibles (según estructura: 1=cantidad, 3=costo)
				if (cantidadVal === null) {
					let cellCantidad = row.children[1];
					cantidadVal = cellCantidad ? parseNumber(cellCantidad.textContent) : 0;
				}
				if (costoVal === null) {
					let cellCosto = row.children[3];
					costoVal = cellCosto ? parseNumber(cellCosto.textContent) : 0;
				}

				totalExistente += (cantidadVal * costoVal);
			});

			let cantidadNum = parseNumber(cantidad);
			let costoUnitarioNum = parseNumber(costoUnitario);
			let nuevoTotalLinea = cantidadNum * costoUnitarioNum;
			let montoDisponible = parseNumber(elementMonto.value);

			if ( (totalExistente + nuevoTotalLinea) > montoDisponible ) {
				// marcar campo costo como inválido y mostrar mensaje
				elementCostoUnitario.classList.add("is-invalid");
				let padreCosto = elementCostoUnitario.parentElement;
				let divErr = document.createElement("div");
				divErr.classList.add("invalid-feedback");
				let txt = document.createTextNode("La suma de los costos excede el monto disponible (" + montoDisponible.toFixed(2) + "). Total actual: " + totalExistente.toFixed(2) + ". Nueva partida: " + nuevoTotalLinea.toFixed(2) + ".");
				divErr.appendChild(txt);
				// remover feedback previo si existe
				let prev = padreCosto.querySelector('div.invalid-feedback');
				if ( prev !== null ) padreCosto.removeChild(prev);
				padreCosto.appendChild(divErr);

				Swal.fire({
					title: 'Monto excedido',
					text: 'La suma de los costos no puede superar el monto disponible.',
					icon: 'error',
					confirmButtonText: 'Cerrar'
				});

				return;
			}
		}

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

		elementCostoUnitario.classList.remove("is-invalid");
		elementPadre = elementCostoUnitario.parentElement;
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

		if ( costoUnitario == '' ) {
			elementCostoUnitario.classList.add("is-invalid");
			elementPadre = elementCostoUnitario.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El costo unitario es obligatorio.");
		 	newDiv.appendChild(newContent);
			elementPadre.appendChild(newDiv);

			errores = true;
		} else if ( costoUnitario.length > 10 ) {
			elementCostoUnitario.classList.add("is-invalid");
			elementPadre = elementCostoUnitario.parentElement;
			newDiv = document.createElement("div");
			newDiv.classList.add("invalid-feedback");
	  		newContent = document.createTextNode("El campo Costo Unitario debe ser máximo de 8 dígitos.");
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
							<td partida class="text-right"><span>${registros.length + 1}</span><input type="hidden" name="detalles[partida][]" value="${partida}"></td>
							<td class="text-right">${cantidad}<input type="hidden" name="detalles[cantidad][]" value="${cantidad}"></td>
							<td>${unidad}</td>
							<td>${costoUnitario}</td>
							<td numeroParte>${numeroParte}</td>
							<td>${concepto}</td>
						</tr>`;

		$(tableRequisicionDetalles).append(elementRow);

		let rowNuevoRegistro = tableRequisicionDetalles.querySelector(`tr[partida="${partida}"]`);
		let columnaConcepto = rowNuevoRegistro.querySelector('td:last-child');

		let cloneElementCosto = elementCostoUnitario.cloneNode(true);
		cloneElementCosto.removeAttribute('id');
		cloneElementCosto.type = 'hidden';
		cloneElementCosto.name = 'detalles[costo][]';
		$(columnaConcepto).append(cloneElementCosto);

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

	$("div.subir-comprobantes, div.subir-soportes").on("click", "i.eliminarArchivo", function (e) {

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

	/*==============================================================
	BOTON PARA VER ARCHIVOS
	==============================================================*/
	$('.verArchivo').on('click', function () {
		var archivoRuta = $(this).attr('archivoRuta');
		$('#pdfViewer').attr('src', archivoRuta);

		// Mostrar el modal
		$('#pdfModal').modal('show');
	});

	/*==============================================================
	boton para descargar archivos
	==============================================================*/
	$('#btnDescargarSoportes').on('click', function (e) {
		e.preventDefault();
		var icons = document.querySelectorAll('div.subir-soportes i.verArchivo');
		if (!icons || icons.length === 0) {
			Swal.fire({
				title: 'No hay archivos',
				text: 'No se encontraron archivos para descargar.',
				icon: 'info',
				confirmButtonText: 'Cerrar'
			});
			return;
		}

		icons.forEach((icon, idx) => {
			// obtener atributo archivoRuta (o fallback a data-archivoRuta o src)
			let url = icon.getAttribute('archivoRuta') || icon.getAttribute('data-archivoRuta') || icon.dataset.archivoruta || icon.dataset.archivoRuta || icon.src;
			if (!url) return;

			try {
				const a = document.createElement('a');
				a.href = url;
				// intentar obtener nombre de archivo desde la URL
				const parts = url.split('/');
				let filename = parts.pop().split('?')[0] || `archivo_${idx+1}`;
				a.download = filename;
				a.style.display = 'none';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			} catch (e) {
				// fallback: abrir en nueva pestaña
				window.open(url, '_blank');
			}
		});
	});

	$('#btnDescargarComprobantes').on('click', function (e) {
		e.preventDefault();
		var icons = document.querySelectorAll('div.subir-comprobantes i.verArchivo');
		if (!icons || icons.length === 0) {
			Swal.fire({
				title: 'No hay archivos',
				text: 'No se encontraron archivos para descargar.',
				icon: 'info',
				confirmButtonText: 'Cerrar'
			});
			return;
		}
		icons.forEach((icon, idx) => {
			// obtener atributo archivoRuta (o fallback a data-archivoRuta o src)
			let url = icon.getAttribute('archivoRuta') || icon.getAttribute('data-archivoRuta') || icon.dataset.archivoruta || icon.dataset.archivoRuta || icon.src;
			if (!url) return;
			try {
				const a = document.createElement('a');
				a.href = url;
				// intentar obtener nombre de archivo desde la URL
				const parts = url.split('/');
				let filename = parts.pop().split('?')[0] || `archivo_${idx+1}`;
				a.download = filename;
				a.style.display = 'none';
				document.body.appendChild(a);
				a.click();
				document.body.removeChild(a);
			} catch (e) {
				// fallback: abrir en nueva pestaña
				window.open(url, '_blank');
			}
		});
	});

	$('#btnDescargarTodo').on('click', function (e) {
		e.preventDefault();
		$('#btnDescargarComprobantes').click();
		$('#btnDescargarSoportes').click();
	});
});