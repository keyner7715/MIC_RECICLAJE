// Validación en tiempo real para el campo Costo de Mantenimiento
const costoInput = document.getElementById('costo_mantenimiento');

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

if (costoInput) {
    costoInput.addEventListener('input', function(e) {
        // Solo permitir números y punto decimal
        let val = e.target.value.replace(/[^0-9.]/g, '');
        // Solo un punto decimal permitido
        val = val.replace(/(\..*)\./g, '$1');
        e.target.value = val;
        if (val !== '' && (isNaN(val) || parseFloat(val) <= 0)) {
            showError(e.target, 'costo_error', 'El costo debe ser mayor a 0');
        } else {
            hideError(e.target, 'costo_error');
        }
    });
}

const form = document.querySelector('form[method="POST"]');
if (form) {
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        if (costoInput && (costoInput.value === '' || isNaN(costoInput.value) || parseFloat(costoInput.value) <= 0)) {
            showError(costoInput, 'costo_error', 'El costo debe ser mayor a 0');
            hasErrors = true;
        }
        if (hasErrors) {
            e.preventDefault();
            alert('Por favor, corrija los errores en el formulario');
        }
    });
}
