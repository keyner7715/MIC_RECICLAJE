// Validación en tiempo real para Año y Precio Diario
const anioInput = document.getElementById('año');
const precioInput = document.getElementById('precio_diario');

function showError(input, errorId, message) {
    let errorDiv = document.getElementById(errorId);
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.id = errorId;
        errorDiv.className = 'error-message';
        errorDiv.style.color = 'red';
        errorDiv.style.fontSize = '0.9em';
        errorDiv.style.marginTop = '5px';
        input.parentNode.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
    input.classList.add('input-error');
}

function hideError(input, errorId) {
    const errorDiv = document.getElementById(errorId);
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
    input.classList.remove('input-error');
}

if (anioInput) {
    anioInput.addEventListener('input', function(e) {
        // Solo permitir números mayores a 0 y sin ceros a la izquierda
        let val = e.target.value.replace(/[^0-9]/g, '');
        // Eliminar ceros a la izquierda
        val = val.replace(/^0+/, '');
        e.target.value = val;
        if (val !== '' && parseInt(val) <= 0) {
            showError(e.target, 'anio_error', 'El año debe ser mayor a 0');
        } else if (val !== '' && val.length > 0 && val.length < 4) {
            showError(e.target, 'anio_error', 'El año debe tener al menos 4 dígitos');
        } else {
            hideError(e.target, 'anio_error');
        }
    });
    // Evitar que el usuario escriba 0 como primer dígito
    anioInput.addEventListener('keydown', function(e) {
        if (e.key === '0' && (!e.target.value || e.target.selectionStart === 0)) {
            e.preventDefault();
        }
    });
}

if (precioInput) {
    precioInput.addEventListener('input', function(e) {
        // Solo permitir números mayores a 0 y sin ceros a la izquierda
        let val = e.target.value.replace(/[^0-9]/g, '');
        // Eliminar ceros a la izquierda
        val = val.replace(/^0+/, '');
        e.target.value = val;
        if (val !== '' && parseInt(val) <= 0) {
            showError(e.target, 'precio_error', 'El precio debe ser mayor a 0');
        } else {
            hideError(e.target, 'precio_error');
        }
    });
    // Evitar que el usuario escriba 0 como primer dígito
    precioInput.addEventListener('keydown', function(e) {
        if (e.key === '0' && (!e.target.value || e.target.selectionStart === 0)) {
            e.preventDefault();
        }
    });
}

const form = document.querySelector('form[method="POST"]');
if (form) {
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        if (anioInput && (anioInput.value === '' || isNaN(anioInput.value) || parseInt(anioInput.value) <= 0)) {
            showError(anioInput, 'anio_error', 'El año debe ser mayor a 0');
            hasErrors = true;
        } else if (anioInput && anioInput.value.length > 0 && anioInput.value.length < 4) {
            showError(anioInput, 'anio_error', 'El año debe tener al menos 4 dígitos');
            hasErrors = true;
        }
        if (precioInput && (precioInput.value === '' || isNaN(precioInput.value) || parseInt(precioInput.value) <= 0)) {
            showError(precioInput, 'precio_error', 'El precio debe ser mayor a 0');
            hasErrors = true;
        }
        if (hasErrors) {
            e.preventDefault();
            alert('Por favor, corrija los errores en el formulario');
        }
    });
}
