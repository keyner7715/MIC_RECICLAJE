// Validación en tiempo real para teléfono
const telefonoInput = document.getElementById('telefono');
const telefonoError = document.getElementById('telefono_error');
if (telefonoInput) {
    telefonoInput.addEventListener('input', function(e) {
        const telefono = e.target.value;
        // Solo permitir números
        e.target.value = telefono.replace(/[^0-9]/g, '');
        if (e.target.value.length > 10) {
            e.target.value = e.target.value.substring(0, 10);
        }
        // Validaciones
        if (!/^[0-9]*$/.test(e.target.value)) {
            telefonoError.textContent = 'Solo se permiten números';
            telefonoError.style.display = 'block';
            e.target.classList.add('input-error');
        } else if (e.target.value.length > 10) {
            telefonoError.textContent = 'Máximo 10 dígitos';
            telefonoError.style.display = 'block';
            e.target.classList.add('input-error');
        } else {
            telefonoError.style.display = 'none';
            e.target.classList.remove('input-error');
        }
    });
}

// Validación en tiempo real para RUC/Cédula
const rucInput = document.getElementById('ruc_cedula');
const rucError = document.getElementById('ruc_error');
if (rucInput) {
    rucInput.addEventListener('input', function(e) {
        const rucCedula = e.target.value;
        // Solo permitir números
        e.target.value = rucCedula.replace(/[^0-9]/g, '');
        if (e.target.value.length > 13) {
            e.target.value = e.target.value.substring(0, 13);
        }
        // Validaciones
        if (!/^[0-9]*$/.test(e.target.value)) {
            rucError.textContent = 'Solo se permiten números';
            rucError.style.display = 'block';
            e.target.classList.add('input-error');
        } else if (e.target.value.length > 0 && !(e.target.value.length === 10 || e.target.value.length === 13)) {
            rucError.textContent = 'Debe tener exactamente 10 o 13 dígitos';
            rucError.style.display = 'block';
            e.target.classList.add('input-error');
        } else {
            rucError.style.display = 'none';
            e.target.classList.remove('input-error');
        }
    });
}

// Validación antes de enviar el formulario
const clienteForm = document.getElementById('clienteForm');
if (clienteForm) {
    clienteForm.addEventListener('submit', function(e) {
        const telefono = telefonoInput.value;
        const rucCedula = rucInput.value;
        let hasErrors = false;
        // Validar teléfono
        if (!/^[0-9]+$/.test(telefono) || telefono.length > 10) {
            telefonoError.textContent = 'Teléfono: Solo números, máximo 10 dígitos';
            telefonoError.style.display = 'block';
            telefonoInput.classList.add('input-error');
            hasErrors = true;
        }
        // Validar RUC/Cédula
        if (!/^[0-9]+$/.test(rucCedula) || !(rucCedula.length === 10 || rucCedula.length === 13)) {
            rucError.textContent = 'RUC/Cédula: Solo números, exactamente 10 o 13 dígitos';
            rucError.style.display = 'block';
            rucInput.classList.add('input-error');
            hasErrors = true;
        }
        if (hasErrors) {
            e.preventDefault();
            alert('Por favor, corrija los errores en el formulario');
        }
    });
}
