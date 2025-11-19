$(function(){

	let tableList = document.getElementById('tablaCheckListMaquinarias');

	// LLamar a la funcion fAjaxDataTable() para llenar el Listado
	if ( tableList != null ) fAjaxDataTable(rutaAjax+'app/Ajax/ChecklistMaquinariaAjax.php', '#tablaCheckListMaquinarias');

	// Confirmar la eliminación del Almacen
	$(tableList).on("click", "button.eliminar", function (e) {

	    e.preventDefault();
	    var folio = $(this).attr("folio");
	    var form = $(this).parents('form');

	    Swal.fire({
			title: '¿Estás Seguro de querer eliminar este Almacén (Descripción: '+folio+') ?',
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

    $('.select2').select2({
        tags: false,
        width: '100%'
        // theme: 'bootstrap4'
    });

    $('.input-group.date').datetimepicker({
        format: 'DD/MMMM/YYYY'
    });
	
    const stepDescription = document.getElementById('stepDescription');
    const optionsContainer = document.getElementById('optionsContainer');
    const observationsContainer = document.getElementById('observationsContainer'); // Nuevo
    const prevStepBtn = document.getElementById('prevStepBtn');
    const nextStepBtn = document.getElementById('nextStepBtn');
    const progressBar = document.getElementById('progressBar');
    const resultsContainer = document.getElementById('resultsContainer');
    const selectionList = document.getElementById('selectionList');
    const resetBtn = document.getElementById('resetBtn');
    const backToChecklistBtn = document.getElementById('backToChecklistBtn'); // ¡Nuevo!

    if (backToChecklistBtn  !== null) {
        // let allSteps = [];
        let currentStepIndex = 0;
        // userSelections ahora guardará un objeto por cada "característica" y también la observación
        // Ejemplo: { 'step1': { 'backend-complexity': 'B', 'observations': 'Algunas notas aquí' }, 'step2': {...} }
        let userSelections = {};

        // Función para guardar el estado actual en localStorage
        const saveProgress = () => {
                try {
                    // Guardar el id de maquinaria actual
                    localStorage.setItem('currentMachineId', $('#maquinariaId').val());
                    // Guardar el índice del paso actual
                    localStorage.setItem('currentStepIndex', currentStepIndex.toString());
                    // Guardar las selecciones del usuario (convertir a JSON string)
                    localStorage.setItem('userSelections', JSON.stringify(userSelections));
                } catch (e) {
                    console.error('Error al guardar en localStorage:', e);
                }
        };

        const loadProgress = () => {
            try {
                const svaedMachineId = localStorage.getItem('currentMachineId');
                
                if (svaedMachineId == $('#maquinariaId').val()) {
                    const savedStepIndex = localStorage.getItem('currentStepIndex');
                    const savedSelections = localStorage.getItem('userSelections');

                    if (savedStepIndex !== null) {
                        currentStepIndex = parseInt(savedStepIndex, 10);
                    }
                    if (savedSelections !== null) {
                        userSelections = JSON.parse(savedSelections);
                    }
                }
            } catch (e) {
                console.error('Error al cargar desde localStorage:', e);
                // Si hay un error al cargar, reiniciar a valores por defecto
                currentStepIndex = 0;
                userSelections = {};
                // Limpiar localStorage para evitar futuros errores con datos corruptos
                localStorage.removeItem('currentStepIndex');
                localStorage.removeItem('userSelections');
            }
        };

        const loadSteps = async () => {
            try {
                loadProgress(); // ¡Cargar el progreso ANTES de renderizar el paso!
                renderStep(); // Renderizar el primer paso una vez cargados los datos (o el paso guardado)
            } catch (error) {
                console.error('Error al cargar los pasos:', error);
                stepDescription.textContent = 'Error al cargar la configuración. Intente de nuevo más tarde.';
                nextStepBtn.disabled = true;
            }
        };

        // Función para renderizar el paso actual
        const renderStep = () => {
            if (allSteps.length === 0) {
                return;
            }

            resultsContainer.classList.add('d-none'); // Ocultar resultados si estaban visibles

            const currentStep = allSteps[currentStepIndex];
            stepDescription.textContent = currentStep.descripcion;
            optionsContainer.innerHTML = ''; // Limpiar opciones anteriores
            observationsContainer.innerHTML = ''; // Limpiar campo de observaciones anterior

            // Asegurarse de que el objeto de selecciones para el paso actual exista
            if (!userSelections[currentStep.id]) {
                userSelections[currentStep.id] = {};
            }

            currentStep.opciones.forEach(optionToEvaluate => {
                const colDiv = document.createElement('div');
                colDiv.className = 'col-md-12 mb-4';

                const card = document.createElement('div');
                card.className = 'card shadow-sm h-100 p-3';

                const featureTitle = document.createElement('h6');
                featureTitle.className = 'card-title mb-3';
                featureTitle.textContent = optionToEvaluate.texto;

                const classificationButtons = document.createElement('div');
                classificationButtons.className = 'd-flex gap-2 justify-content-center';

                optionToEvaluate.clasificaciones.forEach(classification => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-outline-primary btn-sm';
                    button.textContent = classification.texto;
                    button.dataset.featureId = optionToEvaluate.id;
                    button.dataset.value = classification.valor;

                    // Marcar el botón seleccionado si ya existe una selección para esta característica
                    if (userSelections[currentStep.id][optionToEvaluate.id] === classification.valor) {
                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-primary');
                    }

                    button.addEventListener('click', () => {
                        classificationButtons.querySelectorAll('button').forEach(btn => {
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-outline-primary');
                        });
                        button.classList.remove('btn-outline-primary');
                        button.classList.add('btn-primary');

                        userSelections[currentStep.id][optionToEvaluate.id] = classification.valor;

                        checkAllOptionsSelectedAndObservations(); // Verificar todo
                    });
                    classificationButtons.appendChild(button);
                });

                card.appendChild(featureTitle);
                card.appendChild(classificationButtons);
                colDiv.appendChild(card);
                optionsContainer.appendChild(colDiv);
            });

            // --- Lógica para el campo de observaciones ---
            if (currentStep.tiene_observaciones) {
                const formFloatingDiv = document.createElement('div');
                formFloatingDiv.className = 'form-floating mt-4';

                const textarea = document.createElement('textarea');
                textarea.className = 'form-control text-uppercase';
                textarea.id = `observations-${currentStep.id}`;
                textarea.placeholder = 'Escribe tus observaciones aquí...';
                textarea.style.height = '100px'; // Ajusta la altura del textarea
                textarea.value = userSelections[currentStep.id]['observaciones'] || ''; // Cargar observaciones guardadas

                const label = document.createElement('label');
                label.htmlFor = `observations-${currentStep.id}`;
                label.textContent = 'Observaciones (opcional)';

                textarea.addEventListener('input', () => {
                    userSelections[currentStep.id]['observaciones'] = textarea.value;
                    checkAllOptionsSelectedAndObservations(); // Revalidar al escribir
                });

                formFloatingDiv.appendChild(textarea);
                formFloatingDiv.appendChild(label);
                observationsContainer.appendChild(formFloatingDiv);
            }

            checkAllOptionsSelectedAndObservations(); // Verificar el estado inicial del botón Siguiente
            updateNavigationButtons();
            updateProgressBar();
        };

        // Nueva función para verificar si todas las opciones y (opcionalmente) las observaciones están completas
        const checkAllOptionsSelectedAndObservations = () => {
            const currentStep = allSteps[currentStepIndex];
            let allOptionsSelected = true;

            currentStep.opciones.forEach(optionToEvaluate => {
                if (!userSelections[currentStep.id] || !userSelections[currentStep.id][optionToEvaluate.id]) {
                    allOptionsSelected = false;
                }
            });

            let observationsComplete = true;
            if (currentStep.tiene_observaciones) {
                // Si tiene observaciones, se considera "completo" si el campo no está vacío
                // Si quieres que sea OPCIONAL, solo verifica que el objeto exista (no que tenga valor)
                // Para hacerlo OPCIONAL, simplemente lo marcamos como true si el campo existe, no si tiene texto
                // Esto permite que el usuario deje el campo de texto vacío
                observationsComplete = true; // Por defecto es true, ya que es opcional.
            }

            nextStepBtn.disabled = !(allOptionsSelected && observationsComplete);
        };

        // Función para actualizar el estado de los botones de navegación
        const updateNavigationButtons = () => {
            prevStepBtn.disabled = currentStepIndex === 0;

            if (currentStepIndex === allSteps.length - 1) {
                nextStepBtn.textContent = 'Finalizar';
            } else {
                nextStepBtn.textContent = 'Siguiente';
            }
        };

        // Función para actualizar la barra de progreso
        const updateProgressBar = () => {
            if (allSteps.length === 0) {
                progressBar.style.width = '0%';
                progressBar.setAttribute('aria-valuenow', 0);
                return;
            }
            const progress = ((currentStepIndex + 1) / allSteps.length) * 100;
            progressBar.style.width = `${progress}%`;
            progressBar.setAttribute('aria-valuenow', progress);
        };

        // Función para mostrar los resultados finales
        const showResults = () => {
            document.querySelector('.card.shadow.p-4').classList.add('d-none');
            resultsContainer.classList.remove('d-none');
            selectionList.innerHTML = '';

            for (const stepId in userSelections) {
                const stepSelections = userSelections[stepId];
                const currentStep = allSteps.find(step => step.id == parseInt(stepId));

                if (currentStep) {
                    const stepHeader = document.createElement('li');
                    stepHeader.className = 'list-group-item active';
                    stepHeader.textContent = currentStep.descripcion;
                    selectionList.appendChild(stepHeader);

                    for (const featureId in stepSelections) {
                        // Si la clave es 'observaciones', la manejamos por separado
                        if (featureId === 'observaciones') {
                            if (stepSelections[featureId]) { // Solo si hay texto
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item list-group-item-light';
                                listItem.innerHTML = `<strong>Observaciones:</strong> <br>${stepSelections[featureId].toUpperCase().replace(/\n/g, '<br>')}`;
                                selectionList.appendChild(listItem);
                            }
                        } else {
                            let selectedValue = stepSelections[featureId];
                            if (selectedValue == 1) {
                                selectedValue = 'Bueno';
                            } else if (selectedValue == 2) {
                                selectedValue = 'Malo / No operativo';
                            } else if (selectedValue == 0) {
                                selectedValue = 'No aplica';
                            }

                            const originalOption = currentStep.opciones.find(opt => opt.id == parseInt(featureId));
                            if (originalOption) {
                                const listItem = document.createElement('li');
                                listItem.className = 'list-group-item';
                                listItem.textContent = `${originalOption.texto}: ${selectedValue}`;
                                selectionList.appendChild(listItem);
                            }
                        }
                    }
                }
            }


        };

        // --- Manejo de eventos de botones ---
        nextStepBtn.addEventListener('click', () => {
            if (currentStepIndex < allSteps.length - 1) {
                currentStepIndex++;
                renderStep();
                saveProgress(); // ¡Añade esta línea!
            } else {
                showResults();
            }
        });

        prevStepBtn.addEventListener('click', () => {
            if (currentStepIndex > 0) {
                currentStepIndex--;
                renderStep();
                saveProgress(); // ¡Añade esta línea!
            }
        });

        resetBtn.addEventListener('click', () => {
            currentStepIndex = 0;
            userSelections = {};
            document.querySelector('.card.shadow.p-4').classList.remove('d-none');
            resultsContainer.classList.add('d-none');
            renderStep();
        });

        // ¡Nuevo! Event listener para el botón "Volver al Checklist"
        backToChecklistBtn.addEventListener('click', () => {
            location.reload(); // Carga los pasos desde el principio
        });

        loadSteps();

        // --- Guardar progreso al cerrar la pestaña ---
        $('#finishBtn').on('click', function() {

            // Deshabilitar el botón para evitar envíos múltiples
            const finishBtn = $(this);
            finishBtn.prop('disabled', true);

            let datos = new FormData();
            datos.append('accion', 'guardarChecklistMaquinaria');
            datos.append('data', JSON.stringify(userSelections));
            datos.append('_token', $('[name="_token"]').val());
            datos.append('checklistMaquinaria', $('#checklistMaquinaria').val());

            $.ajax({
                url: rutaAjax + 'app/Ajax/ChecklistMaquinariaAjax.php',
                type: 'POST',
                data: datos,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json'
            }).done(function(response) {
                // Si la respuesta es string, intenta parsear a JSON
                let res = response;
                if (typeof response === 'string') {
                    try {
                        res = JSON.parse(response);
                    } catch (e) {
                        res = { error: true, errorMessage: 'Respuesta inválida del servidor.' };
                    }
                }

                if (res.error === false) {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: res.respuestaMessage || 'Progreso guardado correctamente.'
                    });
                    //Eliminar los datos de progreso guardados en localStorage
                    localStorage.removeItem('currentStepIndex');
                    localStorage.removeItem('userSelections');
                    localStorage.removeItem('currentMachineId');
                    location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: res.errorMessage || 'Ocurrió un error al guardar el progreso.'
                    });
                }
            }).fail(function(xhr, status, error) {
                console.error('Error al guardar el progreso:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'No se pudo guardar el progreso. Intente nuevamente.'
                });
            }).always(function() {
                // Rehabilitar el botón después de la petición
                finishBtn.prop('disabled', false);
            });
        });

    }

    const sliderCombInicial = document.getElementById('combustibleInicial');
    const combInicialOutput = document.getElementById('combInicialValue');

    const sliderCombFinal = document.getElementById('combustibleFinal');
    const combFinalOutput = document.getElementById('combFinalValue');

    if (sliderCombInicial == null) return;
    // Inicializa el valor mostrado con el valor actual del slider
    combInicialOutput.innerHTML = sliderCombInicial.value;

    // Actualiza el valor cada vez que el slider cambia
    sliderCombInicial.oninput = function() {
        combInicialOutput.innerHTML = this.value+" %";
    }

    // Inicializa el valor mostrado con el valor actual del slider
    combFinalOutput.innerHTML = sliderCombFinal.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderCombFinal.oninput = function() {
        combFinalOutput.innerHTML = this.value+" %";
    }

    // Inicializa el valor mostrado con el valor actual del slider
    const sliderAceiteMotor = document.getElementById('acMotor');
    const acMotorValueOutput = document.getElementById('acMotorValue');

    if (sliderAceiteMotor == null) return;
    acMotorValueOutput.innerHTML = sliderAceiteMotor.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderAceiteMotor.oninput = function() {
        acMotorValueOutput.innerHTML = this.value+" %";
    }

    // Inicializa el valor mostrado con el valor actual del slider
    const sliderAceiteHidraulico = document.getElementById('acHidraulico');
    const acHidraulicoValueOutput = document.getElementById('acHidraulicoValue');
    if (sliderAceiteHidraulico == null) return;
    acHidraulicoValueOutput.innerHTML = sliderAceiteHidraulico.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderAceiteHidraulico.oninput = function() {
        acHidraulicoValueOutput.innerHTML = this.value+" %";
    }

    // Inicializa el valor mostrado con el valor actual del slider
    const sliderAceiteTransmision = document.getElementById('acTransmision');
    const acTransmisionValueOutput = document.getElementById('acTransmisionValue');
    if (sliderAceiteTransmision == null) return;
    acTransmisionValueOutput.innerHTML = sliderAceiteTransmision.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderAceiteTransmision.oninput = function() {
        acTransmisionValueOutput.innerHTML = this.value+" %";
    }

    // Inicializa el valor mostrado con el valor actual del slider
    const sliderAnticongelante = document.getElementById('anticongelante');
    const anticongelanteValueOutput = document.getElementById('anticongelanteValue');
    if (sliderAnticongelante == null) return;
    anticongelanteValueOutput.innerHTML = sliderAnticongelante.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderAnticongelante.oninput = function() {
        anticongelanteValueOutput.innerHTML = this.value+" %";
    }

    // Inicializa el valor mostrado con el valor actual del slider
    const sliderAcMalacatePrinc = document.getElementById('acMalacatePrinc');
    const acMalacatePrincValueOutput = document.getElementById('acMalacatePrincValue');
    if (sliderAcMalacatePrinc == null) return;
    acMalacatePrincValueOutput.innerHTML = sliderAcMalacatePrinc.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderAcMalacatePrinc.oninput = function() {
        acMalacatePrincValueOutput.innerHTML = this.value+" %";
    }
    
    // Inicializa el valor mostrado con el valor actual del slider
    const sliderAcMalacateAux = document.getElementById('acMalacateAux');
    const acMalacateAuxValueOutput = document.getElementById('acMalacateAuxValue');
    if (sliderAcMalacateAux == null) return;
    acMalacateAuxValueOutput.innerHTML = sliderAcMalacateAux.value;
    // Actualiza el valor cada vez que el slider cambia
    sliderAcMalacateAux.oninput = function() {
        acMalacateAuxValueOutput.innerHTML = this.value+" %";
    }

    $('#btnAutorizar').on('click', function() {
        // Deshabilitar el botón para evitar envíos múltiples
        const authBtn = $(this);
        authBtn.prop('disabled', true);
        const auth = $(this).attr('auth');

        let datos = new FormData();
        datos.append('accion', 'autorizarCheckList');
        datos.append('checklistMaquinaria', $('#checklistMaquinaria').val());
        datos.append('auth', auth);
        datos.append('_token', $('input[name="_token"]').val());

        $.ajax({
            url: rutaAjax + 'app/Ajax/ChecklistMaquinariaAjax.php',
            type: 'POST',
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json'
        }).done(function(response) {
            // Si la respuesta es string, intenta parsear a JSON
            let res = response;
            if (typeof response === 'string') {
                try {
                    res = JSON.parse(response);
                } catch (e) {
                    res = { error: true, errorMessage: 'Respuesta inválida del servidor.' };
                }
            }

            if (res.error === false) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Éxito!',
                    text: res.respuestaMessage || 'Checklist autorizado correctamente.'
                });
                location.reload();
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: res.errorMessage || 'Ocurrió un error al autorizar el checklist.'
                });
            }
        }).fail(function(xhr, status, error) {
            console.error('Error al autorizar el checklist:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo autorizar el checklist. Intente nuevamente.'
            });
        }).always(function() {
            // Rehabilitar el botón después de la petición
            authBtn.prop('disabled', false);
        });
    });
});