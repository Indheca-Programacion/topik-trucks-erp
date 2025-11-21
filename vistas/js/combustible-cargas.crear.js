let elementEmpresa = document.getElementById("empresaId");
let elementMaquinaria = document.getElementById("maquinariaId");
let actualEmpresaId = null;

/*==================================
Seleccionar empresa select#empresaId
==================================*/
$(elementEmpresa).change(function(event){

	return;

	// Si ya hay maquinarias agregadas no debe permitir cambiar de empresa
	let tableCombustibleDetalles = document.querySelector('#tablaCombustibleDetalles tbody');
	let maquinariasNuevas = tableCombustibleDetalles.querySelectorAll('tr[nuevo]')
	if ( maquinariasNuevas.length > 0 ) {
		if ( this.value != actualEmpresaId ) {
			let mensaje = document.getElementById("msgConsultarMaquinarias");
			$(mensaje).after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>No puede cambiar de empresa, debe eliminar primero las cargas que ha agregado.</div>');
			setTimeout(function(){ 
				$(".alert").remove();
			}, 5000);

			$(elementEmpresa).val(actualEmpresaId).trigger('change');
		}

		return;
	}

	actualEmpresaId = this.value;

	// Limpiar el select #servicioId
	$(elementMaquinaria).html('');
	$(elementMaquinaria).append('<option value="">Selecciona un Número Económico</option>');

	if ( this.value == '' ) return;

	$(this).prop('disabled', true);
	$(elementMaquinaria).prop('disabled', true);

	let mensaje = document.getElementById("msgConsultarMaquinarias");
	mensaje.innerHTML = "<span class='list-group-item list-group-item-success'>Consultando Maquinarias ... por favor espere!</span>";

	let token = $('input[name="_token"]').val();
	let empresaId = this.value;

	let datos = new FormData();
	datos.append("accion", "consultar");
	datos.append("_token", token);
	datos.append("empresaId", empresaId);

	$.ajax({
	    url: rutaAjax+"app/Ajax/CombustibleCargaAjax.php",
	    method: "POST",
	    data: datos,
	    cache: false,
	    contentType: false,
	    processData: false,
	    dataType: "json",
	    success: function(respuesta){

	    	// console.log(respuesta);
	    	mensaje.innerHTML = "";

	    	// Si la respuesta es positiva agregar las ordenes de trabajo
	    	if (respuesta.respuesta) {

				respuesta.respuesta.forEach(function callback(currentValue, index, array) {
					let numeroEconomico = currentValue['numeroEconomico'];

					let itemOrden = `<option value="${currentValue.id}" numero-economico="${numeroEconomico}">
										${numeroEconomico}
									</option>`;

					$(elementMaquinaria).append(itemOrden);
				});

	    		$(mensaje).after('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.respuestaMessage+'</div>');

	    	} else {

	    		$(mensaje).after('<div class="alert alert-warning alert-dismissable"><button type="button" class="close" data-dismiss="alert">&times;</button>'+respuesta.errorMessage+'</div>');

		    }

			$(elementEmpresa).prop('disabled', false);
			$(elementMaquinaria).prop('disabled', false);

			setTimeout(function(){ 
				$(".alert").remove();
			}, 5000);

	    }

	})

})

let minDate = moment().subtract(12, 'months')
let maxDate = moment().add(1, 'hours');

$('#fechaDTP').datetimepicker('minDate', minDate);
$('#fechaDTP').datetimepicker('maxDate', maxDate);
