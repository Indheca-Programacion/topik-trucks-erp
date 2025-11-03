$(function(){

	let parametrosTableList = { responsive: true };
	let elementmodalAsignarPuesto = document.querySelector('#modalAsignarPuesto');
	let elementmodalAsignarAlmacen = document.querySelector('#modalAsignarAlmacen');

    /*=============================================
    MOSTRAR TABLAS
    =============================================*/

    // TABLA VENDIDORES

    let tableList = document.getElementById('tablaVendedores');
    // LLamar a la funcion fAjaxDataTable() para llenar el Listado  
    if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/VendedorAjax.php', '#tablaVendedores');

    // Confirmar la eliminación del Vendedor
    // $("table tbody").on("click", "button.eliminar", function (e) {
    $(tableList).on("click", "button.eliminar", function (e) {

        e.preventDefault();
        var folio = $(this).attr("folio");
        var form = $(this).parents('form');

        Swal.fire({
        title: '¿Estás Seguro de querer eliminar este Usuario (Usuario: '+folio+') ?',
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

    formulario.submit(); // Enviar los datos
    }

    let formulario = document.getElementById("formSend");
    let mensaje = document.getElementById("msgSend");
    let btnEnviar = document.getElementById("btnSend");
    if ( btnEnviar != null ) btnEnviar.addEventListener("click", enviar);

    // Activar el elemento Select2
    $('.select2').select2({
        tags: false
    });



});
