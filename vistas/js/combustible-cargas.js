let tableList = document.getElementById('tablaCombustibleCargas');

// LLamar a la funcion fAjaxDataTable() para llenar el Listado
if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/CombustibleCargaAjax.php', '#tablaCombustibleCargas');

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
	let empleadoId = $('#filtroEmpleadoId').val();

	fAjaxDataTable(`${rutaAjax}app/Ajax/CombustibleCargaAjax.php?empresaId=${empresaId}&empleadoId=${empleadoId}`, '#tablaCombustibleCargas');
});

// Confirmar la eliminación de la Carga de Combustible
$(tableList).on("click", "button.eliminar", function (e) {

    e.preventDefault();
    var folio = $(this).attr("folio");
    var form = $(this).parents('form');

    Swal.fire({
		title: '¿Estás Seguro de querer eliminar esta Carga de Combustible (Registro: '+folio+') ?',
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
//Date picker
$('.input-group.date').datetimepicker({
    format: 'DD/MMMM/YYYY'
});
$('.input-group.hour').datetimepicker({
    // format: 'LT'
    format: 'HH:mm'
    // format: 'hh:mm A'
});

let elementEmpresaId = $('#empresaId.select2.is-invalid');    
let elementEmpleadoId = $('#empleadoId.select2.is-invalid');
if ( elementEmpresaId.length == 1 ) {
	$('span[aria-labelledby="select2-empresaId-container"]').css('border-color', '#dc3545');
	$('span[aria-labelledby="select2-empresaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
	$('span[aria-labelledby="select2-empresaId-container"]').css('background-repeat', 'no-repeat');
	$('span[aria-labelledby="select2-empresaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
	$('span[aria-labelledby="select2-empresaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
}
if ( elementEmpleadoId.length == 1 ) {
	$('span[aria-labelledby="select2-empleadoId-container"]').css('border-color', '#dc3545');
	$('span[aria-labelledby="select2-empleadoId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
	$('span[aria-labelledby="select2-empleadoId-container"]').css('background-repeat', 'no-repeat');
	$('span[aria-labelledby="select2-empleadoId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
	$('span[aria-labelledby="select2-empleadoId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');
}

let campoMaquinariaId = document.getElementById('maquinariaId');
$(campoMaquinariaId).on('change', function (e) {

	$("#maquinariaSerie").val('');
	$("#maquinariaTipoDescripcion").val('');
	// $("#maquinariaUbicacionDescripcion").val('');
	$("#ubicacionActualId").val('');
	$('#ubicacionId').val(null).trigger('change');

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
			// console.log(data.datos)
			if ( data.datos.maquinaria ) {
				$("#maquinariaSerie").val(data.datos.maquinaria['serie']);
				$("#maquinariaTipoDescripcion").val(data.datos.maquinaria['maquinaria_tipos.descripcion']);
				// $("#maquinariaUbicacionDescripcion").val(data.datos.maquinaria['ubicaciones.descripcion']);
				$("#ubicacionActualId").val(data.datos.maquinaria.ubicacionId);
				$('#ubicacionId').val(data.datos.maquinaria.ubicacionId).trigger('change');
			}
		}); // .then( data => {

	} // if ( campoMaquinariaId.value != '' )

});

// Agregar Carga
function agregarCarga(){

	let elementPadre = null;
	let newDiv = null;
	let newContent = null;

	let elementMaquinariaId = document.getElementById("maquinariaId");
	let elementUbicacionActualId = document.getElementById("ubicacionActualId");
	let elementUbicacionId = document.getElementById("ubicacionId");
	let elementOperadorId = document.getElementById("operadorId");
	let elementHoroOdometro = document.getElementById("horoOdometro");
	let elementLitros = document.getElementById("litros");
	let elementObservaciones = document.getElementById("observaciones");

	
	let maquinariaId = elementMaquinariaId.value;
	let maquinariaText = elementMaquinariaId.options[elementMaquinariaId.selectedIndex].text;
	// let servicioText = elementServicioId.options[elementServicioId.selectedIndex].getAttribute('folio');
	let ubicacionActualId = elementUbicacionActualId.value;
	let ubicacionId = elementUbicacionId.value;
	let ubicacionText = elementUbicacionId.options[elementUbicacionId.selectedIndex].text;
	let operadorId = elementOperadorId.value;
	let operadorText = elementOperadorId.options[elementOperadorId.selectedIndex].text;
	let horoOdometro = elementHoroOdometro.value;
	let litros = elementLitros.value;
	let observaciones = elementObservaciones.value;
	
	elementMaquinariaId.classList.remove("is-invalid");
	elementPadre = elementMaquinariaId.parentElement;
	newDiv = elementPadre.querySelector('div.invalid-feedback');
	if ( newDiv != null ) elementPadre.removeChild(newDiv);
	$('span[aria-labelledby="select2-maquinariaId-container"]').prop("style").removeProperty("border-color");
	$('span[aria-labelledby="select2-maquinariaId-container"]').prop("style").removeProperty("background-image");
	$('span[aria-labelledby="select2-maquinariaId-container"]').prop("style").removeProperty("background-repeat");
	$('span[aria-labelledby="select2-maquinariaId-container"]').prop("style").removeProperty("background-position");
	$('span[aria-labelledby="select2-maquinariaId-container"]').prop("style").removeProperty("background-size");

	elementUbicacionId.classList.remove("is-invalid");
	elementPadre = elementUbicacionId.parentElement;
	newDiv = elementPadre.querySelector('div.invalid-feedback');
	if ( newDiv != null ) elementPadre.removeChild(newDiv);
	$('span[aria-labelledby="select2-ubicacionId-container"]').prop("style").removeProperty("border-color");
	$('span[aria-labelledby="select2-ubicacionId-container"]').prop("style").removeProperty("background-image");
	$('span[aria-labelledby="select2-ubicacionId-container"]').prop("style").removeProperty("background-repeat");
	$('span[aria-labelledby="select2-ubicacionId-container"]').prop("style").removeProperty("background-position");
	$('span[aria-labelledby="select2-ubicacionId-container"]').prop("style").removeProperty("background-size");

	elementOperadorId.classList.remove("is-invalid");
	elementPadre = elementOperadorId.parentElement;
	newDiv = elementPadre.querySelector('div.invalid-feedback');
	if ( newDiv != null ) elementPadre.removeChild(newDiv);
	$('span[aria-labelledby="select2-operadorId-container"]').prop("style").removeProperty("border-color");
	$('span[aria-labelledby="select2-operadorId-container"]').prop("style").removeProperty("background-image");
	$('span[aria-labelledby="select2-operadorId-container"]').prop("style").removeProperty("background-repeat");
	$('span[aria-labelledby="select2-operadorId-container"]').prop("style").removeProperty("background-position");
	$('span[aria-labelledby="select2-operadorId-container"]').prop("style").removeProperty("background-size");

	elementHoroOdometro.classList.remove("is-invalid");
	elementPadre = elementHoroOdometro.parentElement;
	newDiv = elementPadre.querySelector('div.invalid-feedback');
	if ( newDiv != null ) elementPadre.removeChild(newDiv);

	elementLitros.classList.remove("is-invalid");
	elementPadre = elementLitros.parentElement;
	newDiv = elementPadre.querySelector('div.invalid-feedback');
	if ( newDiv != null ) elementPadre.removeChild(newDiv);

	let errores = false;

	// let registroCarga = document.querySelector(`#tablaCombustibleDetalles tbody tr[maquinariaId="${maquinariaId}"]`);
	
	if ( maquinariaId == '' ) {
		elementMaquinariaId.classList.add("is-invalid");
		elementPadre = elementMaquinariaId.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("El número económico es obligatorio.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		$('span[aria-labelledby="select2-maquinariaId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-maquinariaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');

		errores = true;
	}
	// else if ( registroCarga !== null ) {
		// elementMaquinariaId.classList.add("is-invalid");
		// elementPadre = elementMaquinariaId.parentElement;
		// newDiv = document.createElement("div");
		// newDiv.classList.add("invalid-feedback");
  // 		newContent = document.createTextNode("El número económico ya ha sido registrado.");
	 // 	newDiv.appendChild(newContent); //añade texto al div creado.
		// elementPadre.appendChild(newDiv);

		// $('span[aria-labelledby="select2-maquinariaId-container"]').css('border-color', '#dc3545');
		// $('span[aria-labelledby="select2-maquinariaId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		// $('span[aria-labelledby="select2-maquinariaId-container"]').css('background-repeat', 'no-repeat');
		// $('span[aria-labelledby="select2-maquinariaId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		// $('span[aria-labelledby="select2-maquinariaId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');

		// errores = true;
	// }

	if ( ubicacionId == '' ) {
		elementUbicacionId.classList.add("is-invalid");
		elementPadre = elementUbicacionId.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("La ubicación es obligatoria.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		$('span[aria-labelledby="select2-ubicacionId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-ubicacionId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');

		errores = true;
	}

	if ( operadorId == '' ) {
		elementOperadorId.classList.add("is-invalid");
		elementPadre = elementOperadorId.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("El operador es obligatorio.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		$('span[aria-labelledby="select2-operadorId-container"]').css('border-color', '#dc3545');
		$('span[aria-labelledby="select2-operadorId-container"]').css('background-image', 'url('+rutaAjax+'vistas/img/is-invalid.svg)');
		$('span[aria-labelledby="select2-operadorId-container"]').css('background-repeat', 'no-repeat');
		$('span[aria-labelledby="select2-operadorId-container"]').css('background-position', 'right calc(0.375em + 1.0875rem) center');
		$('span[aria-labelledby="select2-operadorId-container"]').css('background-size', 'calc(0.75em + 0.375rem) calc(0.75em + 0.375rem');

		errores = true;
	}

	if ( parseFloat(horoOdometro) < 0.01 ) {
		elementHoroOdometro.classList.add("is-invalid");
		elementPadre = elementHoroOdometro.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("El valor del campo Horómetro / Odómetro no puede ser menor a 0.1.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		errores = true;
	} else if ( horoOdometro.length > 12 ) {
		elementHoroOdometro.classList.add("is-invalid");
		elementPadre = elementHoroOdometro.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("El campo Horómetro / Odómetro debe ser máximo de 10 dígitos.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		errores = true;
	}

	if ( parseFloat(litros) < 0.01 ) {
		elementLitros.classList.add("is-invalid");
		elementPadre = elementLitros.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("El valor del campo Litros no puede ser menor a 0.1.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		errores = true;
	} else if ( litros.length > 7 ) {
		elementLitros.classList.add("is-invalid");
		elementPadre = elementLitros.parentElement;
		newDiv = document.createElement("div");
		newDiv.classList.add("invalid-feedback");
  		newContent = document.createTextNode("El campo Litros debe ser máximo de 6 dígitos.");
	 	newDiv.appendChild(newContent); //añade texto al div creado.
		elementPadre.appendChild(newDiv);

		errores = true;
	}

	if ( errores ) return;

	$(elementMaquinariaId).val(null).trigger('change');
	$(elementUbicacionId).val(null).trigger('change');
	$(elementOperadorId).val(null).trigger('change');
	elementHoroOdometro.value = '0.00';
	elementLitros.value = '0.00';
	elementObservaciones.value = '';

	let tableCombustibleDetalles = document.querySelector('#tablaCombustibleDetalles tbody');
	let elementRow = `<tr class="font-italic" maquinariaId="${maquinariaId}" nuevo>
						<td class="text-uppercase">
							${maquinariaText}<input type="hidden" name="detalles[maquinariaId][]" value="${maquinariaId}">
							<button type='button' class='btn btn-xs btn-danger ml-1 float-right eliminar'>
								<i class='far fa-times-circle'></i>
							</button>
						</td>
						<td class="text-uppercase">${ubicacionText}<input type="hidden" name="detalles[ubicacionActualId][]" value="${ubicacionActualId}"><input type="hidden" name="detalles[ubicacionId][]" value="${ubicacionId}"></td>
						<td class="text-uppercase">${operadorText}<input type="hidden" name="detalles[empleadoId][]" value="${operadorId}"></td>
						<td class="text-right">${horoOdometro}<input type="hidden" name="detalles[horoOdometro][]" value="${horoOdometro}"></td>
						<td class="text-right">${litros}<input type="hidden" name="detalles[litros][]" value="${litros}"></td>
						<td class="text-right">${observaciones}<input type="hidden" name="detalles[observaciones][]" value="${observaciones}"></td>
					</tr>`;

	$(tableCombustibleDetalles).append(elementRow);

}

let btnAgregarCarga = document.getElementById("btnAgregarCarga");
if ( btnAgregarCarga != null ) btnAgregarCarga.addEventListener("click", agregarCarga);

// Eliminar la carga agregada (creando o editando)
$('#tablaCombustibleDetalles').on("click", "button.eliminar", function (e) {
	this.parentElement.parentElement.remove();
});

// Eliminar la partida (editando)
$('#tablaCombustibleDetalles').on("click", "i.eliminarPartida", function (e) {

	let detalleId = $(this).attr("detalleId");
	let elementInput = `<input type="hidden" name="partidasEliminadas[]" value="${detalleId}">`;
	$('#tablaCombustibleDetalles').parent().parent().append(elementInput);

	this.parentElement.parentElement.remove();

});
