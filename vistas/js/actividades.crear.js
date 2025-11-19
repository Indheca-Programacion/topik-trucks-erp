let elementEmpresa = document.getElementById("empresaId");
let elementOrdenTrabajo = document.getElementById("servicioId");
let actualEmpresaId = null;

/*==================================
Seleccionar empresa select#empresaId
==================================*/
$(elementEmpresa).change(function(event){

	// Si ya hay ordenes agregadas no debe permitir cambiar de empresa
	let tableActividadDetalles = document.querySelector('#tablaActividadDetalles tbody');
	let ordenesNuevas = tableActividadDetalles.querySelectorAll('tr[nuevo]')
	if ( ordenesNuevas.length > 0 ) {
		if ( this.value != actualEmpresaId ) {
			let mensaje = document.getElementById("msgConsultarOrdenesTrabajo");
			$(mensaje).after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>No puede cambiar de empresa, debe eliminar primero las actividades que ha agregado.</div>');
			setTimeout(function(){ 
				$(".alert").remove();
			}, 5000);

			$(elementEmpresa).val(actualEmpresaId).trigger('change');
		}

		return;
	}

	actualEmpresaId = this.value;

	// Limpiar el select #servicioId
	$(elementOrdenTrabajo).html('');
	$(elementOrdenTrabajo).append('<option value="">Selecciona una Orden de Trabajo</option>');

	if ( this.value == '' ) return;

	$(this).prop('disabled', true);
	$(elementOrdenTrabajo).prop('disabled', true);

	let mensaje = document.getElementById("msgConsultarOrdenesTrabajo");
	mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Consultando Ordenes de Trabajo ... por favor espere!</span>";

	let token = $('input[name="_token"]').val();
	let empresaId = this.value;

	let datos = new FormData();
	datos.append("accion", "consultar");
	datos.append("_token", token);
	datos.append("empresaId", empresaId);

	$.ajax({
	    url: rutaAjax+"app/Ajax/ActividadAjax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success: function(respuesta){

	    	mensaje.innerHTML = "";	    	

	    	// Si la respuesta es positiva agregar las ordenes de trabajo
	    	if (respuesta.respuesta) {

				respuesta.respuesta.forEach(function callback(currentValue, index, array) {
					let folio = currentValue.folio.toUpperCase();
					// let serie = currentValue['maquinarias.serie'];
					let numeroEconomico = currentValue['maquinarias.numeroEconomico'];

					let itemOrden = `<option value="${currentValue.id}" folio="${folio}">
											${folio} [ ${numeroEconomico} ]
										</option>`;

					$(elementOrdenTrabajo).append(itemOrden);
				});

	    		$(mensaje).after('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

	    	} else {

	    		$(mensaje).after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    }

			$(elementEmpresa).prop('disabled', false);
			$(elementOrdenTrabajo).prop('disabled', false);

			setTimeout(function(){ 
				$(".alert").remove();
			}, 5000);

	    }

	})

})

let minDate = moment().subtract(1, 'months')
// let maxDate = moment().add(1, 'months');
let maxDate = moment().add(1, 'hours');

$('#fechaInicialDTP').datetimepicker('minDate', minDate);
$('#fechaInicialDTP').datetimepicker('maxDate', maxDate);

$("#fechaInicialDTP").on("change.datetimepicker", function (event) {

	$('#fechaActividadDTP').datetimepicker('minDate', minDate);
	$('#fechaActividadDTP').datetimepicker('maxDate', maxDate);

	if ( $('#fechaInicial').val() == '') {
		$('#fechaFinal').val('');
		$('#fechaActividad').val('');
	} else if ( event.date !== undefined ) {
		let fechaMinima = moment(JSON.parse(JSON.stringify(event.date)));
		let fechaMaxima = moment(fechaMinima).add(6, 'd');
		
		$('#fechaFinalDTP').datetimepicker('date', fechaMaxima);

		$('#fechaActividadDTP').datetimepicker('clear');
		$('#fechaActividadDTP').datetimepicker('minDate', fechaMinima);
		$('#fechaActividadDTP').datetimepicker('maxDate', fechaMaxima);
		$('#fechaActividadDTP').datetimepicker('date', fechaMinima);
	}

});
