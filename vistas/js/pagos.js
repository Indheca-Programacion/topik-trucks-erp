$(function(){

    let parametrosTableList = { responsive: false };

    let tablaRequisiciones= $("#tablaRequisiciones").DataTable();
    let tablaOrdenes= $("#tablaOrdenes").DataTable();

    $(".select2").select2({
        language: "es",
        tags: false,
        width: "100%",
        // theme: 'bootstrap4'
    });


    /*======================================================
	Abrir el input al presionar el botón Cargar Cotizaciones
	======================================================*/
    $("#btnSubirCotizaciones").click(function () {
        document.getElementById("cotizacionArchivos").click();
    });

    /*================================================
    Validar tipo y tamaño de los archivos Cotizaciones
    ================================================*/
    $("#cotizacionArchivos").change(function () {
        // $("div.subir-cotizaciones span.lista-archivos").html('');
        let archivos = this.files;
        if (archivos.length == 0) return;

        let error = false;

        for (let i = 0; i < archivos.length; i++) {
        let archivo = archivos[i];

        /*==========================================
                VALIDAMOS QUE EL FORMATO DEL ARCHIVO SEA PDF
                ==========================================*/

        if (archivo["type"] != "application/pdf") {
            error = true;

            // $("#cotizacionArchivos").val("");
            // $("div.subir-cotizaciones span.lista-archivos").html('');

            Swal.fire({
            title: "Error en el tipo de archivo",
            text: '¡El archivo "' + archivo["name"] + '" debe ser PDF!',
            icon: "error",
            confirmButtonText: "¡Cerrar!",
            });

            return false;
        } else if (archivo["size"] > 4000000) {
            error = true;

            // $("#cotizacionArchivos").val("");
            // $("div.subir-cotizaciones span.lista-archivos").html('');

            Swal.fire({
            title: "Error en el tamaño del archivo",
            text:
                '¡El archivo "' + archivo["name"] + '" no debe pesar más de 4MB!',
            icon: "error",
            confirmButtonText: "¡Cerrar!",
            });

            return false;
        }
        // else {

        // $("div.subir-cotizaciones span.lista-archivos").append('<p class="font-italic text-info mb-0 text-right">'+archivo["name"]+'</p>');

        // }
        }

        if (error) {
        $("#cotizacionArchivos").val("");

        return;
        }

        for (let i = 0; i < archivos.length; i++) {
        let archivo = archivos[i];

        $("div.subir-cotizaciones span.lista-archivos").append(
            '<p class="font-italic text-info mb-0 text-right">' +
            archivo["name"] +
            "</p>"
        );
        }

        let cloneElementArchivos = this.cloneNode(true);
        cloneElementArchivos.removeAttribute("id");
        cloneElementArchivos.name = "cotizacionArchivos[]";
        $("div.subir-cotizaciones").append(cloneElementArchivos);
    }); // $("#cotizacionArchivos").change(function(){

    $('#btnSiguiente').on('click', function () {
        
        let step1 = document.getElementById("formulario-step-1");
        let step2 = document.getElementById("formulario-step-2");
        let step3 = document.getElementById("formulario-step-3");

        let step1Trigger = document.getElementById("stepper1trigger1");
        let step2Trigger = document.getElementById("stepper1trigger2");
        let step3Trigger = document.getElementById("stepper1trigger3");

        let btnAnterior = document.getElementById("btnAnterior");
        let btnSiguiente = document.getElementById("btnSiguiente");
        let btnSubirPagos = document.getElementById("btnSubirPagos");

        if (step2.classList.contains("d-none")) {
            step1.classList.add("d-none");
            step2.classList.remove("d-none");
            step3.classList.add("d-none");

            btnSiguiente.setAttribute("disabled", "true");
            btnAnterior.classList.remove("d-none");

            step2Trigger.classList.add("active");
        } else if (step3.classList.contains("d-none")) {
            step1.classList.add("d-none");
            step2.classList.add("d-none");
            step3.classList.remove("d-none");

            btnSubirPagos.classList.remove("d-none");
            btnSiguiente.classList.add("d-none");

            step3Trigger.classList.add("active");

            obtenerTodasLasRequisicionesSeleccionadas();
        }

        
    });

    $('#btnAnterior').on('click', function () {
        
        let step1 = document.getElementById("formulario-step-1");
        let step2 = document.getElementById("formulario-step-2");
        let step3 = document.getElementById("formulario-step-3");

        let step1Trigger = document.getElementById("stepper1trigger1");
        let step2Trigger = document.getElementById("stepper1trigger2");
        let step3Trigger = document.getElementById("stepper1trigger3");

        let btnSiguiente = document.getElementById("btnSiguiente");
        let btnAnterior = document.getElementById("btnAnterior");
        let btnSubirPagos = document.getElementById("btnSubirPagos");

        if (step2.classList.contains("d-none") ) {
            step2.classList.remove("d-none");
            step3.classList.add("d-none");

            btnSubirPagos.classList.add("d-none");
            btnSiguiente.classList.remove("d-none");

            step1Trigger.classList.add("active");
            step2Trigger.classList.remove("active");
        } else {
            step1.classList.remove("d-none");
            step2.classList.add("d-none");
            btnSiguiente.removeAttribute("disabled");

            step2Trigger.classList.remove("active");
            step3Trigger.classList.remove("active");
        }


        // Ocultar el botón anterior si estamos en el step 1
        if (!step1.classList.contains("d-none")) {
            btnAnterior.classList.add("d-none");
        }
    });

    $(document).on('change', '[id^="pagoArchivo"]', function(){
        $(this).closest('tr').find('select').removeAttr('disabled');
        $('#btnSiguiente').removeAttr('disabled');
    });

    $(document).on('change', '[id^="pagoArchivoOrden_"]', function(){
        $(this).closest('tr').find('select').removeAttr('disabled');
        $('#btnSiguiente').removeAttr('disabled');
    });

    /*=============================================
    Modal para ver la requisición
    =============================================*/

    $('#modalVerRequisicion').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let folio = button.data('id');
        let iframe = $(this).find('iframe');
        iframe.attr('src', rutaAjax + '/requisiciones/' + folio+"/imprimir");
    });

    $('#modalVerRequisicion').on('hide.bs.modal', function (event) {
        let iframe = $(this).find('iframe');
        iframe.attr('src', '');
    });

    /*=============================================
    Modal para ver la orden de compra
    =============================================*/

    $('#modalVerOrdenes').on('show.bs.modal', function (event) {
        let button = $(event.relatedTarget);
        let folio = button.data('id');
        let iframe = $(this).find('iframe');
        iframe.attr('src', rutaAjax + '/orden-compra/' + folio+"/imprimir");
    });

    $('#modalVerOrdenes').on('hide.bs.modal', function (event) {
        let iframe = $(this).find('iframe');
        iframe.attr('src', '');
    });

    let datosSeleccionados = [];

    function obtenerTodasLasRequisicionesSeleccionadas() {
        datosSeleccionados = [];
        if ($('#categoria').val() == 1) {
            let tablaRequisiciones = $("#tablaRequisiciones").DataTable();
            tablaRequisiciones.rows().every(function() {
                let rowNode = this.node();
                let fileInput = $(rowNode).find('input[type="file"]')[0];
                let selectInput = $(rowNode).find('select')[0];
                let folio = $(rowNode).find('td').eq(1);

                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                let btn = $(rowNode).find('button[data-id]');
                let dataId = btn.length ? btn.data('id') : null;
                datosSeleccionados.push({
                    file: fileInput.files[0],
                    select: selectInput ? selectInput.value : null,
                    dataId: dataId,
                    folio: folio.text()
                });
                }
            });
        } else {
            let tablaOrdenes = $("#tablaOrdenes").DataTable();
            tablaOrdenes.rows().every(function() {
                let rowNode = this.node();
                let fileInput = $(rowNode).find('input[type="file"]')[0];
                let selectInput = $(rowNode).find('select')[0];
                let folio = $(rowNode).find('td').eq(1);

                if (fileInput && fileInput.files && fileInput.files.length > 0) {
                    let btn = $(rowNode).find('button[data-id]');
                    let dataId = btn.length ? btn.data('id') : null;
                    datosSeleccionados.push({
                        file: fileInput.files[0],
                        select: selectInput ? selectInput.value : null,
                        dataId: dataId,
                        folio: folio.text()
                    });
                }
            });
        }

        let resumenHtml = '<ul class="list-group">';
        datosSeleccionados.forEach(item => {
            resumenHtml += `<li class="list-group-item d-flex justify-content-between align-items-center">
                Folio: <b>${item.folio}</b> - Archivo: <b>${item.file.name}</b>
            </li>`;
        });
        console.log(datosSeleccionados);
        resumenHtml += '</ul>';
        $('#resumenRequisicion').html(resumenHtml);
    }

    $('#btnSubirPagos').on('click', function () {
        if (datosSeleccionados.length === 0) return;
        let categoria = $('#categoria').val();
        let formData = new FormData();
        formData.append('accion', 'subirPagos');
        formData.append('categoria', categoria);
        datosSeleccionados.forEach((item, idx) => {
            formData.append(`files[${idx}]`, item.file);
            formData.append(`estatus[${idx}]`, item.select);
            formData.append(`requisicionId[${idx}]`, item.dataId);
        });

        // Desactivar el botón y mostrar loader
        let $btn = $('#btnSubirPagos');
        $btn.attr('disabled', true);
        let originalHtml = $btn.html();
        $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Subiendo...');

        $.ajax({
            url: rutaAjax + 'app/Ajax/PagosAjax.php', // Cambia la URL por la tuya
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
            // Restaurar botón
            $btn.removeAttr('disabled');
            $btn.html(originalHtml);

            if (response.error) {
                Swal.fire({
                title: "Error",
                text: response.error,
                icon: "error",
                confirmButtonText: "¡Cerrar!"
                });
            } else {
                let folios = response.requisiciones.map(r => r[1]).join(', ');
                Swal.fire({
                title: "Pagos subidos",
                html: `Los pagos se han subido correctamente.<br>Requisiciones modificadas: <b>${folios}</b>`,
                icon: "success",
                confirmButtonText: "¡Cerrar!"
                }).then(() => {
                window.location.reload();
                });
            }
            },
            error: function() {
            // Restaurar botón
            $btn.removeAttr('disabled');
            $btn.html(originalHtml);

            Swal.fire({
                title: "Error",
                text: "Hubo un problema al subir los pagos.",
                icon: "error",
                confirmButtonText: "¡Cerrar!"
            });
            }
        });
    });

    $('#categoria').on('change', function() {
        if (this.value == 1) {
            $('#tablaOrdenes_wrapper').addClass('d-none');
            $('#tablaRequisiciones_wrapper').removeClass('d-none');
        } else {
            $('#tablaOrdenes_wrapper').removeClass('d-none');
            $('#tablaRequisiciones_wrapper').addClass('d-none');
        }
    });
});