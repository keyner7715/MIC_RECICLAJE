// Validación en tiempo real para el campo Teléfono en técnicos
const telefonoInput = document.getElementById('telefono');

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

if (telefonoInput) {
    telefonoInput.addEventListener('input', function(e) {
        // Solo permitir números y máximo 10 dígitos
        let val = e.target.value.replace(/[^0-9]/g, '');
        if (val.length > 10) {
            val = val.slice(0, 10);
        }
        e.target.value = val;
        if (val.length !== 10 && val.length > 0) {
            showError(e.target, 'telefono_error', 'El teléfono debe tener exactamente 10 dígitos');
        } else {
            hideError(e.target, 'telefono_error');
        }
    });
}

const form = document.querySelector('form[method="POST"]');
if (form) {
    form.addEventListener('submit', function(e) {
        let hasErrors = false;
        if (telefonoInput && (telefonoInput.value.length !== 10)) {
            showError(telefonoInput, 'telefono_error', 'El teléfono debe tener exactamente 10 dígitos');
            hasErrors = true;
        }
        if (hasErrors) {
            e.preventDefault();
            alert('Por favor, corrija los errores en el formulario');
        }
    });
}
