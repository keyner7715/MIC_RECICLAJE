// Validaciones para el formulario de proveedores
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('proveedorForm');
    const telefonoInput = document.getElementById('telefono');
    const telefonoError = document.getElementById('telefono_error');

    // Validación de teléfono en tiempo real
    telefonoInput.addEventListener('input', function() {
        let value = this.value.replace(/[^0-9]/g, '');
        this.value = value;
        
        // Limitar a 10 dígitos
        if (value.length > 10) {
            this.value = value.slice(0, 10);
            value = this.value;
        }

        // Mostrar errores
        if (value.length > 0 && value.length < 10) {
            this.classList.add('input-error');
            telefonoError.textContent = `Faltan ${10 - value.length} dígitos`;
            telefonoError.style.display = 'block';
        } else if (value.length === 10) {
            this.classList.remove('input-error');
            telefonoError.style.display = 'none';
        } else {
            this.classList.remove('input-error');
            telefonoError.style.display = 'none';
        }
    });

    // Validación al enviar el formulario
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        // Validar teléfono si no está vacío
        const telefono = telefonoInput.value.trim();
        if (telefono && telefono.length !== 10) {
            e.preventDefault();
            telefonoInput.classList.add('input-error');
            telefonoError.textContent = 'El teléfono debe tener exactamente 10 dígitos';
            telefonoError.style.display = 'block';
            isValid = false;
        }

        if (!isValid) {
            alert('Por favor corrija los errores en el formulario');
        }
    });

    // Limpiar errores al hacer focus
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.classList.remove('input-error');
        });
    });
});
