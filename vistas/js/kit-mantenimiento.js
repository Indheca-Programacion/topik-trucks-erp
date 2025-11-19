$(function(){
    
	let tableList = document.getElementById('tablaKitMantenimiento');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/KitMantenimientoAjax.php', '#tablaKitMantenimiento');

	// Confirmar la eliminación del Kit de Mantenimiento
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Kit de Mantenimiento (Descripción: '+folio+') ?',
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

	$(".select2").select2({
		tags: true,
		width: "100%",
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

	function agregarPartida(){
		let elementCantidad = document.getElementById("cantidad");
		let elementUnidad = document.getElementById("unidad");
		let elementNumeroParte = document.getElementById("numeroParte");
		let elementConcepto = document.getElementById("concepto");

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

		let tableRequisicionDetalles = document.querySelector('#tblComponentes tbody');
		let registros = tableRequisicionDetalles.querySelectorAll('tr');
		// let ultimaPartida = tableRequisicionDetalles.lastElementChild;
		// let partida = ( ultimaPartida === null ) ? 1 : parseInt(ultimaPartida.getAttribute('partida')) + 1;

		let registrosNuevos = tableRequisicionDetalles.querySelectorAll('tr[nuevo]');
		let partida = registrosNuevos.length + 1;

		let elementRow = `<tr nuevo partida="${partida}">
							<td partida class="text-right"><span>${registros.length + 1}</span><input type="hidden" name="detalles[partida][]" value="${partida}"></td>
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

	$(".btnEditarComponente").click(function(){
		let id = $(this).data("id");
		let cantidad = $(this).data("cantidad");
		let unidad = $(this).data("unidad");
		let numeroParte = $(this).data("numeroparte");
		let concepto = $(this).data("concepto");

		$("#cantidad_detalle").val(cantidad);
		$("#unidad_detalle").val(unidad).trigger('change');
		$("#numero_parte_detalle").val(numeroParte);
		$("#concepto_detalle").val(concepto);
		$("#id_detalle").val(id);

		$("#modalEditarDetalle").modal("show");
	});

	$(".btnGuardarCambios").click(function(){
		let id = $("#id_detalle").val();
		let cantidad = $("#cantidad_detalle").val();
		let unidad = $("#unidad_detalle").val();
		let numeroParte = $("#numero_parte_detalle").val();
		let concepto = $("#concepto_detalle").val();

		$.ajax({
			type: "POST",
			url: rutaAjax+'app/Ajax/KitMantenimientoAjax.php',
			data: {
				accion: "editar_detalle",
				id: id,
				cantidad: cantidad,
				unidad: unidad,
				numeroParte: numeroParte,
				concepto: concepto
			},
			dataType: "json",
			beforeSend: function() {
				$(".btnGuardarCambios").prop("disabled", true);
			}
		}).done(function( response ) {
			if ( response.error == false ) {
				Swal.fire({
					icon: 'success',
					title: '¡Éxito!',
					text: 'Los cambios se han guardado correctamente.'
				}).then(() => {
					location.reload();
				});
			} else {
				Swal.fire({
					icon: 'error',
					title: '¡Error!',
					text: 'No se pudieron guardar los cambios.'
				});
			}
		});
	});
});